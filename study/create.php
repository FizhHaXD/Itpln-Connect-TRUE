<?php
session_start();
include "../config/database.php";

// Harus login
if (!isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "auth/login.php?need_login=1&return=" . urlencode("study/create.php"));
    exit();
}

$id_user = $_SESSION['id_user'];
$errors  = [];
$success = false;

// Ambil daftar matkul dari DB
$master_matkul = [];
$master_q = mysqli_query($conn, "SELECT nama_matkul FROM master_matkul ORDER BY nama_matkul ASC");
if ($master_q) {
    while ($r = mysqli_fetch_assoc($master_q)) {
        $master_matkul[] = $r['nama_matkul'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input
    $judul       = trim(mysqli_real_escape_string($conn, $_POST['judul'] ?? ''));
    $mata_kuliah = trim(mysqli_real_escape_string($conn, $_POST['mata_kuliah'] ?? ''));
    $deskripsi   = trim(mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? ''));
    $tipe        = in_array($_POST['tipe'] ?? '', ['belajar_bersama','open_mentoring']) ? $_POST['tipe'] : '';
    $jadwal      = $_POST['jadwal'] ?? '';
    $lokasi      = trim(mysqli_real_escape_string($conn, $_POST['lokasi'] ?? ''));
    $maks        = (int)($_POST['maks_anggota'] ?? 10);

    // Validasi
    if (empty($judul))       $errors[] = "Judul tidak boleh kosong.";
    if (empty($mata_kuliah)) $errors[] = "Nama mata kuliah tidak boleh kosong.";
    if (empty($deskripsi))   $errors[] = "Deskripsi tidak boleh kosong.";
    if (empty($tipe))        $errors[] = "Pilih tipe sesi.";
    if (empty($lokasi))      $errors[] = "Lokasi tidak boleh kosong.";
    if (empty($jadwal))      $errors[] = "Jadwal tidak boleh kosong.";
    elseif (strtotime($jadwal) <= time()) $errors[] = "Jadwal harus di masa depan.";
    if ($maks < 2 || $maks > 50) $errors[] = "Maksimal anggota harus antara 2-50 orang.";

    if (empty($errors)) {
        $jadwal_db = date('Y-m-d H:i:s', strtotime($jadwal));
        $sql = "INSERT INTO study_groups (id_creator, judul, mata_kuliah, deskripsi, tipe, jadwal, lokasi, maks_anggota)
                VALUES ($id_user, '$judul', '$mata_kuliah', '$deskripsi', '$tipe', '$jadwal_db', '$lokasi', $maks)";
        
        if (mysqli_query($conn, $sql)) {
            $new_id = mysqli_insert_id($conn);
            // Otomatis creator masuk sebagai anggota pertama
            mysqli_query($conn, "INSERT INTO study_group_members (id_group, id_user) VALUES ($new_id, $id_user)");
            header("Location: detail.php?id=$new_id&created=1");
            exit();
        } else {
            $errors[] = "Terjadi kesalahan sistem. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Sesi Belajar - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <div class="nav">
    <a class="btn" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>

  <div class="form-card">
    <div class="form-card-header">
      <div class="form-card-icon"><i class="fa-solid fa-plus"></i></div>
      <div>
        <h2>Buat Sesi Belajar</h2>
        <p>Ajak teman belajar bareng atau bagikan ilmumu lewat sesi mentoring.</p>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul style="margin: 8px 0 0 16px; padding: 0;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" class="form-body">

      <!-- TIPE SESI -->
      <div class="form-group">
        <label class="form-label">Tipe Sesi <span class="required">*</span></label>
        <div class="study-tipe-selector">
          <label class="study-tipe-option <?= ($_POST['tipe'] ?? 'belajar_bersama') == 'belajar_bersama' ? 'selected' : '' ?>">
            <input type="radio" name="tipe" value="belajar_bersama" <?= ($_POST['tipe'] ?? 'belajar_bersama') == 'belajar_bersama' ? 'checked' : '' ?> required>
            <div class="study-tipe-icon" style="background: var(--primary);">
              <i class="fa-solid fa-people-group"></i>
            </div>
            <div>
              <b>Belajar Bersama</b>
              <p>Kelompok belajar untuk persiapan ujian, tugas, atau diskusi materi.</p>
            </div>
          </label>
          <label class="study-tipe-option <?= ($_POST['tipe'] ?? '') == 'open_mentoring' ? 'selected' : '' ?>">
            <input type="radio" name="tipe" value="open_mentoring" <?= ($_POST['tipe'] ?? '') == 'open_mentoring' ? 'checked' : '' ?>>
            <div class="study-tipe-icon" style="background: var(--success);">
              <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
              <b>Open Mentoring</b>
              <p>Sesi berbagi ilmu atau pemandu bagi mahasiswa lain yang membutuhkan bimbingan.</p>
            </div>
          </label>
        </div>
      </div>

      <!-- JUDUL -->
      <div class="form-group">
        <label for="judul" class="form-label">Judul Sesi <span class="required">*</span></label>
        <input type="text" id="judul" name="judul" class="form-control"
               placeholder="Contoh: Belajar Bareng UTS Kalkulus 2"
               value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>" required maxlength="150">
      </div>

      <!-- MATA KULIAH -->
      <div class="form-group">
        <label for="mata_kuliah" class="form-label">Mata Kuliah <span class="required">*</span></label>
        <select id="mata_kuliah" name="mata_kuliah" class="form-control" required>
          <option value="">-- Pilih Mata Kuliah --</option>
          <?php foreach ($master_matkul as $mk): ?>
            <option value="<?= htmlspecialchars($mk) ?>" <?= ($_POST['mata_kuliah'] ?? '') == $mk ? 'selected' : '' ?>><?= htmlspecialchars($mk) ?></option>
          <?php endforeach; ?>
          <option value="Lainnya" <?= ($_POST['mata_kuliah'] ?? '') == 'Lainnya' ? 'selected' : '' ?>>Lainnya...</option>
        </select>
      </div>

      <!-- DESKRIPSI -->
      <div class="form-group">
        <label for="deskripsi" class="form-label">Deskripsi <span class="required">*</span></label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"
                  placeholder="Ceritakan apa yang akan dipelajari, level kesulitan, dan hal lain yang perlu diketahui peserta..." required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
      </div>

      <!-- JADWAL & MAKS ANGGOTA -->
      <div class="form-row-2">
        <div class="form-group">
          <label for="jadwal" class="form-label">Jadwal <span class="required">*</span></label>
          <input type="datetime-local" id="jadwal" name="jadwal" class="form-control"
                 value="<?= htmlspecialchars($_POST['jadwal'] ?? '') ?>"
                 min="<?= date('Y-m-d\TH:i') ?>" required>
        </div>
        <div class="form-group">
          <label for="maks_anggota" class="form-label">Maks. Anggota <span class="required">*</span></label>
          <input type="number" id="maks_anggota" name="maks_anggota" class="form-control"
                 min="2" max="50" value="<?= htmlspecialchars($_POST['maks_anggota'] ?? '10') ?>" required>
          <small class="form-hint">Termasuk dirimu sebagai pembuat. (2-50 orang)</small>
        </div>
      </div>

      <!-- LOKASI -->
      <div class="form-group">
        <label for="lokasi" class="form-label">Lokasi / Link Meeting <span class="required">*</span></label>
        <input type="text" id="lokasi" name="lokasi" class="form-control"
               placeholder="Contoh: Perpustakaan Lt.2, Kantin Baru, atau https://zoom.us/j/..."
               value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>" required maxlength="200">
        <small class="form-hint">Bisa berupa nama tempat offline atau link Zoom/Google Meet.</small>
      </div>

      <button type="submit" class="btn btn-success btn-block">
        <i class="fa-solid fa-check"></i> Buat Sesi Sekarang
      </button>
    </form>
  </div>

</div>
</main>

<?php include "../includes/footer.php"; ?>

<script>
// Highlight selected tipe radio
document.querySelectorAll('input[name="tipe"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.study-tipe-option').forEach(el => el.classList.remove('selected'));
    radio.closest('.study-tipe-option').classList.add('selected');
  });
});
</script>

</body>
</html>
