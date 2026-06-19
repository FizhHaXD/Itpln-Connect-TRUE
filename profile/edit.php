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
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <h2><i class="fa-solid fa-pen" style="color:var(--primary); margin-right:10px;"></i>Edit Profil</h2>

  <div class="card">
    <form action="update.php" method="POST" enctype="multipart/form-data">

      <label>Nama</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required>

      <label>Email</label>
      <input type="email" value="<?= htmlspecialchars($user['email']); ?>" disabled>
      <small class="text-muted">Email tidak bisa diubah</small>

      <label>No HP</label>
      <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp'] ?? ''); ?>" placeholder="08123456789">

      <label>Bio</label>
      <textarea name="bio" rows="4" placeholder="Ceritakan tentang dirimu..."><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>

      <label>Foto Profil</label>
      <input type="file" name="foto" accept="image/*" class="input-file">
      <small class="text-muted">Format: JPG, PNG. Maks 2MB</small>

      <?php if (!empty($user['foto'])): ?>
        <p class="text-muted">Foto saat ini: <b><?= htmlspecialchars($user['foto']); ?></b></p>
      <?php endif; ?>

      <div class="form-actions">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
        <a href="index.php" class="btn">Batal</a>
      </div>
    </form>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
