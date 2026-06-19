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
    $nama_community = mysqli_real_escape_string($conn, $_POST['nama_community']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    
    // Social media - Admin WhatsApp (nomor saja, tanpa +)
    $admin_whatsapp = isset($_POST['admin_whatsapp']) ? mysqli_real_escape_string($conn, preg_replace('/[^0-9]/', '', $_POST['admin_whatsapp'])) : '';
    $discord_link = isset($_POST['discord_link']) ? mysqli_real_escape_string($conn, $_POST['discord_link']) : '';
    $instagram_link = isset($_POST['instagram_link']) ? mysqli_real_escape_string($conn, $_POST['instagram_link']) : '';
    $telegram_link = isset($_POST['telegram_link']) ? mysqli_real_escape_string($conn, $_POST['telegram_link']) : '';
    $website_link = isset($_POST['website_link']) ? mysqli_real_escape_string($conn, $_POST['website_link']) : '';
    $email_contact = isset($_POST['email_contact']) ? mysqli_real_escape_string($conn, $_POST['email_contact']) : '';
    
    // Handle foto upload
    $foto = "default_community.png"; // Default foto
    
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $foto = "community_" . time() . "." . $ext;
        
        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            "../uploads/communities/" . $foto
        );
    }
    
    $query = mysqli_query($conn, "
        INSERT INTO communities 
        (nama_community, deskripsi, kategori, foto, 
         admin_whatsapp, discord_link, instagram_link, 
         telegram_link, website_link, email_contact)
        VALUES 
        ('$nama_community', '$deskripsi', '$kategori', '$foto',
         '$admin_whatsapp', '$discord_link', '$instagram_link',
         '$telegram_link', '$website_link', '$email_contact')
    ");
    
    if ($query) {
        // Otomatis jadikan pembuat komunitas sebagai admin/pemilik
        $id_community = mysqli_insert_id($conn);
        mysqli_query($conn, "
            INSERT INTO community_members (id_user, id_community, role, joined_at)
            VALUES ($id_user, $id_community, 'admin', NOW())
        ");

        $message = "Komunitas berhasil dibuat!";
        header("Location: index.php?success=1");
        exit();
    } else {
        $message = "Gagal membuat komunitas: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Komunitas</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
  <div class="nav">
    <a class="btn" href="../dashboard/index.php"><i class="fa-solid fa-house"></i> Dashboard</a>
  </div>

  <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
    <a href="index.php" class="btn btn-sm" style="margin:0;">&larr; Kembali</a>
    <h2 style="margin:0;"><i class="fa-solid fa-plus" style="color:var(--primary); margin-right:10px;"></i>Buat Komunitas Baru</h2>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-error"><?= $message; ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="POST" enctype="multipart/form-data">
      <label>Nama Komunitas</label>
      <input type="text" name="nama_community" placeholder="Contoh: Klub Anime" required>

      <label>Kategori</label>
      <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="Akademik">Akademik</option>
        <option value="Olahraga">Olahraga</option>
        <option value="Seni">Seni</option>
        <option value="Teknologi">Teknologi</option>
        <option value="Musik">Musik</option>
        <option value="Gaming">Gaming</option>
        <option value="Lainnya">Lainnya</option>
      </select>

      <label>Deskripsi</label>
      <textarea name="deskripsi" rows="4" placeholder="Jelaskan tentang komunitas ini..." required></textarea>

      <hr>

      <h4><i class="fa-solid fa-link"></i> Link Komunikasi (Opsional)</h4>
      <p class="text-muted" style="margin-bottom: 16px;">Tambahkan link grup atau media sosial untuk berkomunikasi dengan anggota</p>
      <hr>

      <label>Admin WhatsApp (Nomor)</label>
      <input type="text" name="admin_whatsapp" placeholder="628123456789" pattern="[0-9]+" title="Hanya angka, contoh: 628123456789">
      <small class="text-muted">Format: 628123456789 (tanpa +, -, atau spasi)</small>

      <label>Link Discord Server</label>
      <input type="url" name="discord_link" placeholder="https://discord.gg/...">

      <label>Instagram</label>
      <input type="url" name="instagram_link" placeholder="https://instagram.com/...">

      <label>Telegram Group</label>
      <input type="url" name="telegram_link" placeholder="https://t.me/...">

      <label>Website</label>
      <input type="url" name="website_link" placeholder="https://...">

      <label>Email Kontak</label>
      <input type="email" name="email_contact" placeholder="contact@example.com">

      <hr>

      <label>Foto Komunitas</label>
      <input type="file" name="photo" accept="image/*" class="input-file">
      <small class="text-muted">Opsional. Format: JPG, PNG. Maks 2MB</small>

      <div class="form-actions">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Simpan Komunitas</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
