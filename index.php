<?php
include "config/database.php";
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard/index.php");
    exit();
}

// Ambil postingan eksternal untuk carousel
$carouselPosts = mysqli_query($conn, "
    SELECT p.*, c.nama_community, u.nama as nama_user, u.foto as foto_user
    FROM community_posts p
    JOIN communities c ON p.id_community = c.id_community
    JOIN users u ON p.id_user = u.id_user
    WHERE p.visibility = 'eksternal'
    ORDER BY p.created_at DESC
    LIMIT 10
");

// Ambil 6 komunitas terbaru
$communities = mysqli_query($conn, "
    SELECT * FROM communities 
    ORDER BY id_community DESC 
    LIMIT 6
");

// Ambil 6 event terbaru
$events = mysqli_query($conn, "
    SELECT e.*, c.nama_community
    FROM events e
    LEFT JOIN communities c ON e.id_community = c.id_community
    ORDER BY e.tanggal DESC
    LIMIT 6
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ITPLN Connect - Platform Komunitas & Event Kampus</title>
  <meta name="description" content="Platform komunitas dan event kampus ITPLN. Temukan komunitas sesuai minatmu, ikuti event, dan terhubung dengan sesama mahasiswa.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css?v=2.0">
  <link rel="stylesheet" href="assets/css/landing.css?v=2.0">
</head>
<body>

<?php include "includes/navbar_guest.php"; ?>

<main class="main-content">

  <!-- HERO SECTION -->
  <section class="hero-modern">
    <div class="hero-container">
      <div class="hero-text">
        <h1>Temukan Ruangmu, <br><span>Kembangkan Potensimu</span></h1>
        <p>ITPLN Connect adalah wadah bagi seluruh mahasiswa untuk menemukan komunitas yang sesuai minat, mengikuti event seru, dan membangun relasi yang berarti di kampus.</p>
        <div class="hero-actions">
          <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-primary btn-lg">
            Mulai Sekarang <i class="fa-solid fa-arrow-right"></i>
          </a>
          <a href="<?= BASE_URL ?>communities/index.php" class="btn btn-outline-primary btn-lg" style="background: white;">
            Jelajahi Komunitas
          </a>
        </div>
      </div>
      <div class="hero-graphic">
        <div class="graphic-circle"></div>
        
        <div class="graphic-card-1">
          <div class="graphic-icon icon-blue">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="graphic-text">
            <b>15+ Komunitas</b>
            <span>Aktif berkolaborasi</span>
          </div>
        </div>

        <div class="graphic-card-2">
          <div class="graphic-icon icon-cyan">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <div class="graphic-text">
            <b>Event Mingguan</b>
            <span>Jangan sampai terlewat</span>
          </div>
        </div>

        <div class="graphic-card-3">
          <div class="graphic-icon icon-green">
            <i class="fa-solid fa-comments"></i>
          </div>
          <div class="graphic-text">
            <b>Diskusi Terbuka</b>
            <span>Berbagi ide kreatif</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEED LAYOUT WRAPPER -->
  <div class="feed-container container-wide">
    
    <!-- LEFT SIDEBAR -->
    <aside class="feed-sidebar">
      <button class="sidebar-toggle-btn" onclick="toggleSidebar()" id="sidebarToggle">
        <span><i class="fa-solid fa-bars" style="margin-right:8px;"></i> Menu & Navigasi</span>
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="sidebar-content-wrapper" id="sidebarContent">
        <div class="sidebar-card">
          <h3>Menu Utama</h3>
          <ul class="sidebar-nav">
            <li><a href="<?= BASE_URL ?>index.php"><i class="fa-solid fa-house"></i> Beranda</a></li>
            <li><a href="<?= BASE_URL ?>communities/index.php"><i class="fa-solid fa-users"></i> Komunitas</a></li>
            <li><a href="<?= BASE_URL ?>events/index.php"><i class="fa-solid fa-calendar-days"></i> Event Mendatang</a></li>
          </ul>
        </div>

        <div class="sidebar-card" style="background: var(--primary); color: white;">
          <h3 style="border-bottom-color: rgba(255,255,255,0.2);">Siap Bergabung?</h3>
          <p style="font-size: 0.9rem; margin-bottom: 16px; opacity: 0.9;">Buat akunmu sekarang dan mulai bangun koneksi dengan mahasiswa lainnya.</p>
          <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-outline-primary" style="background: white; color: var(--primary); width: 100%; justify-content: center;">Daftar Gratis</a>
        </div>
      </div>
    </aside>

    <!-- RIGHT CONTENT (FEED) -->
    <div class="feed-content">

      <!-- FEATURES SECTION -->
      <section class="features-section">
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-people-group"></i>
        </div>
        <h3>Komunitas Beragam</h3>
        <p>Dari teknologi, seni, hingga olahraga. Temukan tempat di mana passion dan minatmu bisa berkembang bersama teman-teman yang sefrekuensi.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-calendar-days"></i>
        </div>
        <h3>Event Terpusat</h3>
        <p>Tidak perlu takut ketinggalan informasi. Semua event kampus dari seminar, workshop, hingga perlombaan dapat kamu pantau di satu tempat.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-share-nodes"></i>
        </div>
        <h3>Koneksi & Kolaborasi</h3>
        <p>Bangun networking yang kuat sejak masa kuliah. Temukan partner diskusi, berbagi ide, dan wujudkan karya-karya hebat bersama komunitas.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-folder-open"></i>
        </div>
        <h3>Portofolio Otomatis</h3>
        <p>Setiap aktivitas, kepanitiaan, dan event yang kamu ikuti akan tercatat otomatis sebagai jejak rekam portofolio digital masa kuliahmu.</p>
      </div>
    </div>
  </section>

  <!-- CAROUSEL SECTION -->
  <section class="section-spacing bg-light">
    <div class="container-wide">
      <div class="modern-header">
        <h2>Kabar Terbaru</h2>
        <p>Intip aktivitas dan pengumuman terbaru dari berbagai komunitas yang ada di ITPLN Connect.</p>
      </div>
      
      <div class="landing-carousel" style="margin-top:0;">
        <div class="carousel-wrapper">
          <button class="carousel-btn carousel-prev" onclick="carouselPrev()">&#8249;</button>
          
          <div class="carousel-slide" id="carouselSlide">
            <div class="carousel-loading">Memuat postingan...</div>
          </div>
          
          <button class="carousel-btn carousel-next" onclick="carouselNext()">&#8250;</button>
        </div>
        <div class="carousel-dots" id="carouselDots"></div>
      </div>
    </div>
  </section>

  <!-- COMMUNITIES SECTION -->
  <section class="section-spacing">
    <div class="container-wide">
      <div class="section-header" style="border:none;">
        <h2><i class="fa-solid fa-users" style="color:var(--primary); margin-right:10px;"></i> Komunitas Terpopuler</h2>
        <a href="communities/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <?php if (mysqli_num_rows($communities) > 0): ?>
        <div class="grid-bento">
          <?php while ($c = mysqli_fetch_assoc($communities)): ?>
            <?php $foto = !empty($c['foto']) ? $c['foto'] : 'default_community.png'; ?>
            <a href="communities/detail.php?id=<?= $c['id_community']; ?>" class="card-komunitas">
              <img src="uploads/communities/<?= htmlspecialchars($foto); ?>" class="card-img" alt="<?= htmlspecialchars($c['nama_community']); ?>">
              <div class="card-body">
                <span class="badge"><?= htmlspecialchars($c['kategori']); ?></span>
                <h3><?= htmlspecialchars($c['nama_community']); ?></h3>
                <p><?= htmlspecialchars(substr($c['deskripsi'], 0, 100)); ?>...</p>
              </div>
            </a>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p class="empty-message">Belum ada komunitas tersedia.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- EVENTS SECTION -->
  <section class="section-spacing bg-light">
    <div class="container-wide">
      <div class="section-header" style="border:none;">
        <h2><i class="fa-solid fa-calendar-days" style="color:var(--primary); margin-right:10px;"></i> Event Mendatang</h2>
        <a href="events/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <?php if (mysqli_num_rows($events) > 0): ?>
        <div class="grid">
          <?php while ($e = mysqli_fetch_assoc($events)): ?>
            <div class="card">
              <span class="badge"><?= htmlspecialchars($e['nama_community'] ?? 'Event Umum'); ?></span>
              <h3 style="margin-top:10px;"><?= htmlspecialchars($e['nama_event']); ?></h3>
              <div class="event-info" style="margin: 16px 0; display:flex; flex-direction:column; gap:8px; color:var(--text-secondary); font-size:0.95rem;">
                <div><i class="fa-solid fa-calendar" style="width:20px; color:var(--primary);"></i> <?= date('d M Y', strtotime($e['tanggal'])); ?></div>
                <div><i class="fa-solid fa-clock" style="width:20px; color:var(--primary);"></i> <?= htmlspecialchars($e['jam']); ?></div>
                <div><i class="fa-solid fa-location-dot" style="width:20px; color:var(--primary);"></i> <?= htmlspecialchars($e['lokasi']); ?></div>
              </div>
              <a href="events/detail.php?id=<?= $e['id_event']; ?>" class="btn btn-outline-primary btn-block">
                Lihat Detail
              </a>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p class="empty-message">Belum ada event yang akan datang.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- CTA SECTION -->
  <section class="cta-wrapper reveal">
    <div class="cta-card">
      <div class="cta-content">
        <h2>Siap Untuk Bergabung?</h2>
        <p style="color: rgba(255,255,255,0.9);">Jangan lewatkan kesempatan untuk mengembangkan diri, memperluas koneksi, dan bersenang-senang di masa kuliah. Buat akunmu sekarang dan mulai jelajahi ITPLN Connect.</p>
        <a href="auth/register.php" class="btn btn-lg" style="background: var(--accent); color: var(--text); padding: 14px 32px; font-size:1.1rem; border: var(--border-width) solid var(--border); box-shadow: var(--shadow);">
          Daftar Gratis Sekarang <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

    </div> <!-- End of feed-content -->
  </div> <!-- End of feed-container -->

</main>

<?php include "includes/footer.php"; ?>

<script>
// Carousel data from PHP
const posts = <?= json_encode(array_map(function($p) {
    return [
        'title' => $p['title'] ?? '',
        'content' => $p['content'],
        'foto' => $p['foto'] ? 'uploads/posts/' . $p['foto'] : null,
        'foto2' => $p['foto2'] ? 'uploads/posts/' . $p['foto2'] : null,
        'foto3' => $p['foto3'] ? 'uploads/posts/' . $p['foto3'] : null,
        'community' => $p['nama_community'],
        'id_community' => $p['id_community'],
        'user' => $p['nama_user'],
        'foto_user' => $p['foto_user'] ? 'uploads/profiles/' . $p['foto_user'] : 'uploads/profiles/default.png'
    ];
}, mysqli_fetch_all($carouselPosts, MYSQLI_ASSOC))) ?>;

let currentSlide = 0;
let autoSlideTimer = null;

function renderCarousel() {
  if (posts.length === 0) {
    document.getElementById('carouselSlide').innerHTML = '<p class="empty-message">Belum ada postingan.</p>';
    return;
  }
  
  const post = posts[currentSlide];
  const slide = document.getElementById('carouselSlide');
  
  let photosHtml = '';
  if (post.foto || post.foto2 || post.foto3) {
    photosHtml = '<div class="carousel-photos">';
    if (post.foto) photosHtml += `<img src="${post.foto}" alt="Post" class="carousel-img">`;
    if (post.foto2) photosHtml += `<img src="${post.foto2}" alt="Post" class="carousel-img">`;
    if (post.foto3) photosHtml += `<img src="${post.foto3}" alt="Post" class="carousel-img">`;
    photosHtml += '</div>';
  }
  
  slide.innerHTML = `
    <div class="carousel-post">
      <div class="post-header">
        <img src="${post.foto_user}" class="post-avatar" alt="User">
        <div>
          <b style="font-size:1.05rem; color: var(--text);">${escapeHtml(post.user)}</b>
          <small class="text-muted" style="display:block;">di <span style="color:var(--primary); font-weight:700;">${escapeHtml(post.community)}</span></small>
        </div>
      </div>
      
      <div class="post-body">
        ${post.title ? `<h3 style="margin-bottom:12px; color: var(--text);">${escapeHtml(post.title)}</h3>` : ''}
        <p style="color:var(--text-secondary); line-height:1.7; margin-bottom: 20px;">${escapeHtml(post.content).replace(/\n/g, '<br>')}</p>
        
        ${photosHtml}
      </div>
      
      <div style="text-align: center; margin-top: 32px;">
        <a href="auth/login.php" class="btn btn-primary" style="padding: 10px 24px;">
          Masuk untuk interaksi <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>
  `;
  
  const dots = document.getElementById('carouselDots');
  if (dots) {
    dots.innerHTML = posts.map((_, idx) => 
      `<span class="carousel-dot ${idx === currentSlide ? 'active' : ''}" onclick="goToSlide(${idx})"></span>`
    ).join('');
  }
}

function carouselNext() {
  currentSlide = (currentSlide + 1) % posts.length;
  renderCarousel();
  resetAutoSlide();
}

function carouselPrev() {
  currentSlide = (currentSlide - 1 + posts.length) % posts.length;
  renderCarousel();
  resetAutoSlide();
}

function goToSlide(idx) {
  currentSlide = idx;
  renderCarousel();
  resetAutoSlide();
}

function startAutoSlide() {
  autoSlideTimer = setInterval(carouselNext, 5000);
}

function resetAutoSlide() {
  clearInterval(autoSlideTimer);
  startAutoSlide();
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text || '';
  return div.innerHTML;
}

// Init
if (posts.length > 0) {
  renderCarousel();
  startAutoSlide();
}

// Sidebar toggle for mobile
function toggleSidebar() {
  const content = document.getElementById('sidebarContent');
  const btn = document.getElementById('sidebarToggle');
  content.classList.toggle('open');
  btn.classList.toggle('active');
}
</script>

</body>
</html>
