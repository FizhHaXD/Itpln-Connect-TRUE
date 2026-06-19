<?php
session_start();
include "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;

// Filter & Search
$filter_tipe   = isset($_GET['tipe']) && in_array($_GET['tipe'], ['belajar_bersama','open_mentoring']) ? $_GET['tipe'] : '';
$filter_status = isset($_GET['status']) && in_array($_GET['status'], ['open','penuh','selesai','']) ? $_GET['status'] : 'open';
$search        = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = "WHERE 1=1";
$params = [];

if ($filter_tipe)   $where .= " AND sg.tipe = '$filter_tipe'";
if ($filter_status) $where .= " AND sg.status = '$filter_status'";
if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (sg.judul LIKE '%$s%' OR sg.mata_kuliah LIKE '%$s%')";
}

// Auto-update status 'selesai' jika jadwal sudah lewat
mysqli_query($conn, "UPDATE study_groups SET status='selesai' WHERE jadwal < NOW() AND status='open'");

// Ambil sesi belajar dengan jumlah anggota
$query = "
    SELECT sg.*, 
           u.nama AS nama_creator, u.foto AS foto_creator,
           COUNT(sgm.id) AS jumlah_anggota
    FROM study_groups sg
    JOIN users u ON sg.id_creator = u.id_user
    LEFT JOIN study_group_members sgm ON sg.id = sgm.id_group
    $where
    GROUP BY sg.id
    ORDER BY sg.jadwal ASC
";

$groups = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelompok Belajar - ITPLN Connect</title>
  <meta name="description" content="Temukan atau buat kelompok belajar dan sesi mentoring bersama mahasiswa ITPLN.">
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
<div class="container">

  <!-- PAGE HEADER -->
  <div class="page-header">
    <div>
      <h2><i class="fa-solid fa-book-open" style="color:var(--primary); margin-right:10px;"></i>Kelompok Belajar</h2>
      <p style="color:var(--text-secondary); margin-top:4px; font-size:0.95rem;">Temukan partner belajar atau bagikan ilmumu lewat sesi mentoring.</p>
    </div>
    <div style="display:flex; gap:10px;">
      <a href="materials.php" class="btn" style="background:var(--bg); border:2px solid var(--primary); color:var(--primary);"><i class="fa-solid fa-folder-tree"></i> Bank Materi</a>
      <?php if ($id_user): ?>
        <a href="create.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Buat Sesi</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- FILTER BAR -->
  <form method="GET" class="study-filter-bar">
    <div class="study-search-wrap">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" name="q" class="study-search-input" placeholder="Cari mata kuliah atau judul..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
    </div>
    <div class="study-filter-group">
      <select name="tipe" class="study-select">
        <option value="">Semua Tipe</option>
        <option value="belajar_bersama"  <?= $filter_tipe == 'belajar_bersama'  ? 'selected' : '' ?>>Belajar Bersama</option>
        <option value="open_mentoring"   <?= $filter_tipe == 'open_mentoring'   ? 'selected' : '' ?>>Open Mentoring</option>
      </select>
      <select name="status" class="study-select">
        <option value="open"    <?= $filter_status == 'open'    ? 'selected' : '' ?>>Masih Open</option>
        <option value="penuh"   <?= $filter_status == 'penuh'   ? 'selected' : '' ?>>Sesi Penuh</option>
        <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Sudah Selesai</option>
        <option value=""        <?= $filter_status == ''        ? 'selected' : '' ?>>Semua Status</option>
      </select>
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search || $filter_tipe || $filter_status !== 'open'): ?>
        <a href="index.php" class="btn"><i class="fa-solid fa-xmark"></i> Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- RESULTS -->
  <?php if (mysqli_num_rows($groups) == 0): ?>
    <div class="empty-state">
      <div class="empty-state-icon"><i class="fa-solid fa-users-slash"></i></div>
      <h3>Belum Ada Sesi</h3>
      <p>Tidak ada sesi yang sesuai filter kamu. Coba reset filter atau buat sesi baru!</p>
      <?php if ($id_user): ?>
        <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Sesi Pertama</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-primary">Masuk untuk Membuat Sesi</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="study-grid">
      <?php while ($g = mysqli_fetch_assoc($groups)): ?>
        <?php
          $isMentor = ($g['tipe'] === 'open_mentoring');
          $isFull   = ($g['jumlah_anggota'] >= $g['maks_anggota']);
          $isSelesai = ($g['status'] === 'selesai');
          $foto = !empty($g['foto_creator']) ? '../uploads/profiles/'.$g['foto_creator'] : '../uploads/profiles/default.png';
        ?>
        <a href="detail.php?id=<?= $g['id'] ?>" class="study-card <?= $isSelesai ? 'study-card--selesai' : '' ?>">
          <div class="study-card-header">
            <span class="study-badge <?= $isMentor ? 'study-badge--mentor' : 'study-badge--belajar' ?>">
              <i class="fa-solid <?= $isMentor ? 'fa-chalkboard-user' : 'fa-people-group' ?>"></i>
              <?= $isMentor ? 'Open Mentoring' : 'Belajar Bersama' ?>
            </span>
            <span class="study-status-badge study-status--<?= $g['status'] ?>">
              <?php if ($g['status'] == 'open'): ?><i class="fa-solid fa-circle-dot"></i> Open
              <?php elseif ($g['status'] == 'penuh'): ?><i class="fa-solid fa-circle-xmark"></i> Penuh
              <?php else: ?><i class="fa-solid fa-circle-check"></i> Selesai
              <?php endif; ?>
            </span>
          </div>

          <div class="study-card-body">
            <div class="study-matkul">
              <i class="fa-solid fa-book"></i> <?= htmlspecialchars($g['mata_kuliah']) ?>
            </div>
            <h3 class="study-card-title"><?= htmlspecialchars($g['judul']) ?></h3>
            <p class="study-card-desc"><?= htmlspecialchars(mb_substr($g['deskripsi'], 0, 100)) ?>...</p>
          </div>

          <div class="study-card-footer">
            <div class="study-meta-row">
              <span><i class="fa-solid fa-calendar-days"></i> <?= date('d M Y', strtotime($g['jadwal'])) ?></span>
              <span><i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($g['jadwal'])) ?> WIB</span>
            </div>
            <div class="study-meta-row">
              <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars(mb_substr($g['lokasi'], 0, 40)) ?></span>
              <span class="study-slot <?= $isFull ? 'study-slot--full' : '' ?>">
                <i class="fa-solid fa-users"></i> <?= $g['jumlah_anggota'] ?>/<?= $g['maks_anggota'] ?>
              </span>
            </div>
            <div class="study-creator">
              <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($g['nama_creator']) ?>" class="study-creator-avatar" onerror="this.src='../uploads/profiles/default.png'">
              <span>Oleh <b><?= htmlspecialchars($g['nama_creator']) ?></b></span>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
