<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$message = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    if (!empty($content)) {
        $query = mysqli_query($conn, "
            INSERT INTO global_posts (id_user, content, created_at)
            VALUES ($id_user, '$content', NOW())
        ");
        
        if ($query) {
            header("Location: ../dashboard/index.php?posted=1");
            exit();
        } else {
            $message = "Gagal membuat postingan!";
        }
    } else {
        $message = "Konten tidak boleh kosong!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Postingan Global</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <div class="nav">
    <a class="btn" href="../dashboard/index.php">← Kembali</a>
  </div>

  <h2><i class="fa-solid fa-pen" style="color:var(--primary); margin-right:10px;"></i>Buat Postingan Global</h2>
  <p class="text-muted">Bagikan pemikiran atau pengumuman ke seluruh kampus</p>

  <?php if ($message): ?>
    <div class="alert alert-error"><?= $message; ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="POST">
      <label>Apa yang ingin kamu bagikan?</label>
      <textarea name="content" rows="5" placeholder="Tulis sesuatu..." required maxlength="500"></textarea>
      <small class="text-muted">Maksimal 500 karakter</small>

      <div class="form-actions">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Posting Sekarang</button>
      </div>
    </form>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
