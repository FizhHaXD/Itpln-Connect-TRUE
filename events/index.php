<?php
session_start();
include "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;

// Ambil semua event (tidak filter tanggal, show all)
$events = mysqli_query($conn, "
  SELECT e.*, c.nama_community 
  FROM events e 
  JOIN communities c ON e.id_community = c.id_community
  ORDER BY e.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Kampus - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php 
// Use guest navbar if not logged in, otherwise use regular navbar
if ($id_user) {
  include "../includes/navbar.php";
} else {
  include "../includes/navbar_guest.php";
}
?>

<main class="main-content">
<div class="container">
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Event berhasil dibuat!</div>
  <?php endif; ?>

  <div class="page-header">
    <h2><i class="fa-solid fa-calendar-days" style="color:var(--primary); margin-right:10px;"></i>Event Kampus</h2>
    <?php if ($id_user): ?>
      <a href="create.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Buat Event</a>
    <?php endif; ?>
  </div>

  <?php if (mysqli_num_rows($events) == 0): ?>
    <p class="empty-message">Belum ada event. 
      <?php if ($id_user): ?>
        <a href="create.php">Buat event pertama!</a>
      <?php else: ?>
        <a href="../auth/login.php">Login</a> untuk membuat event.
      <?php endif; ?>
    </p>
  <?php else: ?>
    <div class="grid">
      <?php while ($e = mysqli_fetch_assoc($events)): ?>
        <?php
          // Cek apakah user sudah ikut (hanya jika logged in)
          $joined = false;
          if ($id_user) {
            $cek = mysqli_query($conn,
              "SELECT * FROM event_participants 
               WHERE id_user=$id_user AND id_event=".$e['id_event']
            );
            $joined = mysqli_num_rows($cek) > 0;
          }

          // Cek visibility - skip internal events untuk guest
          if ($e['visibility'] == 'internal' && $id_user) {
            $member = mysqli_query($conn,
              "SELECT * FROM community_members 
               WHERE id_user=$id_user AND id_community=".$e['id_community']
            );
            if (mysqli_num_rows($member) == 0) continue;
          } elseif ($e['visibility'] == 'internal' && !$id_user) {
            // Guest tidak bisa lihat internal events
            continue;
          }
        ?>

        <a href="detail.php?id=<?= $e['id_event']; ?>" class="card event-card" style="text-decoration: none; color: inherit;">
          <div class="event-header">
            <span class="badge"><?= htmlspecialchars($e['nama_community']); ?></span>
            <?php if ($e['visibility'] == 'internal'): ?>
              <span class="badge badge-secondary"><i class="fa-solid fa-lock" style="font-size:0.7rem;"></i> Internal</span>
            <?php endif; ?>
          </div>

          <h3><?= htmlspecialchars($e['nama_event']); ?></h3>
          
          <div class="event-info">
            <p><i class="fa-solid fa-calendar"></i> <?= date('d M Y', strtotime($e['tanggal'])); ?></p>
            <p><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($e['jam']); ?></p>
            <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($e['lokasi']); ?></p>
          </div>

          <p class="event-desc"><?= htmlspecialchars(substr($e['deskripsi'], 0, 100)); ?>...</p>

          <div class="event-actions">
            <span class="btn btn-sm" style="pointer-events:none;">Lihat Detail <i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
