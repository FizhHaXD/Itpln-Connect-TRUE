<?php
session_start();
include "../config/database.php";

$today = date("Y-m-d");

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nama = $_SESSION['nama'] ?? 'User';

// Mengambil Event yang diikuti
$events_q = mysqli_query($conn, "
  SELECT e.id_event as id, e.nama_event as judul, e.tanggal, e.jam, e.lokasi, c.nama_community as konteks, 'event' as tipe_agenda
  FROM event_participants ep
  JOIN events e ON ep.id_event = e.id_event
  JOIN communities c ON e.id_community = c.id_community
  WHERE ep.id_user = $id_user AND e.tanggal >= CURDATE()
");

// Mengambil Sesi Belajar yang diikuti
$study_q = mysqli_query($conn, "
  SELECT sg.id as id, sg.judul, DATE(sg.jadwal) as tanggal, TIME(sg.jadwal) as jam, sg.lokasi, sg.mata_kuliah as konteks, 'study' as tipe_agenda
  FROM study_group_members sgm
  JOIN study_groups sg ON sgm.id_group = sg.id
  WHERE sgm.id_user = $id_user AND DATE(sg.jadwal) >= CURDATE()
");

$all_agenda = [];
if($events_q) while($row = mysqli_fetch_assoc($events_q)) $all_agenda[] = $row;
if($study_q) while($row = mysqli_fetch_assoc($study_q)) $all_agenda[] = $row;

// Urutkan agenda berdasarkan waktu terdekat
usort($all_agenda, function($a, $b) {
    $datetimeA = strtotime($a['tanggal'] . ' ' . $a['jam']);
    $datetimeB = strtotime($b['tanggal'] . ' ' . $b['jam']);
    return $datetimeA <=> $datetimeB;
});

// Materi Terkini (5 file terbaru)
$recent_materials = mysqli_query($conn, "
  SELECT id, nama_file, file_path, ukuran_file, mata_kuliah, uploaded_at, id_group
  FROM study_materials
  ORDER BY uploaded_at DESC
  LIMIT 4
");

// jumlah komunitas diikuti
$qCommunity = mysqli_query($conn,
  "SELECT COUNT(*) AS total 
   FROM community_members 
   WHERE id_user = $id_user"
);
$totalCommunity = mysqli_fetch_assoc($qCommunity)['total'];

// jumlah event diikuti
$qEvent = mysqli_query($conn,
  "SELECT COUNT(*) AS total 
   FROM event_participants 
   WHERE id_user = $id_user"
);
$totalEvent = mysqli_fetch_assoc($qEvent)['total'];

// agenda terdekat (gabungan event / sesi belajar)
$nextAgenda = !empty($all_agenda) ? $all_agenda[0] : null;

// komunitas yang diikuti (untuk quick view)
$myCommunities = mysqli_query($conn,
  "SELECT c.nama_community, c.kategori
   FROM community_members cm
   JOIN communities c ON cm.id_community = c.id_community
   WHERE cm.id_user = $id_user
   LIMIT 3"
);

// Tips array
$tips = [
  "Bergabung ke komunitas untuk menemukan event menarik!",
  "Cek agenda rutin agar tidak ketinggalan event.",
  "Ajak teman untuk ikut komunitas bersama.",
  "Event hari ini ditandai dengan badge hijau.",
  "Semakin aktif, semakin banyak koneksi yang kamu bangun!"
];
$randomTip = $tips[array_rand($tips)];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - ITPLN Connect</title>
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <!-- Welcome Card -->
  <div class="card welcome-card">
    <h2>Halo, <?= htmlspecialchars($nama); ?>!</h2>
    <p>Selamat datang di dashboard komunitas kampus</p>
  </div>
  
  <!-- CAROUSEL: Postingan Eksternal -->
  <div class="carousel-container">
    <h2><i class="fa-solid fa-bullhorn" style="margin-right:10px;"></i>Postingan Terbaru</h2>
    
    <div class="carousel-wrapper">
      <button class="carousel-btn carousel-prev" onclick="carouselPrev()">&#8249;</button>
      
      <div class="carousel-slide" id="carouselSlide">
        <div class="carousel-loading">Memuat postingan...</div>
      </div>
      
      <button class="carousel-btn carousel-next" onclick="carouselNext()">&#8250;</button>
    </div>
    
    <div class="carousel-dots" id="carouselDots"></div>
  </div>

  <!-- Layout 2 Kolom -->
  <div class="grid-2">
    <!-- Kolom Kiri -->
    <div class="col-main">
      <!-- Ringkasan Stats -->
      <div class="card">
        <h3><i class="fa-solid fa-chart-bar" style="color:var(--primary); margin-right:8px;"></i>Ringkasan Aktivitas</h3>
        <div class="summary-stats">
          <div class="stat-item">
            <div class="number"><?= $totalCommunity; ?></div>
            <div class="label">Komunitas Diikuti</div>
          </div>
          <div class="stat-item">
            <div class="number"><?= $totalEvent; ?></div>
            <div class="label">Event Diikuti</div>
          </div>
        </div>

        <hr>

        <h4><i class="fa-solid fa-star" style="color:var(--primary); margin-right:8px;"></i>Agenda Terdekat</h4>
        <?php if ($nextAgenda): ?>
          <div class="next-event">
            <b><?= htmlspecialchars($nextAgenda['judul']); ?></b>
            <span class="badge" style="font-size:0.75rem; margin-left:6px; background: <?= $nextAgenda['tipe_agenda']=='event' ? '#dbeafe; color:#1e40af;' : '#f3e8ff; color:#6b21a8;' ?>">
              <?= $nextAgenda['tipe_agenda']=='event' ? 'Event' : 'Sesi Belajar' ?>
            </span>
            <span class="event-time" style="display:block; margin-top:6px;">
              <i class="fa-solid fa-calendar" style="margin-right:4px;"></i><?= date('d M Y', strtotime($nextAgenda['tanggal'])); ?>
              &nbsp;|&nbsp;
              <i class="fa-solid fa-clock" style="margin-right:4px;"></i><?= date('H:i', strtotime($nextAgenda['jam'])); ?> WIB
            </span>
          </div>
        <?php else: ?>
          <p class="empty-message">Tidak ada agenda terdekat. Yuk cari event atau sesi belajar!</p>
        <?php endif; ?>
      </div>

      <!-- Agenda -->
      <div class="card">
        <h3><i class="fa-solid fa-list-check" style="color:var(--primary); margin-right:8px;"></i>Agenda Kamu</h3>
        <?php if (empty($all_agenda)): ?>
          <p class="empty-message">Belum ada agenda. Yuk ikut event atau sesi belajar!</p>
        <?php else: ?>
          <div style="display:flex; flex-direction:column; gap:12px; margin-top:16px;">
          <?php foreach ($all_agenda as $a) : ?>
            <div class="agenda-item <?= ($a['tanggal'] == $today) ? 'today' : ''; ?>" style="border-left: 4px solid <?= $a['tipe_agenda']=='event' ? '#3b82f6' : '#a855f7' ?>;">
              <div class="agenda-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                  <span class="badge" style="font-size:0.7rem; margin-bottom:4px; display:inline-block; background: <?= $a['tipe_agenda']=='event' ? '#dbeafe; color:#1e40af;' : '#f3e8ff; color:#6b21a8;' ?>">
                    <i class="fa-solid <?= $a['tipe_agenda']=='event' ? 'fa-calendar-days' : 'fa-book-open' ?>"></i> <?= $a['tipe_agenda']=='event' ? 'Event' : 'Sesi Belajar' ?>
                  </span>
                  <b style="display:block; font-size:1.05rem;"><a href="<?= $a['tipe_agenda']=='event' ? '../events/detail.php?id='.$a['id'] : '../study/detail.php?id='.$a['id'] ?>" style="color:var(--text); text-decoration:none;"><?= htmlspecialchars($a['judul']); ?></a></b>
                </div>
                <?php if ($a['tanggal'] == $today): ?>
                  <span class="badge-today" style="flex-shrink:0;">Hari ini</span>
                <?php endif; ?>
              </div>
              <small class="community-name" style="color:var(--text-muted); display:block; margin-bottom:8px;"><?= htmlspecialchars($a['konteks']); ?></small>
              <div class="agenda-details" style="font-size:0.9rem; color:var(--text-secondary); display:flex; gap:12px; flex-wrap:wrap;">
                <span><i class="fa-solid fa-calendar" style="color:var(--primary);"></i> <?= date('d M Y', strtotime($a['tanggal'])); ?></span>
                <span><i class="fa-solid fa-clock" style="color:var(--primary);"></i> <?= date('H:i', strtotime($a['jam'])); ?></span>
                <span><i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> <?= htmlspecialchars($a['lokasi']); ?></span>
              </div>
            </div>
          <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Kolom Kanan (Sidebar) -->
    <div class="col-side">
      <!-- Quick Actions -->
      <div class="card">
        <h3><i class="fa-solid fa-bolt" style="color:var(--primary); margin-right:8px;"></i>Aksi Cepat</h3>
        <div class="quick-actions">
          <a class="btn btn-block" href="../communities/index.php">
            <i class="fa-solid fa-users"></i> Cari Komunitas
          </a>
          <a class="btn btn-block btn-success" href="../study/index.php">
            <i class="fa-solid fa-users-rectangle"></i> Cari Sesi Belajar
          </a>
          <a class="btn btn-block btn-outline-primary" href="../study/materials.php">
            <i class="fa-solid fa-folder-open"></i> Bank Materi
          </a>
          <a class="btn btn-block btn-secondary" href="../profile/index.php">
            <i class="fa-solid fa-user-pen"></i> Edit Profil
          </a>
        </div>
      </div>

      <!-- Komunitas Saya -->
      <div class="card">
        <h3><i class="fa-solid fa-users" style="color:var(--primary); margin-right:8px;"></i>Komunitas Saya</h3>
        <?php if (mysqli_num_rows($myCommunities) == 0): ?>
          <p class="empty-message">Belum bergabung komunitas</p>
        <?php else: ?>
          <?php while ($c = mysqli_fetch_assoc($myCommunities)) : ?>
            <div class="mini-card">
              <b><?= htmlspecialchars($c['nama_community']); ?></b>
              <span class="badge"><?= htmlspecialchars($c['kategori']); ?></span>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <!-- Materi Terkini -->
      <div class="card">
        <h3 style="margin-bottom:16px;"><i class="fa-solid fa-book-open" style="color:#10b981; margin-right:8px;"></i>Materi Terkini</h3>
        <?php if (mysqli_num_rows($recent_materials) == 0): ?>
          <p class="empty-message">Belum ada materi dibagikan.</p>
        <?php else: ?>
          <div style="display:flex; flex-direction:column; gap:10px;">
            <?php while ($m = mysqli_fetch_assoc($recent_materials)): ?>
              <div style="display:flex; align-items:center; gap:10px; padding:10px; background:var(--bg); border:1px solid var(--border); border-radius:8px; width: 100%; box-sizing: border-box; min-width: 0;">
                <div style="color:#ef4444; font-size:1.5rem;"><i class="fa-solid fa-file-pdf"></i></div>
                <div style="flex-grow:1; min-width:0;">
                  <b style="display:block; font-size:0.9rem; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="<?= htmlspecialchars($m['nama_file']) ?>"><?= htmlspecialchars($m['nama_file']) ?></b>
                  <small style="color:var(--text-muted); display:block; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= htmlspecialchars($m['mata_kuliah'] ?? 'Global') ?></small>
                </div>
                <a href="../uploads/materials/<?= htmlspecialchars($m['file_path']) ?>" download="<?= htmlspecialchars($m['nama_file']) ?>" class="btn btn-sm" style="background:#eef2ff; color:#4f46e5; border:1px solid #c7d2fe; padding:6px 10px;" title="Download">
                  <i class="fa-solid fa-download"></i>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
          <a href="../study/materials.php" class="btn btn-sm btn-block btn-outline-primary" style="margin-top:12px;">Lihat Bank Materi <i class="fa-solid fa-arrow-right"></i></a>
        <?php endif; ?>
      </div>

      <!-- Tips -->
      <div class="card tip-card">
        <h3><i class="fa-solid fa-lightbulb" style="margin-right:8px;"></i>Tips Hari Ini</h3>
        <p class="tip-text"><?= $randomTip; ?></p>
      </div>
    </div>
  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

<script src="../assets/js/dashboard.js"></script>

</body>
</html>
