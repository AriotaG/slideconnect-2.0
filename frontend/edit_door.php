<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || ($_SESSION['user']['role'] != 'superadmin' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.html");
    exit();
}

$door_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($door_id <= 0) {
    die("Invalid door ID");
}

$door = getDoorById($door_id);
$doors = getAllDoorsExcept($door_id);
$users = getAllUsers();

$ad_video_dir = "../uploads/doors/$door_id/ad_video";
$inactivity_video_dir = "../uploads/doors/$door_id/inactivity_video";

$ad_videos = getVideoFiles($ad_video_dir);
$inactivity_videos = getVideoFiles($inactivity_video_dir);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = !empty($_POST['name']) ? $_POST['name'] : $door['name'];
        $description = !empty($_POST['description']) ? $_POST['description'] : $door['description'];
        $location = !empty($_POST['location']) ? $_POST['location'] : $door['location'];
        $connected_door_id = isset($_POST['connected_door_id']) && $_POST['connected_door_id'] != '' ? intval($_POST['connected_door_id']) : null;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : $door['user_id'];
        $subtext = !empty($_POST['subtext']) ? $_POST['subtext'] : $door['subtext'];
        $room_name = !empty($_POST['room_name']) ? $_POST['room_name'] : $door['room_name'];

        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE doors SET name = ?, description = ?, location = ?, connected_door_id = ?, user_id = ?, subtext = ?, room_name = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssiiisi", $name, $description, $location, $connected_door_id, $user_id, $subtext, $room_name, $door_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Creazione delle cartelle per i video
        if (!is_dir($ad_video_dir)) {
            if (!mkdir($ad_video_dir, 0777, true)) {
                throw new Exception("Failed to create directory: $ad_video_dir");
            }
        }
        if (!is_dir($inactivity_video_dir)) {
            if (!mkdir($inactivity_video_dir, 0777, true)) {
                throw new Exception("Failed to create directory: $inactivity_video_dir");
            }
        }

        // Caricamento dei file video
        $allowed_types = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv'];
        $max_size = 60 * 1024 * 1024; // 60MB

        $ad_video_url = $door['ad_video_url'];
        $inactivity_video_url = $door['inactivity_video_url'];

        if (isset($_FILES['ad_video']) && $_FILES['ad_video']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['ad_video'];
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception("Invalid file type: " . $file['type']);
            }
            if ($file['size'] > $max_size) {
                throw new Exception("File size exceeds limit: " . $file['size']);
            }

            $ad_video_path = $ad_video_dir . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $ad_video_path)) {
                $ad_video_url = "/panel/uploads/doors/$door_id/ad_video/" . basename($file['name']);
            } else {
                throw new Exception("Failed to move uploaded file: " . $file['name']);
            }
        }

        if (isset($_FILES['inactivity_video']) && $_FILES['inactivity_video']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['inactivity_video'];
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception("Invalid file type: " . $file['type']);
            }
            if ($file['size'] > $max_size) {
                throw new Exception("File size exceeds limit: " . $file['size']);
            }

            $inactivity_video_path = $inactivity_video_dir . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $inactivity_video_path)) {
                $inactivity_video_url = "/panel/uploads/doors/$door_id/inactivity_video/" . basename($file['name']);
            } else {
                throw new Exception("Failed to move uploaded file: " . $file['name']);
            }
        }

        // Aggiornamento della tabella doors con gli URL dei video
        $stmt = $conn->prepare("UPDATE doors SET ad_video_url = ?, inactivity_video_url = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssi", $ad_video_url, $inactivity_video_url, $door_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $_SESSION['message'] = "Door updated successfully!";
        header("Location: manage_doors.php");
        exit();
    } catch (Exception $e) {
        error_log($e->getMessage());
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Door</title>
  <!-- Include AdminLTE CSS -->
  <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="dashboard.php" class="nav-link">Home</a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="logout.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <img src="/adminlte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <?php require_once '../includes/menu.php'; ?>
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo htmlspecialchars($door['name']); ?></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <!-- Player di default per lo streaming -->
                  <video class="img-fluid" controls>
                    <source src="path_to_streaming_video" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
                </div>

                <h3 class="profile-username text-center"><?php echo htmlspecialchars($door['name']); ?></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Location</b> <a class="float-right"><?php echo htmlspecialchars($door['location']); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Description</b> <a class="float-right"><?php echo htmlspecialchars($door['description']); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>User</b> <a class="float-right"><?php echo htmlspecialchars($door['user_name']); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Streaming URL</b>
                    <button id="copyButton" class="btn btn-primary float-right" onclick="copyToClipboard()">Copy URL</button>
                  </li>
                </ul>
                <script>
                function copyToClipboard() {
                  var roomName = document.getElementById("room_name").value;
                  var doorId = "<?php echo $door_id; ?>";
                  var url = "https://demo.increative.it/join?room=" + roomName + "&name=" + doorId;
                  navigator.clipboard.writeText(url).then(function() {
                    alert("URL copiato: " + url);
                  }, function(err) {
                    alert("Errore nella copia del testo: ", err);
                  });
                }
                </script>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                  <li class="nav-item"><a class="nav-link" href="#media" data-toggle="tab">Media</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="settings">
                    <form class="form-horizontal" action="edit_door.php?id=<?php echo $door_id; ?>" method="post" enctype="multipart/form-data">
                      <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($door['name']); ?>" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($door['description']); ?>" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label">Location</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($door['location']); ?>" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="connected_door_id" class="col-sm-2 col-form-label">Connected Door</label>
                        <div class="col-sm-10">
                          <select class="form-control" id="connected_door_id" name="connected_door_id">
                            <option value="">Select a connected door</option>
                            <?php foreach ($doors as $d): ?>
                              <option value="<?php echo $d['id']; ?>" <?php if ($d['id'] == $door['connected_door_id']) echo 'selected'; ?>><?php echo htmlspecialchars($d['name']); ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_id" class="col-sm-2 col-form-label">User</label>
                        <div class="col-sm-10">
                          <select class="form-control" id="user_id" name="user_id">
                            <option value="">Select a user</option>
                            <?php foreach ($users as $user): ?>
                              <option value="<?php echo $user['id']; ?>" <?php if ($user['id'] == $door['user_id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['username']); ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="subtext" class="col-sm-2 col-form-label">Subtext</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="subtext" name="subtext" value="<?php echo htmlspecialchars($door['subtext']); ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="room_name" class="col-sm-2 col-form-label">Room Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="room_name" name="room_name" value="<?php echo htmlspecialchars($door['room_name']); ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-primary">Update Door</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="media">
                    <form class="form-horizontal" action="edit_door.php?id=<?php echo $door_id; ?>" method="post" enctype="multipart/form-data">
                      <div class="form-group row">
                        <label for="ad_video" class="col-sm-2 col-form-label">Ad Video</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="ad_video" name="ad_video" accept="video/*">
                          <?php if ($door['ad_video_url']): ?>
                            <p>Current Ad Video: <a href="<?php echo "/panel" . $door['ad_video_url']; ?>" target="_blank">View</a></p>
                          <?php endif; ?>
                          <div class="row mt-2">
                            <?php foreach ($ad_videos as $video): ?>
                              <div class="col-sm-3">
                                <video class="img-fluid" controls>
                                  <source src="<?php echo "/panel/uploads/doors/$door_id/ad_video/$video"; ?>" type="video/mp4">
                                  Your browser does not support the video tag.
                                </video>
                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inactivity_video" class="col-sm-2 col-form-label">Inactivity Video</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="inactivity_video" name="inactivity_video" accept="video/*">
                          <?php if ($door['inactivity_video_url']): ?>
                            <p>Current Inactivity Video: <a href="<?php echo "/panel" . $door['inactivity_video_url']; ?>" target="_blank">View</a></p>
                          <?php endif; ?>
                          <div class="row mt-2">
                            <?php foreach ($inactivity_videos as $video): ?>
                              <div class="col-sm-3">
                                <video class="img-fluid" controls>
                                  <source src="<?php echo "/panel/uploads/doors/$door_id/inactivity_video/$video"; ?>" type="video/mp4">
                                  Your browser does not support the video tag.
                                </video>
                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-primary">Update Media</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

<!-- Main Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      SlideConnect 2.0 WebRTC
    </div>
    <strong>Copyright &copy; 2024 <a href="https://increative.it">inCreative Developer</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- Include AdminLTE JS -->
<script src="/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>