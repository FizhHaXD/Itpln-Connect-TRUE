<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn);

// Proses Tambah Matkul
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama_matkul = trim(mysqli_real_escape_string($conn, $_POST['nama_matkul']));
    if (!empty($nama_matkul)) {
        // Cek apakah sudah ada
        $cek = mysqli_query($conn, "SELECT 1 FROM master_matkul WHERE nama_matkul = '$nama_matkul'");
        if (mysqli_num_rows($cek) == 0) {
            mysqli_query($conn, "INSERT INTO master_matkul (nama_matkul) VALUES ('$nama_matkul')");
            header("Location: matkul.php?success=add");
            exit();
        } else {
            header("Location: matkul.php?error=exists");
            exit();
        }
    } else {
        header("Location: matkul.php?error=empty");
        exit();
    }
}

// Proses Hapus Matkul
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_matkul = (int)$_POST['id_matkul'];
    mysqli_query($conn, "DELETE FROM master_matkul WHERE id = $id_matkul");
    header("Location: matkul.php?success=delete");
    exit();
}

// Ambil data
$matkul_q = mysqli_query($conn, "SELECT * FROM master_matkul ORDER BY nama_matkul ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Mata Kuliah - ITPLN Connect</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
    <div class="nav" style="margin-bottom: 20px;">
        <a class="btn" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="page-header">
        <div>
            <h2><i class="fa-solid fa-book"></i> Manajemen Master Mata Kuliah</h2>
            <p class="text-muted">Kelola daftar mata kuliah yang akan muncul di dropdown pembuatan sesi dan folder Bank Materi.</p>
        </div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'add'): ?>
        <div class="alert alert-success" style="margin-bottom:20px;"><i class="fa-solid fa-check"></i> Mata Kuliah berhasil ditambahkan.</div>
    <?php endif; ?>
    <?php if (isset($_GET['success']) && $_GET['success'] == 'delete'): ?>
        <div class="alert alert-success" style="margin-bottom:20px;"><i class="fa-solid fa-check"></i> Mata Kuliah berhasil dihapus.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;"><i class="fa-solid fa-xmark"></i> Gagal! Mata Kuliah tersebut sudah ada.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'empty'): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;"><i class="fa-solid fa-xmark"></i> Nama Mata Kuliah tidak boleh kosong.</div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
        
        <!-- Form Tambah -->
        <div class="card" style="position: sticky; top: 80px;">
            <h3 style="margin-bottom: 16px;"><i class="fa-solid fa-plus"></i> Tambah Matkul Baru</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Nama Mata Kuliah</label>
                    <input type="text" name="nama_matkul" class="form-control" required placeholder="Contoh: Kalkulus Lanjut">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content:center;"><i class="fa-solid fa-save"></i> Simpan Matkul</button>
            </form>
        </div>

        <!-- Tabel Daftar Matkul -->
        <div class="card">
            <h3 style="margin-bottom: 16px;"><i class="fa-solid fa-list"></i> Daftar Mata Kuliah</h3>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Mata Kuliah</th>
                            <th style="width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($matkul_q) == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align:center;">Belum ada mata kuliah.</td>
                        </tr>
                        <?php endif; ?>
                        <?php while ($m = mysqli_fetch_assoc($matkul_q)): ?>
                        <tr>
                            <td>#<?= $m['id'] ?></td>
                            <td><b><?= htmlspecialchars($m['nama_matkul']) ?></b></td>
                            <td style="text-align: center;">
                                <form method="POST" onsubmit="return confirm('Yakin ingin menghapus Matkul ini? Ini tidak akan menghapus materi yang sudah diunggah, tapi matkul ini akan hilang dari opsi form.')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_matkul" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 15px;">
                <i class="fa-solid fa-circle-info"></i> Jika Anda menghapus master mata kuliah, maka folder kosong di Bank Materi akan hilang. Namun, jika folder tersebut sudah ada isinya, foldernya akan tetap muncul (secara dinamis dari tabel `study_materials`).
            </p>
        </div>

    </div>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
