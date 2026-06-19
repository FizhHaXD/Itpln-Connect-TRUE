<?php
session_start();
include "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;

// ambil semua komunitas
$communities = mysqli_query($conn, "SELECT * FROM communities ORDER BY id_community DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Komunitas - ITPLN Connect</title>
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
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Komunitas berhasil dibuat!</div>
  <?php endif; ?>

  <div class="page-header">
    <h2><i class="fa-solid fa-users" style="color:var(--primary); margin-right:10px;"></i>Komunitas Kampus</h2>
    <?php if ($id_user): ?>
      <a class="btn btn-success" href="create.php"><i class="fa-solid fa-plus"></i> Buat Komunitas</a>
    <?php endif; ?>
  </div>

  <div class="grid">
    <?php while ($c = mysqli_fetch_assoc($communities)) : ?>
      <?php
        // cek apakah user sudah join (hanya jika logged in)
        $joined = false;
        if ($id_user) {
          $check = mysqli_query($conn,
            "SELECT * FROM community_members 
             WHERE id_user=$id_user AND id_community=".$c['id_community']
          );
          $joined = mysqli_num_rows($check) > 0;
        }
        
        // set foto default jika kosong
        $foto = !empty($c['foto']) ? $c['foto'] : 'default_community.png';
      ?>

      <a href="detail.php?id=<?= $c['id_community']; ?>" class="card-komunitas">
        <img src="../uploads/communities/<?= htmlspecialchars($foto); ?>" class="card-img" alt="<?= htmlspecialchars($c['nama_community']); ?>">
        
        <div class="card-body">
          <span class="badge"><?= htmlspecialchars($c['kategori']); ?></span>
          <h3><?= htmlspecialchars($c['nama_community']); ?></h3>
          <p><?= htmlspecialchars($c['deskripsi']); ?></p>

          <?php if ($joined): ?>
            <span class="badge badge-success"><i class="fa-solid fa-check"></i> Sudah Join</span>
          <?php else: ?>
            <span class="btn btn-sm" style="pointer-events:none;">Lihat Detail <i class="fa-solid fa-arrow-right"></i></span>
          <?php endif; ?>
        </div>
      </a>
    <?php endwhile; ?>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
