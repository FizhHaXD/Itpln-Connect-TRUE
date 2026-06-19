<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['materi_file'])) {
    $id_user = $_SESSION['id_user'];
    $id_group = isset($_POST['id_group']) ? (int)$_POST['id_group'] : 0;
    
    // Cek apakah user tergabung di grup ini, apakah pembuat sesi, atau admin
    $cek_member = mysqli_query($conn, "SELECT 1 FROM study_group_members WHERE id_group = $id_group AND id_user = $id_user");
    $is_creator = mysqli_query($conn, "SELECT 1 FROM study_groups WHERE id = $id_group AND id_creator = $id_user");
    $roleQ = mysqli_query($conn, "SELECT role FROM users WHERE id_user = $id_user");
    $rData = mysqli_fetch_assoc($roleQ);
    $isAdmin = ($rData && $rData['role'] === 'admin');
    
    // HANYA KETUA ATAU ADMIN YANG BOLEH UPLOAD
    if (mysqli_num_rows($is_creator) == 0 && !$isAdmin) {
        header("Location: detail.php?id=$id_group&error=unauthorized_upload");
        exit();
    }
    
    $file = $_FILES['materi_file'];
    $nama_asli = $file['name'];
    $tmp_path = $file['tmp_name'];
    $ukuran = $file['size'];
    $error = $file['error'];
    
    // Validasi dasar
    if ($error !== UPLOAD_ERR_OK) {
        header("Location: detail.php?id=$id_group&error=upload_failed");
        exit();
    }
    
    // Batas ukuran 10 MB
    if ($ukuran > 10 * 1024 * 1024) {
        header("Location: detail.php?id=$id_group&error=file_too_large");
        exit();
    }
    
    // Validasi Ekstensi
    $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'png', 'jpg', 'jpeg'];
    $ext = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed_ext)) {
        header("Location: detail.php?id=$id_group&error=invalid_type");
        exit();
    }
    
    // Generate nama unik
    $nama_baru = time() . '_' . uniqid() . '.' . $ext;
    $tujuan = "../uploads/materials/" . $nama_baru;
    
    if (move_uploaded_file($tmp_path, $tujuan)) {
        $nama_asli_safe = mysqli_real_escape_string($conn, $nama_asli);
        $nama_baru_safe = mysqli_real_escape_string($conn, $nama_baru);
        
        $mk_query = mysqli_query($conn, "SELECT mata_kuliah FROM study_groups WHERE id = $id_group");
        $mk_data = mysqli_fetch_assoc($mk_query);
        $mata_kuliah = $mk_data ? mysqli_real_escape_string($conn, $mk_data['mata_kuliah']) : '';
        
        $sql = "INSERT INTO study_materials (id_group, id_user, mata_kuliah, nama_file, file_path, ukuran_file) 
                VALUES ($id_group, $id_user, '$mata_kuliah', '$nama_asli_safe', '$nama_baru_safe', $ukuran)";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: detail.php?id=$id_group&materi_success=1");
        } else {
            unlink($tujuan); // Hapus jika gagal masuk DB
            header("Location: detail.php?id=$id_group&error=db_failed");
        }
    } else {
        header("Location: detail.php?id=$id_group&error=move_failed");
    }
    exit();
} else {
    header("Location: index.php");
    exit();
}
