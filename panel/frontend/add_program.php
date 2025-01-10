<?php
session_start();
require_once '../includes/functions.php';
require_once '../config/config.php'; // Includi il file di configurazione

if (!isLoggedIn() || ($_SESSION['user']['role'] != 'superadmin' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.html");
    exit();
}

$door_id = isset($_GET['door_id']) ? intval($_GET['door_id']) : 0;
if ($door_id <= 0) {
    die("Invalid door ID");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $videos = [];

    // Gestione del caricamento dei video
    $upload_dir = "../uploads/doors/$door_id/ad_video/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['videos']['name'][$key]);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $videos[] = "/panel/uploads/doors/$door_id/ad_video/" . $file_name;
        }
    }

    $videos_json = json_encode($videos);

    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO schedule (door_id, title, description, start_time, end_time, videos) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("isssss", $door_id, $title, $description, $start_time, $end_time, $videos_json);
    if ($stmt->execute()) {
        header("Location: view_door.php?id=$door_id");
        exit();
    } else {
        die("Execute failed: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aggiungi Programma</title>
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
            <h1 class="m-0">Aggiungi Programma</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Dettagli Programma</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="add_program.php?door_id=<?php echo $door_id; ?>" method="post" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label for="title">Titolo</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                  </div>
                  <div class="form-group">
                    <label for="description">Descrizione</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="start_time">Ora di Inizio</label>
                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                  </div>
                  <div class="form-group">
                    <label for="end_time">Ora di Fine</label>
                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                  </div>
                  <div class="form-group">
                    <label for="videos">Video</label>
                    <input type="file" class="form-control" id="videos" name="videos[]" multiple>
                    <div id="video-thumbnails" class="mt-2"></div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
              </form>
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
<script>
document.getElementById('videos').addEventListener('change', function(event) {
    var files = event.target.files;
    var thumbnailsContainer = document.getElementById('video-thumbnails');
    thumbnailsContainer.innerHTML = '';

    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var videoElement = document.createElement('video');
        videoElement.src = URL.createObjectURL(file);
        videoElement.width = 100;
        videoElement.controls = true;
        thumbnailsContainer.appendChild(videoElement);
    }
});
</script>
</body>
</html>