<?php
session_start();
include "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;
$isAdmin = false;
if ($id_user) {
    $roleQ = mysqli_query($conn, "SELECT role FROM users WHERE id_user=$id_user");
    if ($r = mysqli_fetch_assoc($roleQ)) {
        $isAdmin = ($r['role'] === 'admin');
    }
}

// Get Master templates from DB
$master_matkul = [];
$master_q = mysqli_query($conn, "SELECT nama_matkul FROM master_matkul ORDER BY nama_matkul ASC");
while ($r = mysqli_fetch_assoc($master_q)) {
    $master_matkul[] = $r['nama_matkul'];
}

// Get materials count per subject
$counts_query = mysqli_query($conn, "
    SELECT mata_kuliah, COUNT(id) as jumlah 
    FROM study_materials 
    WHERE mata_kuliah IS NOT NULL AND mata_kuliah != ''
    GROUP BY mata_kuliah
");
$counts = [];
while ($row = mysqli_fetch_assoc($counts_query)) {
    $counts[$row['mata_kuliah']] = $row['jumlah'];
}

// Merge counts into master list
foreach ($counts as $mk => $jml) {
    if (!in_array($mk, $master_matkul)) {
        $master_matkul[] = $mk;
    }
}
sort($master_matkul);

$selected_matkul = isset($_GET['matkul']) ? trim($_GET['matkul']) : '';
$materials_query = null;

if ($selected_matkul) {
    $s = mysqli_real_escape_string($conn, $selected_matkul);
    $materials_query = mysqli_query($conn, "
        SELECT sm.*, u.nama AS pengunggah, sg.judul AS judul_sesi
        FROM study_materials sm
        LEFT JOIN study_groups sg ON sm.id_group = sg.id
        JOIN users u ON sm.id_user = u.id_user
        WHERE sm.mata_kuliah = '$s'
        ORDER BY sm.uploaded_at DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bank Materi - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="../assets/css/study.css?v=<?= time() ?>">
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

  <!-- Navigasi -->
  <div class="nav" style="margin-bottom: 20px; display:flex; gap:10px;">
    <a class="btn" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Kelompok Belajar</a>
    <?php if ($selected_matkul): ?>
      <a class="btn" href="materials.php"><i class="fa-solid fa-folder-tree"></i> Semua Folder Matkul</a>
    <?php endif; ?>
  </div>

  <div class="study-card-main study-card-main-pad">
    <h2 style="margin-bottom:10px; color:#4f46e5;"><i class="fa-solid fa-book-bookmark"></i> Bank Materi Global</h2>
    <p style="color:var(--text-secondary); margin-bottom:30px;">Kumpulan seluruh materi resmi dari kampus dan materi sesi belajar mahasiswa berdasarkan Mata Kuliah.</p>

    <?php if (!$selected_matkul): ?>
      <!-- TAMPILAN FOLDER MATA KULIAH -->
      <div class="folder-grid">
        <?php foreach ($master_matkul as $mk): 
          $jml = $counts[$mk] ?? 0;
        ?>
          <a href="materials.php?matkul=<?= urlencode($mk) ?>" class="folder-card">
            <i class="fa-solid fa-folder<?= $jml > 0 ? '-open' : '' ?> folder-icon" style="color: <?= $jml > 0 ? '#4f46e5' : '#9ca3af' ?>;"></i>
            <b style="font-size: 1.1rem;"><?= htmlspecialchars($mk) ?></b>
            <span class="badge" style="background:#e5e7eb; color:#374151;"><?= $jml ?> file materi</span>
          </a>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <!-- TAMPILAN DAFTAR MATERI DALAM SATU MATA KULIAH -->
      <h3 style="margin-bottom:20px; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-folder-open" style="color:#f59e0b;"></i>
        Materi: <?= htmlspecialchars($selected_matkul) ?>
      </h3>
      
      <?php if (isset($_GET['materi_success'])): ?>
        <div class="alert alert-success" style="margin-bottom:16px;"><i class="fa-solid fa-check"></i> Materi global berhasil diunggah!</div>
      <?php endif; ?>
      <?php if (isset($_GET['delete_success'])): ?>
        <div class="alert alert-success" style="margin-bottom:16px;"><i class="fa-solid fa-trash"></i> Materi berhasil dihapus!</div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="margin-bottom:16px;"><i class="fa-solid fa-triangle-exclamation"></i> Gagal! Pastikan file <= 10MB dan format didukung.</div>
      <?php endif; ?>

      <?php if (!$id_user): ?>
        <div class="alert alert-info" style="margin-bottom:20px;">
          <i class="fa-solid fa-lock"></i> Anda harus <a href="<?= BASE_URL ?>auth/login.php?need_login=1&return=<?= urlencode('study/materials.php?matkul='.$selected_matkul) ?>">Login</a> untuk mengunduh materi.
        </div>
      <?php endif; ?>
      
      <?php if ($isAdmin): ?>
        <div class="upload-admin-box">
          <h4 style="margin-bottom:12px; color:#166534;"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Materi Kampus (Admin)</h4>
          <form action="upload_global_materi.php" method="POST" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="hidden" name="mata_kuliah" value="<?= htmlspecialchars($selected_matkul) ?>">
            <input type="file" name="materi_file" style="flex-grow:1; max-width:300px;" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-upload"></i> Upload Global</button>
          </form>
          <small style="color:#166534; display:block; margin-top:5px;">Materi ini akan ditandai sebagai materi resmi kampus, bukan dari grup belajar mahasiswa.</small>
        </div>
      <?php endif; ?>

      <?php if (mysqli_num_rows($materials_query) == 0): ?>
        <div style="text-align:center; padding: 40px; border: 2px dashed var(--border); border-radius:12px;">
          <i class="fa-solid fa-file-excel" style="font-size:3rem; color:var(--text-muted); margin-bottom:10px;"></i>
          <p style="color:var(--text-muted); margin:0;">Tidak ada materi untuk mata kuliah ini.</p>
        </div>
      <?php else: ?>
        <div class="material-file-list">
          <?php while ($m = mysqli_fetch_assoc($materials_query)): ?>
            <div class="material-file-item">
              <div class="material-file-icon"><i class="fa-solid fa-file-lines"></i></div>
              <div style="flex-grow:1; min-width:0;">
                <b style="display:block; font-size:1.1rem; color:var(--text); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                  <?= htmlspecialchars($m['nama_file']) ?>
                  <?php if (!$m['id_group']): ?>
                    <span class="badge" style="background:#dcfce7; color:#166534; font-size:0.7rem; margin-left:5px; border:1px solid #166534;"><i class="fa-solid fa-university"></i> Materi Resmi</span>
                  <?php endif; ?>
                </b>
                <div style="color:var(--text-muted); font-size:0.9rem; margin-top:4px;">
                  Diunggah oleh <b><?= htmlspecialchars($m['pengunggah']) ?></b> 
                  <?php if ($m['id_group']): ?>
                    pada sesi <i><?= htmlspecialchars($m['judul_sesi']) ?></i>
                  <?php endif; ?>
                </div>
                <small style="color:var(--text-secondary);"><?= date('d M Y', strtotime($m['uploaded_at'])) ?> &bull; <?= round($m['ukuran_file']/1024/1024, 2) ?> MB</small>
              </div>
              <div style="display:flex; gap:10px;">
                <?php if ($id_user): ?>
                  <a href="../uploads/materials/<?= htmlspecialchars($m['file_path']) ?>" download="<?= htmlspecialchars($m['nama_file']) ?>" class="btn btn-primary" style="padding:10px 20px; border-radius:8px;">
                    <i class="fa-solid fa-download"></i> Download
                  </a>
                <?php else: ?>
                  <button class="btn btn-primary" disabled style="opacity:0.5; cursor:not-allowed;"><i class="fa-solid fa-download"></i> Download</button>
                <?php endif; ?>
                
                <?php if ($isAdmin || $id_user == $m['id_user']): ?>
                  <form method="POST" action="delete_materi.php" onsubmit="return confirm('Hapus materi ini?')" style="display:inline;">
                    <input type="hidden" name="id_materi" value="<?= $m['id'] ?>">
                    <input type="hidden" name="return_to_global" value="<?= htmlspecialchars($selected_matkul) ?>">
                    <button type="submit" class="btn btn-danger" style="padding:10px; border-radius:8px;"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
