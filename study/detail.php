<?php
session_start();
include "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) { header("Location: index.php"); exit(); }

// Ambil data sesi
$group = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT sg.*, 
           u.nama AS nama_creator, u.foto AS foto_creator,
           COUNT(sgm.id) AS jumlah_anggota
    FROM study_groups sg
    JOIN users u ON sg.id_creator = u.id_user
    LEFT JOIN study_group_members sgm ON sg.id = sgm.id_group
    WHERE sg.id = $id
    GROUP BY sg.id
"));

if (!$group) { header("Location: index.php"); exit(); }

// Cek apakah user sudah join dan periksa rolenya
$isJoined = false;
$isAdmin = false;
if ($id_user) {
    $cek = mysqli_query($conn, "SELECT 1 FROM study_group_members WHERE id_group=$id AND id_user=$id_user");
    $isJoined = mysqli_num_rows($cek) > 0;
    
    $roleQ = mysqli_query($conn, "SELECT role FROM users WHERE id_user=$id_user");
    $rData = mysqli_fetch_assoc($roleQ);
    if ($rData && $rData['role'] === 'admin') $isAdmin = true;
}

// Ambil daftar anggota
$members = mysqli_query($conn, "
    SELECT u.id_user, u.nama, u.foto
    FROM study_group_members sgm
    JOIN users u ON sgm.id_user = u.id_user
    WHERE sgm.id_group = $id
    ORDER BY sgm.joined_at ASC
");

// Ambil daftar materi
$materials = mysqli_query($conn, "
    SELECT sm.*, u.nama AS pengunggah 
    FROM study_materials sm 
    JOIN users u ON sm.id_user = u.id_user 
    WHERE sm.id_group = $id 
    ORDER BY sm.uploaded_at DESC
");

$isFull    = ($group['jumlah_anggota'] >= $group['maks_anggota']);
$isCreator = ($id_user == $group['id_creator']);
$isSelesai = ($group['status'] === 'selesai');
$isMentor  = ($group['tipe'] === 'open_mentoring');

$fotoCreator = !empty($group['foto_creator'])
    ? '../uploads/profiles/' . $group['foto_creator']
    : '../uploads/profiles/default.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($group['judul']) ?> - Kelompok Belajar ITPLN</title>
  <meta name="description" content="<?= htmlspecialchars(mb_substr($group['deskripsi'], 0, 120)) ?>">
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
  <div class="nav">
    <a class="btn" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>

  <!-- SUCCESS ALERTS -->
  <?php if (isset($_GET['created'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Sesi berhasil dibuat! Sebarkan ke teman-temanmu.</div>
  <?php endif; ?>
  <?php if (isset($_GET['joined'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Berhasil bergabung! Sampai jumpa di sesi belajarnya.</div>
  <?php endif; ?>
  <?php if (isset($_GET['left'])): ?>
    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Kamu sudah keluar dari sesi ini.</div>
  <?php endif; ?>
  <?php if (isset($_GET['full'])): ?>
    <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> Maaf, sesi ini sudah penuh.</div>
  <?php endif; ?>

  <div class="study-detail-wrapper">

    <!-- CARD UTAMA -->
    <div class="study-card-main study-card-main-pad">
      
      <!-- Header badges -->
      <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
        <span class="study-badge-custom <?= $isMentor ? 'study-badge-custom--mentor' : 'study-badge-custom--belajar' ?>">
          <i class="fa-solid <?= $isMentor ? 'fa-chalkboard-user' : 'fa-people-group' ?>"></i>
          <?= $isMentor ? 'Open Mentoring' : 'Belajar Bersama' ?>
        </span>
        <span class="study-badge-custom <?= $group['status'] == 'open' ? 'study-badge-custom--open' : 'study-status-badge study-status--'.$group['status'] ?>">
          <?php if ($group['status'] == 'open'): ?><i class="fa-solid fa-circle-dot"></i> Open
          <?php elseif ($group['status'] == 'penuh'): ?><i class="fa-solid fa-circle-xmark"></i> Penuh
          <?php else: ?><i class="fa-solid fa-circle-check"></i> Selesai
          <?php endif; ?>
        </span>
        <span class="study-badge-custom study-badge-custom--matkul">
          <i class="fa-solid fa-book"></i> <?= htmlspecialchars($group['mata_kuliah']) ?>
        </span>
      </div>

      <h1 style="font-size:1.8rem; font-family:'Plus Jakarta Sans',sans-serif; margin-bottom:16px; color: var(--text);">
        <?= htmlspecialchars($group['judul']) ?>
      </h1>

      <!-- Creator info -->
      <div class="study-creator-info-box">
        <img src="<?= htmlspecialchars($fotoCreator) ?>" alt="<?= htmlspecialchars($group['nama_creator']) ?>"
             onerror="this.src='../uploads/profiles/default.png'">
        <div>
          <small style="color:var(--text-muted); display:block; line-height:1; margin-bottom:4px;"><?= $isMentor ? 'Mentor' : 'Pembuat Sesi' ?></small>
          <b style="display:block; font-size:1.1rem; color:var(--text);"><?= htmlspecialchars($group['nama_creator']) ?></b>
        </div>
        <div style="margin-left:auto; color:var(--text-muted); font-size:0.9rem;">
          Dibuat <?= date('d M Y', strtotime($group['created_at'])) ?>
        </div>
      </div>

      <hr style="border:0; border-top:1px solid var(--text); margin:20px 0;">

      <!-- Description -->
      <h3 style="margin-bottom:10px; display:flex; align-items:center; gap:8px; font-size:1.2rem;">
        <i class="fa-solid fa-align-left" style="color: #4f46e5;"></i> Deskripsi
      </h3>
      <p style="line-height:1.75; color:var(--text-secondary);"><?= nl2br(htmlspecialchars($group['deskripsi'])) ?></p>

      <hr style="border:0; border-top:1px solid var(--text); margin:20px 0;">

      <!-- Schedule & Location GRID -->
      <div class="study-info-grid-row">
        <div class="study-info-box">
          <div class="study-info-icon-box">
            <i class="fa-solid fa-calendar-days"></i>
          </div>
          <div>
            <small style="color:var(--text-muted); display:block; margin-bottom:4px;">Jadwal</small>
            <b style="display:block; color:var(--text);"><?= date('l, d F Y', strtotime($group['jadwal'])) ?></b>
            <span style="color:var(--text-secondary); font-size:0.9rem;"><?= date('H:i', strtotime($group['jadwal'])) ?> WIB</span>
          </div>
        </div>
        <div class="study-info-box">
          <div class="study-info-icon-box">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div>
            <small style="color:var(--text-muted); display:block; margin-bottom:4px;">Lokasi / Link</small>
            <?php if (filter_var($group['lokasi'], FILTER_VALIDATE_URL)): ?>
              <a href="<?= htmlspecialchars($group['lokasi']) ?>" target="_blank" class="btn btn-sm btn-primary" style="margin-top:4px;">
                <i class="fa-solid fa-video"></i> Buka Link
              </a>
            <?php else: ?>
              <b style="display:block; color:var(--text); text-transform:uppercase;"><?= htmlspecialchars($group['lokasi']) ?></b>
            <?php endif; ?>
          </div>
        </div>
        <div class="study-info-box">
          <div class="study-info-icon-box">
            <i class="fa-solid fa-users"></i>
          </div>
          <div style="flex-grow:1;">
            <small style="color:var(--text-muted); display:block; margin-bottom:4px;">Kapasitas</small>
            <b style="display:block; color:var(--text); margin-bottom:4px;"><?= $group['jumlah_anggota'] ?> / <?= $group['maks_anggota'] ?> anggota</b>
            <div class="study-progress-bar">
              <div class="study-progress-fill <?= $isFull ? 'study-progress--full' : '' ?>"
                   style="width:<?= min(100, ($group['jumlah_anggota'] / $group['maks_anggota']) * 100) ?>%"></div>
            </div>
          </div>
        </div>
      </div>

      <hr style="border:0; border-top:1px solid var(--text); margin:20px 0;">

      <!-- MATERI BELAJAR SECTION -->
      <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:20px;">
        
        <div style="flex:1; min-width:300px;">
          <h3 style="margin-bottom:16px; display:flex; align-items:center; gap:8px; font-size:1.2rem;">
            <i class="fa-solid fa-folder-open" style="color:#4f46e5;"></i> Materi Belajar
          </h3>
          
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" style="margin-bottom:16px; padding:10px;"><i class="fa-solid fa-triangle-exclamation"></i> Gagal! Cek format dan ukuran max 10MB.</div>
          <?php endif; ?>
          <?php if (isset($_GET['materi_success'])): ?>
            <div class="alert alert-success" style="margin-bottom:16px; padding:10px;"><i class="fa-solid fa-check"></i> Materi berhasil diunggah!</div>
          <?php endif; ?>
          <?php if (isset($_GET['delete_success'])): ?>
            <div class="alert alert-success" style="margin-bottom:16px; padding:10px;"><i class="fa-solid fa-trash"></i> Materi dihapus!</div>
          <?php endif; ?>

          <?php if (!$isJoined && !$isCreator && !$isAdmin): ?>
            <div class="study-materials-lock">
              <i class="fa-solid fa-lock" style="margin-top:2px;"></i>
              <span style="font-size:0.95rem;">Bergabunglah dengan sesi ini untuk melihat dan mengunduh materi belajar.</span>
            </div>
          <?php else: ?>
            
            <div class="material-file-list">
              <?php if (mysqli_num_rows($materials) == 0): ?>
                <div style="padding: 16px; border: 1px dashed var(--border); border-radius: 8px; text-align:center;">
                  <p style="color:var(--text-muted); margin:0;">Belum ada materi.</p>
                </div>
              <?php else: ?>
                <?php while ($m = mysqli_fetch_assoc($materials)): ?>
                  <div class="material-file-item">
                    <div class="material-file-icon"><i class="fa-solid fa-file-pdf"></i></div>
                    <div style="flex-grow:1; min-width:0;">
                      <b style="display:block; font-size:0.95rem; color:var(--text); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= htmlspecialchars($m['nama_file']) ?></b>
                      <small style="color:var(--text-muted);">Oleh <?= htmlspecialchars($m['pengunggah']) ?> &bull; <?= round($m['ukuran_file']/1024/1024, 2) ?> MB</small>
                    </div>
                    <div style="display:flex; gap:8px;">
                      <a href="../uploads/materials/<?= htmlspecialchars($m['file_path']) ?>" download="<?= htmlspecialchars($m['nama_file']) ?>" class="btn btn-sm" style="background:#eef2ff; color:#4f46e5; border:1px solid #c7d2fe;" title="Download">
                        <i class="fa-solid fa-download"></i>
                      </a>
                      <?php if ($m['id_user'] == $id_user || $isCreator || $isAdmin): ?>
                        <form method="POST" action="delete_materi.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                          <input type="hidden" name="id_materi" value="<?= $m['id'] ?>">
                          <button type="submit" class="btn btn-sm" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca;" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>

            <!-- Form Upload (HANYA KETUA ATAU ADMIN) -->
            <?php if (!$isSelesai && ($isCreator || $isAdmin)): ?>
              <div class="upload-group-box">
                <h4 style="margin-bottom:12px; font-size:0.95rem; display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Materi (Khusus Ketua/Admin)</h4>
                <form method="POST" action="upload_materi.php" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                  <input type="hidden" name="id_group" value="<?= $id ?>">
                  <input type="file" name="materi_file" style="flex-grow:1; max-width:250px; font-size:0.9rem;" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg">
                  <button type="submit" class="btn btn-sm" style="background:#4f46e5; color:white; padding:6px 12px;"><i class="fa-solid fa-upload"></i> Upload</button>
                </form>
              </div>
            <?php endif; ?>

          <?php endif; ?>
        </div>

        <!-- ACTION BUTTONS AREA (Bottom Right equivalent) -->
        <div style="display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
          <?php if (!$id_user): ?>
            <a href="<?= BASE_URL ?>auth/login.php?need_login=1&return=<?= urlencode('study/detail.php?id='.$id) ?>" class="btn btn-success study-action-btn" style="box-shadow: 2px 2px 0 #064e3b;">
              <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk untuk Bergabung
            </a>
          <?php elseif ($isCreator): ?>
            <?php if (!$isSelesai): ?>
              <form method="POST" action="join.php" onsubmit="return confirm('Hapus sesi ini? Semua anggota akan dikeluarkan.')">
                <input type="hidden" name="id_group" value="<?= $id ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger study-action-btn"><i class="fa-solid fa-trash"></i> Hapus Sesi</button>
              </form>
            <?php endif; ?>
          <?php elseif ($isJoined): ?>
            <?php if (!$isSelesai): ?>
              <form method="POST" action="join.php" onsubmit="return confirm('Keluar dari sesi ini?')">
                <input type="hidden" name="id_group" value="<?= $id ?>">
                <input type="hidden" name="action" value="leave">
                <button type="submit" class="btn btn-outline-primary study-action-btn"><i class="fa-solid fa-right-from-bracket"></i> Keluar Sesi</button>
              </form>
            <?php endif; ?>
          <?php elseif ($isSelesai): ?>
            <!-- Do nothing, handled by badge above -->
          <?php elseif ($isFull): ?>
            <!-- Do nothing -->
          <?php else: ?>
            <form method="POST" action="join.php">
              <input type="hidden" name="id_group" value="<?= $id ?>">
              <input type="hidden" name="action" value="join">
              <button type="submit" class="btn study-action-btn study-btn-join">
                <i class="fa-solid fa-handshake"></i> Bergabung Sekarang
              </button>
            </form>
          <?php endif; ?>
        </div>

      </div>
    </div> <!-- END CARD UTAMA -->


    <!-- CARD ANGGOTA (Full Width Bawah) -->
    <div class="study-card-main study-card-main-pad">
      <h3 style="margin-bottom:16px; font-family:'Plus Jakarta Sans',sans-serif; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-users" style="color:#4f46e5;"></i>
        Anggota (<?= $group['jumlah_anggota'] ?>)
      </h3>
      <?php if (mysqli_num_rows($members) == 0): ?>
        <p style="color:var(--text-muted); margin:0;">Belum ada anggota.</p>
      <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:10px;">
          <?php while ($m = mysqli_fetch_assoc($members)):
            $fotoM = !empty($m['foto']) ? '../uploads/profiles/'.$m['foto'] : '../uploads/profiles/default.png';
            $isKetua = ($m['id_user'] == $group['id_creator']);
          ?>
            <div class="study-member-box <?= $isKetua ? 'study-member-box--creator' : '' ?>">
              <img src="<?= htmlspecialchars($fotoM) ?>" alt="<?= htmlspecialchars($m['nama']) ?>"
                   style="width:36px; height:36px; border-radius:50%; border:1px solid var(--border);" onerror="this.src='../uploads/profiles/default.png'">
              <span style="flex-grow:1; color:var(--text); font-weight:500;"><?= htmlspecialchars($m['nama']) ?></span>
              <?php if ($isKetua): ?>
                <span class="study-creator-badge-outline"><?= $isMentor ? 'Mentor' : 'Ketua' ?></span>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
