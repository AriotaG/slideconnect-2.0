<?php
$user = $_SESSION['user'];
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'default.png';
?>

<!-- Sidebar user panel (optional) -->
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
  <div class="image">
    <img src="/panel/uploads/profile_images/<?php echo $profile_image; ?>" class="img-circle elevation-2" alt="User Image">
  </div>
  <div class="info">
    <a href="#" class="d-block"><?php echo $user['name']; ?></a>
  </div>
</div>

<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
      <a href="../frontend/dashboard.php" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
          Dashboard
        </p>
      </a>
    </li>
    <?php if ($user['role'] == 'superadmin' || $user['role'] == 'admin'): ?>
    <li class="nav-item">
      <a href="../frontend/create_user.php" class="nav-link">
        <i class="nav-icon fas fa-user-plus"></i>
        <p>
          Create User
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="../frontend/add_door.php" class="nav-link">
        <i class="nav-icon fas fa-door-open"></i>
        <p>
          Create Door
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="../frontend/manage_users.php" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
        <p>
          Manage Users
        </p>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a href="../frontend/manage_doors.php" class="nav-link">
        <i class="nav-icon fas fa-door-open"></i>
        <p>
          Manage Doors
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="../frontend/user_profile.php" class="nav-link">
        <i class="nav-icon fas fa-user"></i>
        <p>
          User Profile
        </p>
      </a>
    </li>
  </ul>
</nav>
<!-- /.sidebar-menu -->