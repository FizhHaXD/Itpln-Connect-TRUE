<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
?>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<nav class="navbar">
  <div class="navbar-container">

    <!-- BRAND -->
    <a href="<?= BASE_URL ?>index.php" class="navbar-brand">
      <span class="brand-icon">
        <i class="fa-solid fa-graduation-cap" style="color:white; font-size:1rem;"></i>
      </span>
      <span class="brand-text">ITPLN Connect</span>
    </a>

    <!-- MENU TENGAH -->
    <div class="navbar-menu navbar-center">
      <a href="<?= BASE_URL ?>communities/index.php" class="nav-link">
        <i class="fa-solid fa-users"></i> Komunitas
      </a>
      <a href="<?= BASE_URL ?>events/index.php" class="nav-link">
        <i class="fa-solid fa-calendar-days"></i> Event
      </a>
    </div>

    <!-- AUTH BUTTONS -->
    <div class="navbar-auth">
      <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-outline-primary">
        Masuk
      </a>
      <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-primary">
        Daftar
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
    <a href="<?= BASE_URL ?>communities/index.php" class="mobile-link">
      <i class="fa-solid fa-users"></i> Komunitas
    </a>
    <a href="<?= BASE_URL ?>events/index.php" class="mobile-link">
      <i class="fa-solid fa-calendar-days"></i> Event
    </a>
    <a href="<?= BASE_URL ?>auth/login.php" class="mobile-link">
      <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
    </a>
    <a href="<?= BASE_URL ?>auth/register.php" class="mobile-link">
      <i class="fa-solid fa-user-plus"></i> Daftar
    </a>
  </div>
</nav>

<script>
function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  menu.classList.toggle('active');
}
</script>
