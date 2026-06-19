<?php
require_once "../config/database.php";
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM users WHERE id_user=$id_user
"));

$foto = !empty($user['foto']) ? $user['foto'] : 'default.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Profil berhasil diperbarui!</div>
  <?php endif; ?>

  <h2><i class="fa-solid fa-user" style="color:var(--primary); margin-right:10px;"></i>Profil Saya</h2>

  <div class="profile-card">
    <img src="../uploads/profiles/<?= htmlspecialchars($foto); ?>" class="profile-img" alt="Profile">

    <h3><?= htmlspecialchars($user['nama']); ?></h3>
    <p class="text-muted"><?= htmlspecialchars($user['email']); ?></p>

    <hr>

    <p><b>No HP:</b> <?= htmlspecialchars($user['no_hp'] ?? '-'); ?></p>
    <p><b>Bio:</b><br><?= nl2br(htmlspecialchars($user['bio'] ?? 'Belum ada bio')); ?></p>

    <hr>

    <a href="edit.php" class="btn btn-success"><i class="fa-solid fa-pen"></i> Edit Profil</a>
    <a href="../dashboard/index.php" class="btn"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
