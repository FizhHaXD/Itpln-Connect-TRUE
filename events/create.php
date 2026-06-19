<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_community = (int)$_POST['id_community'];
    $nama_event = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $visibility = mysqli_real_escape_string($conn, $_POST['visibility']);
    
    // Handle photo upload
    $photo = "default_event.png";
    
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = "event_" . time() . "." . $ext;
        
        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            "../uploads/events/" . $photo
        );
    }
    
    $query = mysqli_query($conn, "
        INSERT INTO events 
        (id_community, nama_event, deskripsi, tanggal, jam, lokasi, photo, visibility, created_at)
        VALUES 
        ($id_community, '$nama_event', '$deskripsi', '$tanggal', '$jam', '$lokasi', '$photo', '$visibility', NOW())
    ");
    
    if ($query) {
        header("Location: index.php?success=1");
        exit();
    } else {
        $message = "Gagal membuat event!";
    }
}

// Get user's communities (as admin/member)
$communities = mysqli_query($conn, "
    SELECT DISTINCT c.id_community, c.nama_community 
    FROM communities c
    JOIN community_members cm ON c.id_community = cm.id_community
    WHERE cm.id_user = $id_user
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Event - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <a href="index.php" class="btn btn-sm">← Kembali</a>
  
  <h2><i class="fa-solid fa-calendar-plus" style="color:var(--primary); margin-right:10px;"></i>Buat Event Baru</h2>

  <?php if ($message): ?>
    <div class="alert alert-error"><?= $message; ?></div>
  <?php endif; ?>

  <?php if (mysqli_num_rows($communities) == 0): ?>
    <div class="alert alert-info">
      Kamu harus menjadi member komunitas terlebih dahulu untuk membuat event.
      <a href="../communities/index.php">Lihat Komunitas</a>
    </div>
  <?php else: ?>
    <div class="card">
      <form method="POST" enctype="multipart/form-data">
        
        <label>Komunitas <span class="required">*</span></label>
        <select name="id_community" required>
          <option value="">-- Pilih Komunitas --</option>
          <?php while ($c = mysqli_fetch_assoc($communities)): ?>
            <option value="<?= $c['id_community']; ?>">
              <?= htmlspecialchars($c['nama_community']); ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Nama Event <span class="required">*</span></label>
        <input type="text" name="nama_event" placeholder="Workshop Web Development" required>

        <label>Deskripsi <span class="required">*</span></label>
        <textarea name="deskripsi" rows="5" placeholder="Jelaskan tentang event ini..." required></textarea>

        <div class="form-row">
          <div class="form-col">
            <label>Tanggal <span class="required">*</span></label>
            <input type="date" name="tanggal" required>
          </div>
          <div class="form-col">
            <label>Jam <span class="required">*</span></label>
            <input type="time" name="jam" required>
          </div>
        </div>

        <label>Lokasi <span class="required">*</span></label>
        <input type="text" name="lokasi" placeholder="Lab Komputer Lt. 3" required>

        <label>Visibility <span class="required">*</span></label>
        <select name="visibility" required>
          <option value="public">Public (Semua orang bisa lihat)</option>
          <option value="internal">Internal (Hanya member komunitas)</option>
        </select>

        <hr>

        <label>Foto Event</label>
        <input type="file" name="photo" accept="image/*" class="input-file">
        <small class="text-muted">Format: JPG, PNG. Maks 2MB</small>

        <hr>

        <button type="submit" class="btn btn-success"><i class="fa-solid fa-calendar-check"></i> Buat Event</button>
      </form>
    </div>
  <?php endif; ?>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
