<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

if (!isLoggedIn() || ($_SESSION['user']['role'] != 'superadmin' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.html");
    exit();
}

$conn = connectDB();

// Recupera le porte esistenti
$doors = [];
$doors_stmt = $conn->prepare("SELECT id, name FROM doors");
if (!$doors_stmt) {
    die("Prepare failed: " . $conn->error);
}
$doors_stmt->execute();
$doors_result = $doors_stmt->get_result();
while ($row = $doors_result->fetch_assoc()) {
    $doors[] = $row;
}

// Recupera gli utenti esistenti
$users = [];
$users_stmt = $conn->prepare("SELECT id, username FROM users");
if (!$users_stmt) {
    die("Prepare failed: " . $conn->error);
}
$users_stmt->execute();
$users_result = $users_stmt->get_result();
while ($row = $users_result->fetch_assoc()) {
    $users[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $connected_door_id = $_POST['connected_door_id'] ? intval($_POST['connected_door_id']) : null;
        $user_id = $_POST['user_id'] ? intval($_POST['user_id']) : null;
        $subtext = $_POST['subtext'];
        $room_name = $_POST['room_name'];
        $location = $_POST['location'];
        $on_air = isset($_POST['on_air']) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO doors (name, description, connected_door_id, user_id, subtext, room_name, location, on_air) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssiiissi", $name, $description, $connected_door_id, $user_id, $subtext, $room_name, $location, $on_air);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $door_id = $stmt->insert_id;

        // Creazione delle cartelle per i video
        $door_dir = "../uploads/doors/$door_id";
        $ad_video_dir = "$door_dir/ad_video";
        $inactivity_video_dir = "$door_dir/inactivity_video";

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
        $ad_video_url = '';
        $inactivity_video_url = '';

        if (isset($_FILES['ad_video']) && $_FILES['ad_video']['error'] == UPLOAD_ERR_OK) {
            $ad_video_path = $ad_video_dir . '/' . basename($_FILES['ad_video']['name']);
            if (move_uploaded_file($_FILES['ad_video']['tmp_name'], $ad_video_path)) {
                $ad_video_url = "/uploads/doors/$door_id/ad_video/" . basename($_FILES['ad_video']['name']);
            } else {
                throw new Exception("Failed to move uploaded file: " . $_FILES['ad_video']['name']);
            }
        }

        if (isset($_FILES['inactivity_video']) && $_FILES['inactivity_video']['error'] == UPLOAD_ERR_OK) {
            $inactivity_video_path = $inactivity_video_dir . '/' . basename($_FILES['inactivity_video']['name']);
            if (move_uploaded_file($_FILES['inactivity_video']['tmp_name'], $inactivity_video_path)) {
                $inactivity_video_url = "/uploads/doors/$door_id/inactivity_video/" . basename($_FILES['inactivity_video']['name']);
            } else {
                throw new Exception("Failed to move uploaded file: " . $_FILES['inactivity_video']['name']);
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

        $_SESSION['message'] = "Door added successfully!";
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
  <title>Add Door</title>
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
            <h1 class="m-0">Add Door</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Add your content here -->
        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success">
            <?php
              echo $_SESSION['message'];
              unset($_SESSION['message']);
            ?>
          </div>
        <?php endif; ?>
        <form action="add_door.php" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
          </div>
          <div class="form-group">
            <label for="connected_door_id">Connected Door</label>
            <select class="form-control" id="connected_door_id" name="connected_door_id">
              <option value="">Select a connected door</option>
              <?php foreach ($doors as $door): ?>
                <option value="<?php echo $door['id']; ?>"><?php echo htmlspecialchars($door['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="user_id">User</label>
            <select class="form-control" id="user_id" name="user_id">
              <option value="">Select a user</option>
              <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="subtext">Subtext</label>
            <input type="text" class="form-control" id="subtext" name="subtext">
          </div>
          <div class="form-group">
            <label for="room_name">Room Name</label>
            <input type="text" class="form-control" id="room_name" name="room_name">
          </div>
          <div class="form-group">
            <label for="location">Location</label>
            <input type="text" class="form-control" id="location" name="location">
          </div>
          <div class="form-group">
            <label for="on_air">On Air</label>
            <input type="checkbox" id="on_air" name="on_air">
          </div>
          <div class="form-group">
            <label for="ad_video">Ad Video</label>
            <input type="file" class="form-control" id="ad_video" name="ad_video" accept="video/*">
          </div>
          <div class="form-group">
            <label for="inactivity_video">Inactivity Video</label>
            <input type="file" class="form-control" id="inactivity_video" name="inactivity_video" accept="video/*">
          </div>
          <button type="submit" class="btn btn-primary">Add Door</button>
        </form>
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