<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$mata_kuliah = isset($_POST['mata_kuliah']) ? trim($_POST['mata_kuliah']) : '';

// Pastikan user adalah admin
$roleQ = mysqli_query($conn, "SELECT role FROM users WHERE id_user = $id_user");
$rData = mysqli_fetch_assoc($roleQ);
if (!$rData || $rData['role'] !== 'admin') {
    header("Location: materials.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['materi_file']) && $mata_kuliah) {
    
    $file = $_FILES['materi_file'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'png', 'jpg', 'jpeg'];
    
    if (in_array($file_ext, $allowed_ext)) {
        if ($file_error === 0) {
            if ($file_size <= 10485760) { // 10MB limit
                $new_file_name = uniqid('global_mat_', true) . '.' . $file_ext;
                $upload_path = '../uploads/materials/' . $new_file_name;
                
                // Pastikan direktori ada
                if (!is_dir('../uploads/materials')) {
                    mkdir('../uploads/materials', 0777, true);
                }
                
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $query = "INSERT INTO study_materials (id_group, id_user, mata_kuliah, nama_file, file_path, ukuran_file, uploaded_at) 
                              VALUES (NULL, $id_user, '$mata_kuliah', '$file_name', '$new_file_name', $file_size, NOW())";
                    
                    if (mysqli_query($conn, $query)) {
                        header("Location: materials.php?matkul=" . urlencode($mata_kuliah) . "&materi_success=1");
                        exit();
                    }
                }
            }
        }
    }
    
    header("Location: materials.php?matkul=" . urlencode($mata_kuliah) . "&error=upload_failed");
    exit();
}
header("Location: materials.php");
exit();
?>
