<?php
session_start();
require_once "../config/database.php";

$id_event = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get event details
$event = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT e.*, c.nama_community, c.id_community
    FROM events e
    JOIN communities c ON e.id_community = c.id_community
    WHERE e.id_event = $id_event
"));

if (!$event) {
    header("Location: index.php");
    exit();
}

$id_user = $_SESSION['id_user'] ?? null;

// Check if user already joined event
$hasJoined = false;
if ($id_user) {
    $check = mysqli_query($conn, "
        SELECT * FROM event_participants 
        WHERE id_user=$id_user AND id_event=$id_event
    ");
    $hasJoined = mysqli_num_rows($check) > 0;
}

$foto = !empty($event['photo']) ? $event['photo'] : 'default_event.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($event['nama_event']); ?> - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php 
if ($id_user) {
  include "../includes/navbar.php";
} else {
  include "../includes/navbar_guest.php";
}
?>

<main class="main-content">
<div class="container">
  <a href="index.php" class="btn btn-sm">← Kembali</a>

  <?php if (isset($_GET['joined'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Berhasil ikut event!</div>
  <?php endif; ?>

  <?php if (isset($_GET['already'])): ?>
    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Kamu sudah terdaftar di event ini.</div>
  <?php endif; ?>

  <!-- Event Banner -->
  <div class="event-banner">
    <img src="../uploads/events/<?= htmlspecialchars($foto); ?>" class="banner-img" alt="<?= htmlspecialchars($event['nama_event']); ?>">
  </div>

  <!-- Event Info -->
  <div class="card">
    <span class="badge"><?= htmlspecialchars($event['nama_community']); ?></span>
    
    <h1><?= htmlspecialchars($event['nama_event']); ?></h1>
    
    <div class="event-info">
      <p><i class="fa-solid fa-calendar"></i> <b>Tanggal:</b> <?= date('d M Y', strtotime($event['tanggal'])); ?></p>
      <p><i class="fa-solid fa-clock"></i> <b>Waktu:</b> <?= substr($event['jam'], 0, 5); ?> WIB</p>
      <p><i class="fa-solid fa-location-dot"></i> <b>Lokasi:</b> <?= htmlspecialchars($event['lokasi']); ?></p>
      <?php if ($event['visibility'] == 'internal'): ?>
        <p><i class="fa-solid fa-lock"></i> <b>Tipe:</b> Internal (Hanya untuk member komunitas)</p>
      <?php endif; ?>
    </div>

    <hr>

    <h3><i class="fa-solid fa-align-left" style="color:var(--primary); margin-right:8px;"></i>Deskripsi</h3>
    <p class="intro"><?= nl2br(htmlspecialchars($event['deskripsi'])); ?></p>

    <hr>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <?php if (!$id_user): ?>
        <!-- Guest: needs to login -->
        <a href="../auth/login.php?need_login=1&return=<?= urlencode('events/detail.php?id=' . $id_event); ?>" class="btn btn-success">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk untuk Ikut Event
        </a>
      <?php elseif ($hasJoined): ?>
        <!-- Already joined -->
        <span class="badge badge-success badge-lg"><i class="fa-solid fa-check"></i> Sudah Terdaftar</span>
      <?php else: ?>
        <!-- Can join -->
        <a href="join.php?id=<?= $id_event; ?>" class="btn btn-success"><i class="fa-solid fa-calendar-check"></i> Ikut Event</a>
      <?php endif; ?>

      <a href="../communities/detail.php?id=<?= $event['id_community']; ?>" class="btn">
        <i class="fa-solid fa-users"></i> Lihat Komunitas
      </a>
    </div>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
