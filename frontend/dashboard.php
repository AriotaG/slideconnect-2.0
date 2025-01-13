<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || ($_SESSION['user']['role'] != 'superadmin' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
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
      <img src="https://increative.it/wp-content/uploads/2021/07/increative-simbol-light.png" alt="InCreative Logo" class="brand-image elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">SlideConnect 2.0</span>
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
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Add your content here -->
        <p>Welcome to the SlideConnect 2.0</p>
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
	<img src="https://increative.it/wp-content/uploads/2021/06/increative_logo_light.png" alt="InCreative Logo" class="brand-image" style="opacity: 0.7;max-width: 100px;height: auto;">
    <strong style="font-size: 0.7vw;">Copyright &copy; 2025 <a href="https://increative.it">InCreative.it</a>. </strong><span style="font-size: 0.7vw;">All rights reserved.</span>
  </footer>
</div>
<!-- ./wrapper -->

<!-- Include AdminLTE JS -->
<script src="/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>