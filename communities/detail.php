<?php
session_start();
include "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;
$id_community = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data komunitas
$community = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM communities WHERE id_community=$id_community"
));

if (!$community) {
    header("Location: index.php");
    exit();
}

// Ambil postingan komunitas dengan
$isLoggedIn = isset($_SESSION['id_user']);
$currentUserId = $isLoggedIn ? $_SESSION['id_user'] : null;

// Check if user is member
$isMember = false;
if ($currentUserId) {
    $memberCheck = mysqli_query($conn, "
        SELECT 1 FROM community_members 
        WHERE id_community = $id_community AND id_user = $currentUserId
    ");
    $isMember = mysqli_num_rows($memberCheck) > 0;
}

// Get posts with visibility filtering
// Internal: member only
// Eksternal: everyone
// Private: creator only
$postsQuery = "
    SELECT p.*, u.nama as nama_user, u.foto as foto_user
    FROM community_posts p
    JOIN users u ON p.id_user = u.id_user
    WHERE p.id_community = $id_community
    AND (
        p.visibility = 'eksternal'
        " . ($isMember ? "OR p.visibility = 'internal'" : "") . "
        " . ($currentUserId ? "OR (p.visibility = 'private' AND p.id_user = $currentUserId)" : "") . "
    )
    ORDER BY p.created_at DESC
";

$posts = mysqli_query($conn, $postsQuery);

// Cek apakah user sudah join (hanya jika logged in)
// This block is now redundant as $isMember is already determined above.
// However, to faithfully apply the change as requested, we keep it.
// In a real scenario, this would be removed or refactored.
$isMember = false;
if ($id_user) {
    $check = mysqli_query($conn,
        "SELECT * FROM community_members 
         WHERE id_user=$id_user AND id_community=$id_community"
    );
    $isMember = mysqli_num_rows($check) > 0;
}

// Set foto default
$foto = !empty($community['foto']) ? $community['foto'] : 'default_community.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($community['nama_community']); ?> - Komunitas</title>
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
  <div class="nav">
    <a class="btn" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <a class="btn" href="../dashboard/index.php"><i class="fa-solid fa-house"></i> Dashboard</a>
  </div>

  <?php if (isset($_GET['joined'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Selamat! Kamu sudah bergabung dengan komunitas ini!</div>
  <?php endif; ?>

  <?php if (isset($_GET['posted'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Postingan berhasil dibuat!</div>
  <?php endif; ?>

  <!-- Banner Komunitas -->
  <div class="community-banner">
    <img src="../uploads/communities/<?= htmlspecialchars($foto); ?>" class="banner-img" alt="<?= htmlspecialchars($community['nama_community']); ?>">
  </div>

  <!-- Info Komunitas -->
  <div class="card">
    <span class="badge"><?= htmlspecialchars($community['kategori']); ?></span>
    <h1><?= htmlspecialchars($community['nama_community']); ?></h1>
    
    <p class="intro"><?= nl2br(htmlspecialchars($community['deskripsi'])); ?></p>

    <?php if (!empty($community['admin_whatsapp']) || !empty($community['discord_link']) || !empty($community['instagram_link']) || !empty($community['telegram_link']) || !empty($community['website_link']) || !empty($community['email_contact'])): ?>
      <div class="community-social-links">
        <hr>
        <h3><i class="fa-solid fa-link"></i> Link Komunikasi</h3>
        <div class="community-links">
          <?php if (!empty($community['admin_whatsapp'])): ?>
              <a href="https://wa.me/<?= htmlspecialchars($community['admin_whatsapp']); ?>" class="btn btn-sm btn-wa" target="_blank">
                <i class="fa-brands fa-whatsapp"></i> Chat Admin WhatsApp
              </a>
          <?php endif; ?>
          
          <?php if (!empty($community['discord_link'])): ?>
            <a href="<?= htmlspecialchars($community['discord_link']); ?>" target="_blank" class="btn btn-sm btn-discord"><i class="fa-brands fa-discord"></i> Discord</a>
          <?php endif; ?>
          
          <?php if (!empty($community['instagram_link'])): ?>
            <a href="<?= htmlspecialchars($community['instagram_link']); ?>" target="_blank" class="btn btn-sm btn-ig"><i class="fa-brands fa-instagram"></i> Instagram</a>
          <?php endif; ?>

          <?php if (!empty($community['telegram_link'])): ?>
            <a href="<?= htmlspecialchars($community['telegram_link']); ?>" target="_blank" class="btn btn-sm btn-telegram"><i class="fa-brands fa-telegram"></i> Telegram</a>
          <?php endif; ?>

          <?php if (!empty($community['website_link'])): ?>
            <a href="<?= htmlspecialchars($community['website_link']); ?>" target="_blank" class="btn btn-sm btn-website"><i class="fa-solid fa-globe"></i> Website</a>
          <?php endif; ?>

          <?php if (!empty($community['email_contact'])): ?>
            <a href="mailto:<?= htmlspecialchars($community['email_contact']); ?>" class="btn btn-sm btn-email"><i class="fa-solid fa-envelope"></i> Email</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <hr>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <?php if (!$id_user): ?>
        <!-- Guest: needs to login first -->
        <a href="../auth/login.php?need_login=1&return=<?= urlencode('communities/detail.php?id=' . $id_community); ?>" class="btn btn-success">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk untuk Join
        </a>
      <?php elseif (!$isMember): ?>
        <!-- Logged in but not member -->
        <a href="join.php?id=<?= $id_community; ?>" class="btn btn-success"><i class="fa-solid fa-handshake"></i> Join Komunitas</a>
      <?php else: ?>
        <!-- Already a member -->
        <span class="badge badge-success badge-lg"><i class="fa-solid fa-check"></i> Sudah Bergabung</span>
      <?php endif; ?>

      <?php if (!empty($community['admin_whatsapp'])): ?>
        <a href="https://wa.me/62<?= htmlspecialchars($community['admin_whatsapp']); ?>?text=Halo%20Admin%20<?= urlencode($community['nama_community']); ?>"
           target="_blank"
           class="btn btn-wa">
           <i class="fa-brands fa-whatsapp"></i> Hubungi Admin
        </a>
      <?php endif; ?>

      <a href="../events/index.php?community=<?= $id_community; ?>" class="btn">
         <i class="fa-solid fa-calendar-days"></i> Lihat Event
      </a>
    </div>
  </div>

  <!-- Postingan Komunitas -->
  <div class="card">
    <h3><i class="fa-solid fa-bullhorn" style="color:var(--primary); margin-right:8px;"></i>Postingan Komunitas</h3>

    <?php if (mysqli_num_rows($posts) == 0): ?>
      <p class="empty-message">Belum ada postingan di komunitas ini.</p>
    <?php else: ?>
      <?php while ($p = mysqli_fetch_assoc($posts)): ?>
        <?php 
          $fotoUser = !empty($p['foto_user']) 
            ? '../uploads/profiles/' . $p['foto_user'] 
            : '../uploads/profiles/default.png';
        ?>

        <div class="post-card card">
          <div class="post-header">
            <img src="<?= htmlspecialchars($fotoUser); ?>" class="post-avatar" alt="User">
            <div style="flex: 1;">
              <b><?= htmlspecialchars($p['nama_user']); ?></b>
              <p class="text-muted" style="font-size: 0.85rem; margin: 0;">
                <?= date('d M Y, H:i', strtotime($p['created_at'])); ?>
                <?php if ($p['visibility'] == 'private'): ?>
                  <span class="badge" style="background: #f59e0b; color:white;"><i class="fa-solid fa-user"></i> Private</span>
                <?php elseif ($p['visibility'] == 'eksternal'): ?>
                  <span class="badge" style="background: #10b981; color:white;"><i class="fa-solid fa-globe"></i> Public</span>
                <?php else: ?>
                  <span class="badge" style="background: #3b82f6; color:white;"><i class="fa-solid fa-lock"></i> Internal</span>
                <?php endif; ?>
              </p>
            </div>
          </div>

          <?php if (!empty($p['title'])): ?>
            <h3><?= htmlspecialchars($p['title']); ?></h3>
          <?php endif; ?>
          
          <p><?= nl2br(htmlspecialchars($p['content'])); ?></p>

          <?php if (!empty($p['foto'])): ?>
            <img src="../uploads/posts/<?= htmlspecialchars($p['foto']); ?>" 
                 alt="Post" 
                 style="max-width: 100%; border-radius: 12px; margin: 16px 0;">
          <?php endif; ?>

          <?php if (!empty($p['whatsapp_link']) || !empty($p['discord_link']) || !empty($p['instagram_link'])): ?>
            <div class="post-links">
              <?php if (!empty($p['whatsapp_link'])): ?>
                <a href="<?= htmlspecialchars($p['whatsapp_link']); ?>" target="_blank" class="btn btn-sm btn-wa"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
              <?php endif; ?>
              
              <?php if (!empty($p['discord_link'])): ?>
                <a href="<?= htmlspecialchars($p['discord_link']); ?>" target="_blank" class="btn btn-sm btn-discord"><i class="fa-brands fa-discord"></i> Discord</a>
              <?php endif; ?>
              
              <?php if (!empty($p['instagram_link'])): ?>
                <a href="<?= htmlspecialchars($p['instagram_link']); ?>" target="_blank" class="btn btn-sm btn-ig"><i class="fa-brands fa-instagram"></i> Instagram</a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>

    <?php if ($isMember): ?>
      <hr>
      <a href="create_post.php?id=<?= $id_community; ?>" class="btn"><i class="fa-solid fa-pen"></i> Buat Postingan</a>
    <?php endif; ?>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
