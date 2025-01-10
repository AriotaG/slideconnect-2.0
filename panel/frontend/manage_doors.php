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
$doors_stmt = $conn->prepare("SELECT d.id, d.name, d.room_name, d.location, d.on_air, u.username AS owner, c.name AS connected_door_name
                              FROM doors d
                              LEFT JOIN users u ON d.user_id = u.id
                              LEFT JOIN doors c ON d.connected_door_id = c.id");
$doors_stmt->execute();
$doors_result = $doors_stmt->get_result();
while ($row = $doors_result->fetch_assoc()) {
    $doors[] = $row;
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM doors WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $delete_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $_SESSION['message'] = "Door deleted successfully!";
    header("Location: manage_doors.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Doors</title>
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
            <h1 class="m-0">Manage Doors</h1>
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
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID Porta</th>
              <th>Nome Porta</th>
              <th>Porta Corrispondente</th>
              <th>Nome Stanza</th>
              <th>Proprietario Porta</th>
              <th>Location</th>
              <th>On Air</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($doors as $door): ?>
            <tr>
              <td><?php echo $door['id']; ?></td>
              <td><?php echo htmlspecialchars($door['name']); ?></td>
              <td><?php echo htmlspecialchars($door['connected_door_name']); ?></td>
              <td><?php echo htmlspecialchars($door['room_name']); ?></td>
              <td><?php echo htmlspecialchars($door['owner']); ?></td>
              <td><?php echo htmlspecialchars($door['location']); ?></td>
              <td>
                <?php if ($door['on_air']): ?>
                  <i class="nav-icon far fa-circle text-danger"></i>
                <?php else: ?>
                  <i class="nav-icon far fa-circle text-info"></i>
                <?php endif; ?>
              </td>
              <td>
                <a href="edit_door.php?id=<?php echo $door['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="manage_doors.php?delete_id=<?php echo $door['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this door?');">Delete</a>
                <a href="view_door.php?id=<?php echo $door['id']; ?>" class="btn btn-success btn-sm">View</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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