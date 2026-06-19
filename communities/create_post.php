<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_community = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data komunitas
$community = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM communities WHERE id_community=$id_community"
));

if (!$community) {
    header("Location: index.php");
    exit();
}

// Cek apakah user adalah member
$check = mysqli_query($conn,
    "SELECT * FROM community_members 
     WHERE id_user=$id_user AND id_community=$id_community"
);
$isMember = mysqli_num_rows($check) > 0;

if (!$isMember) {
    header("Location: detail.php?id=$id_community");
    exit();
}

$message = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $visibility = mysqli_real_escape_string($conn, $_POST['visibility']);
    
    // Social media links (optional)
    $wa_link = isset($_POST['whatsapp_link']) ? mysqli_real_escape_string($conn, $_POST['whatsapp_link']) : '';
    $discord_link = isset($_POST['discord_link']) ? mysqli_real_escape_string($conn, $_POST['discord_link']) : '';
    $instagram_link = isset($_POST['instagram_link']) ? mysqli_real_escape_string($conn, $_POST['instagram_link']) : '';
    
    // Handle foto upload (max 3)
    $foto1 = null;
    $foto2 = null;
    $foto3 = null;
    
    if (!empty($_FILES['fotos']['name'][0])) {
        // Ensure directory exists
        if (!is_dir("../uploads/posts/")) {
            mkdir("../uploads/posts/", 0777, true);
        }
        
        // Process up to 3 photos
        $uploadedCount = 0;
        $fotoFields = ['foto1', 'foto2', 'foto3'];
        
        foreach ($_FILES['fotos']['tmp_name'] as $key => $tmpName) {
            if ($uploadedCount >= 3) break; // Max 3 photos
            
            if (!empty($tmpName) && $_FILES['fotos']['error'][$key] === 0) {
                $ext = pathinfo($_FILES['fotos']['name'][$key], PATHINFO_EXTENSION);
                $filename = "post_" . time() . "_" . ($uploadedCount + 1) . "_" . rand(1000, 9999) . "." . $ext;
                
                if (move_uploaded_file($tmpName, "../uploads/posts/" . $filename)) {
                    ${$fotoFields[$uploadedCount]} = $filename;
                    $uploadedCount++;
                }
            }
        }
    }
    
    if (!empty($title) && !empty($content)) {
        $query = mysqli_query($conn, "
            INSERT INTO community_posts 
            (id_community, id_user, title, content, foto, foto2, foto3, visibility,
             wa_link, discord_link, instagram_link, created_at)
            VALUES 
            ($id_community, $id_user, '$title', '$content', 
             " . ($foto1 ? "'$foto1'" : "NULL") . ", 
             " . ($foto2 ? "'$foto2'" : "NULL") . ", 
             " . ($foto3 ? "'$foto3'" : "NULL") . ", 
             '$visibility',
             " . ($wa_link ? "'$wa_link'" : "NULL") . ", 
             " . ($discord_link ? "'$discord_link'" : "NULL") . ", 
             " . ($instagram_link ? "'$instagram_link'" : "NULL") . ", 
             NOW())
        ");
        
        if ($query) {
            header("Location: detail.php?id=$id_community&posted=1");
            exit();
        } else {
            $message = "Gagal membuat postingan: " . mysqli_error($conn);
        }
    } else {
        $message = "Judul dan konten harus diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Postingan - <?= htmlspecialchars($community['nama_community']); ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="main-content">
<div class="container">
  <a href="detail.php?id=<?= $id_community; ?>" class="btn btn-sm">← Kembali</a>
  
  <h2><i class="fa-solid fa-pen" style="color:var(--primary); margin-right:10px;"></i>Buat Postingan Baru</h2>
  <p class="text-muted">Posting ke komunitas: <b><?= htmlspecialchars($community['nama_community']); ?></b></p>

  <?php if ($message): ?>
    <div class="alert alert-error"><?= $message; ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="POST" enctype="multipart/form-data">
      
      <label>Judul Postingan <span class="required">*</span></label>
      <input type="text" name="title" required placeholder="Judul postingan">

      <label>Isi Postingan <span class="required">*</span></label>
      <textarea name="content" rows="6" required placeholder="Tulis postingan..."></textarea>

      <label>Foto (Opsional)</label>
      <input type="file" name="fotos[]" accept="image/*" multiple class="input-file">
      <small class="text-muted">Pilih hingga 3 foto. Format: JPG, PNG. Maks 2MB per foto</small>

      <label>Visibility <span class="required">*</span></label>
      <select name="visibility" required>
        <option value="internal">Internal - Hanya member komunitas</option>
        <option value="eksternal">Eksternal - Public, semua bisa lihat</option>
        <option value="private">Private - Hanya saya yang bisa lihat</option>
      </select>
      <small class="text-muted">
        <b>Eksternal</b> akan tampil di carousel dashboard
      </small>

      <hr>

      <h4><i class="fa-solid fa-link"></i> Link Komunikasi (Opsional)</h4>
      <p class="text-muted" style="margin-top: -10px; margin-bottom: 16px;">Tambahkan link untuk memudahkan anggota berkomunikasi</p>

      <label>Link WhatsApp</label>
      <input type="url" name="whatsapp_link" placeholder="https://wa.me/...">

      <label>Link Discord</label>
      <input type="url" name="discord_link" placeholder="https://discord.gg/...">

      <label>Link Instagram</label>
      <input type="url" name="instagram_link" placeholder="https://instagram.com/...">

      <div class="form-actions">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Posting Sekarang</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
