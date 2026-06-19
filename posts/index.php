<?php
session_start();
require_once "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Postingan - ITPLN Connect</title>
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
<div class="container-wide">
  <div class="page-header">
    <h2><i class="fa-solid fa-bullhorn" style="color:var(--primary); margin-right:10px;"></i>Semua Postingan</h2>
    <p class="text-muted">Lihat postingan komunitas dan postingan global</p>
  </div>

  <!-- TWO COLUMN POSTS LAYOUT -->
  <div class="posts-grid">
    <!-- KIRI: Postingan Komunitas -->
    <div class="posts-column">
      <div class="card">
        <div class="posts-section-title">
          <h3><i class="fa-solid fa-users" style="color:var(--primary); margin-right:8px;"></i>Postingan Komunitas</h3>
        </div>

        <!-- Filter for Community Posts -->
        <div class="filter-box">
          <label class="filter-label">
            <input type="checkbox" id="filterMyCommunities" <?= !$id_user ? 'disabled' : ''; ?>>
            <span>Hanya dari komunitas saya</span>
          </label>
        </div>

        <div id="communityPostsContainer">
          <p style="text-align:center; color:var(--text-muted);">Memuat postingan komunitas...</p>
        </div>

        <!-- Load More Community -->
        <div id="loadMoreCommunity" style="text-align: center; margin-top: 16px; display: none;">
          <button class="btn btn-sm" onclick="loadMoreCommunityPosts()">Muat Lebih Banyak</button>
        </div>
      </div>
    </div>

    <!-- KANAN: Postingan Global -->
    <div class="posts-column">
      <div class="card">
        <div class="posts-section-title">
          <h3><i class="fa-solid fa-globe" style="color:var(--primary); margin-right:8px;"></i>Postingan Global</h3>
          <a href="create_global.php" class="btn btn-sm btn-success"><i class="fa-solid fa-pen"></i> Post</a>
        </div>

        <div id="globalPostsContainer">
          <p style="text-align:center; color:var(--text-muted);">Memuat postingan global...</p>
        </div>

        <!-- Load More Global -->
        <div id="loadMoreGlobal" style="text-align: center; margin-top: 16px; display: none;">
          <button class="btn btn-sm" onclick="loadMoreGlobalPosts()">Muat Lebih Banyak</button>
        </div>
      </div>
    </div>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

<script src="../assets/js/posts.js"></script>

</body>
</html>
