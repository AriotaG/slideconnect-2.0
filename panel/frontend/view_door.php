<?php
session_start();
require_once '../includes/functions.php';
require_once '../config/config.php'; // Includi il file di configurazione

if (!isLoggedIn() || ($_SESSION['user']['role'] != 'superadmin' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.html");
    exit();
}

$door_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($door_id <= 0) {
    die("Invalid door ID");
}

$door = getDoorById($door_id);
$schedule = getScheduleByDoorId($door_id);

// Recupera il nome della stanza dal database
$room_name = $door['room_name']; // Assumendo che il nome della stanza sia memorizzato nel campo 'room_name' della tabella 'door'
$streaming_url = DOMAIN . "/join?room=" . urlencode($room_name) . "&name=" . $door_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Door</title>
  <!-- Include AdminLTE CSS -->
  <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
  <!-- Include FullCalendar CSS -->
  <link rel="stylesheet" href="/adminlte/plugins/fullcalendar/main.min.css">
  <link rel="stylesheet" href="/adminlte/plugins/fullcalendar-daygrid/main.min.css">
  <link rel="stylesheet" href="/adminlte/plugins/fullcalendar-timegrid/main.min.css">
  <link rel="stylesheet" href="/adminlte/plugins/fullcalendar-bootstrap/main.min.css">
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
                    <p id="streamingUrl" style="font-size: 10px;"><?php echo $streaming_url; ?></p>
                  </li>
                </ul>
                <script>
                function copyToClipboard() {
                  var url = document.getElementById("streamingUrl").innerText;
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
                  <li class="nav-item"><a class="nav-link active" href="#connected_door" data-toggle="tab">Porta Collegata</a></li>
                  <li class="nav-item"><a class="nav-link" href="#schedule" data-toggle="tab">Palinsesto</a></li>
                  <li class="nav-item"><a class="nav-link" href="#calendar" data-toggle="tab">Calendario</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="connected_door">
                    <div class="text-center">
                      <h4><?php echo htmlspecialchars($door['connected_door_name']); ?></h4>
                      <!-- Player video per la porta collegata -->
                      <video width="640" height="480" controls autoplay>
                        <source src="<?php echo "/panel" . htmlspecialchars($door['connected_ad_video_url']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                      </video>
                    </div>
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="schedule">
                    <h4>Palinsesto</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Titolo</th>
                                <th>Descrizione</th>
                                <th>Ora di Inizio</th>
                                <th>Ora di Fine</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule as $program): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($program['title']); ?></td>
                                    <td><?php echo htmlspecialchars($program['description']); ?></td>
                                    <td><?php echo htmlspecialchars($program['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($program['end_time']); ?></td>
                                    <td>
                                        <a href="edit_program.php?id=<?php echo $program['id']; ?>" class="btn btn-primary btn-sm">Modifica</a>
                                        <a href="delete_program.php?id=<?php echo $program['id']; ?>" class="btn btn-danger btn-sm">Elimina</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <div class="video-container">
                                            <?php
                                            $videos = json_decode($program['videos'], true);
                                            foreach ($videos as $video): ?>
                                                <video width="320" height="240" controls loop>
                                                    <source src="<?php echo htmlspecialchars($video); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="add_program.php?door_id=<?php echo $door_id; ?>" class="btn btn-success">Aggiungi Programma</a>
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="calendar">
                    <h4>Calendario</h4>
                    <div id="calendar"></div>
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
<!-- Include FullCalendar JS -->
<script src="/adminlte/plugins/fullcalendar/main.min.js"></script>
<script src="/adminlte/plugins/fullcalendar-daygrid/main.min.js"></script>
<script src="/adminlte/plugins/fullcalendar-timegrid/main.min.js"></script>
<script src="/adminlte/plugins/fullcalendar-interaction/main.min.js"></script>
<script src="/adminlte/plugins/fullcalendar-bootstrap/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid', 'timeGrid', 'interaction', 'bootstrap'],
        themeSystem: 'bootstrap',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            <?php foreach ($schedule as $program): ?>
            {
                title: '<?php echo htmlspecialchars($program['title']); ?>',
                start: '<?php echo $program['start_time']; ?>',
                end: '<?php echo $program['end_time']; ?>',
                color: '#ADD8E6' // Colore azzurrino tenue
            },
            <?php endforeach; ?>
        ]
    });
    calendar.render();
});
</script>
</body>
</html>