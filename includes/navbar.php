<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<nav class="navbar">
  <div class="navbar-container">

    <!-- BRAND -->
    <a href="<?= BASE_URL ?>dashboard/index.php" class="navbar-brand">
      <span class="brand-icon">
        <i class="fa-solid fa-graduation-cap" style="color:white; font-size:1rem;"></i>
      </span>
      <span class="brand-text">ITPLN Connect</span>
    </a>

    <!-- MENU TENGAH -->
    <div class="navbar-menu navbar-center">
      <a href="<?= BASE_URL ?>dashboard/index.php" class="nav-link <?= ($currentDir == 'dashboard') ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i> Dashboard
      </a>
      <a href="<?= BASE_URL ?>communities/index.php" class="nav-link <?= ($currentDir == 'communities') ? 'active' : ''; ?>">
        <i class="fa-solid fa-users"></i> Komunitas
      </a>
      <a href="<?= BASE_URL ?>events/index.php" class="nav-link <?= ($currentDir == 'events') ? 'active' : ''; ?>">
        <i class="fa-solid fa-calendar-days"></i> Event
      </a>
      <a href="<?= BASE_URL ?>study/index.php" class="nav-link <?= ($currentDir == 'study') ? 'active' : ''; ?>">
        <i class="fa-solid fa-book-open"></i> Belajar
      </a>
      <a href="<?= BASE_URL ?>posts/index.php" class="nav-link <?= ($currentDir == 'posts') ? 'active' : ''; ?>">
        <i class="fa-solid fa-bullhorn"></i> Postingan
      </a>
      <a href="<?= BASE_URL ?>chat/chat_list.php" class="nav-link <?= ($currentDir == 'chat') ? 'active' : ''; ?>">
        <i class="fa-solid fa-comments"></i> Chat
      </a>
      <?php
      // Show admin link only for admins
      include_once __DIR__ . '/../config/security.php';
      if (is_admin($conn)):
      ?>
      <a href="<?= BASE_URL ?>admin/index.php" class="nav-link admin-link <?= ($currentDir == 'admin') ? 'active' : ''; ?>">
        <i class="fa-solid fa-shield-halved"></i> Admin
      </a>
      <?php endif; ?>
    </div>

    <!-- USER PROFILE -->
    <div class="navbar-profile">
      <!-- POST GLOBAL BUTTON -->
      <a href="<?= BASE_URL ?>posts/create_global.php" class="btn btn-success" style="margin-right: 8px;">
        <i class="fa-solid fa-pen"></i> Post Global
      </a>
      
      <a href="<?= BASE_URL ?>profile/index.php" class="profile-link">
        <?php 
        $userQuery = mysqli_query($conn, "SELECT foto FROM users WHERE id_user = " . $_SESSION['id_user']);
        $userData = mysqli_fetch_assoc($userQuery);
        
        if (!empty($userData['foto'])) {
            $profilePic = $userData['foto'];
            $_SESSION['foto'] = $userData['foto'];
        } elseif (!empty($_SESSION['foto'])) {
            $profilePic = $_SESSION['foto'];
        } else {
            $profilePic = 'default.png';
        }
        
        $profilePath = BASE_URL . 'uploads/profiles/' . $profilePic;
        ?>
        <img src="<?= $profilePath; ?>" 
             alt="<?= htmlspecialchars($_SESSION['nama']); ?>" 
             class="profile-avatar"
             onerror="this.src='<?= BASE_URL ?>uploads/profiles/default.png'">
        <span class="profile-name"><?= htmlspecialchars($_SESSION['nama']); ?></span>
      </a>
      <a href="<?= BASE_URL ?>logout.php" class="btn btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>

    <!-- MOBILE MENU TOGGLE -->
    <button class="mobile-toggle" onclick="toggleMobileMenu()">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>

  <!-- MOBILE MENU -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="<?= BASE_URL ?>dashboard/index.php" class="mobile-link">
      <i class="fa-solid fa-house"></i> Dashboard
    </a>
    <a href="<?= BASE_URL ?>communities/index.php" class="mobile-link">
      <i class="fa-solid fa-users"></i> Komunitas
    </a>
    <a href="<?= BASE_URL ?>events/index.php" class="mobile-link">
      <i class="fa-solid fa-calendar-days"></i> Event
    </a>
    <a href="<?= BASE_URL ?>study/index.php" class="mobile-link">
      <i class="fa-solid fa-book-open"></i> Belajar
    </a>
    <a href="<?= BASE_URL ?>posts/index.php" class="mobile-link">
      <i class="fa-solid fa-bullhorn"></i> Postingan
    </a>
    <a href="<?= BASE_URL ?>chat/chat_list.php" class="mobile-link">
      <i class="fa-solid fa-comments"></i> Chat
    </a>
    <a href="<?= BASE_URL ?>posts/create_global.php" class="mobile-link" style="color: var(--success); font-weight:600;">
      <i class="fa-solid fa-pen"></i> Post Global
    </a>
    <a href="<?= BASE_URL ?>logout.php" class="mobile-link logout">
      <i class="fa-solid fa-right-from-bracket"></i> Keluar
    </a>
  </div>
</nav>

<script src="<?= BASE_URL ?>assets/js/navbar.js"></script>
