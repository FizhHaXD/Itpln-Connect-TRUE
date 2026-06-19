<footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
        <div class="brand-icon" style="width:38px; height:38px; background:linear-gradient(135deg,#818cf8,#22d3ee); border-radius:10px; display:flex; align-items:center; justify-content:center;">
          <i class="fa-solid fa-graduation-cap" style="color:white; font-size:1rem;"></i>
        </div>
        <span class="brand-text">ITPLN Connect</span>
      </div>
      <p class="footer-tagline">Platform Komunitas &amp; Event Kampus<br>untuk mahasiswa ITPLN.</p>
    </div>

    <div class="footer-links">
      <div class="footer-section">
        <h4 class="footer-section-toggle" onclick="toggleFooterSection(this)">
          Menu
          <i class="fa-solid fa-chevron-down footer-toggle-icon"></i>
        </h4>
        <div class="footer-section-content">
          <a href="<?= BASE_URL ?>dashboard/index.php">Dashboard</a>
          <a href="<?= BASE_URL ?>communities/index.php">Komunitas</a>
          <a href="<?= BASE_URL ?>events/index.php">Event</a>
        </div>
      </div>

      <div class="footer-section">
        <h4 class="footer-section-toggle" onclick="toggleFooterSection(this)">
          Akun
          <i class="fa-solid fa-chevron-down footer-toggle-icon"></i>
        </h4>
        <div class="footer-section-content">
          <a href="<?= BASE_URL ?>profile/index.php">Profil Saya</a>
          <a href="<?= BASE_URL ?>auth/login.php">Masuk</a>
          <a href="<?= BASE_URL ?>auth/register.php">Daftar</a>
        </div>
      </div>

      <div class="footer-section">
        <h4 class="footer-section-toggle" onclick="toggleFooterSection(this)">
          Kontak
          <i class="fa-solid fa-chevron-down footer-toggle-icon"></i>
        </h4>
        <div class="footer-section-content">
          <p>info@itplnconnect.id</p>
          <p>+62 812-3456-7890</p>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; <?= date('Y'); ?> ITPLN Connect. Dibuat untuk mahasiswa.</p>
  </div>
</footer>

<script>
function toggleFooterSection(el) {
  // Only toggle on mobile
  if (window.innerWidth > 768) return;
  const content = el.nextElementSibling;
  const icon = el.querySelector('.footer-toggle-icon');
  content.classList.toggle('open');
  icon.classList.toggle('rotated');
}
</script>
