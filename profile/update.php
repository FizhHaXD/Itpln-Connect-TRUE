<?php
require_once "../config/database.php";
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$no_hp = mysqli_real_escape_string($conn, $_POST['no_hp'] ?? '');
$bio = mysqli_real_escape_string($conn, $_POST['bio'] ?? '');

$fotoSQL = "";

// Upload foto jika ada
if (!empty($_FILES['foto']['name'])) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $fotoName = 'profile_' . $id_user . '_' . time() . '.' . $ext;

    $uploadPath = "../uploads/profiles/" . $fotoName;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
        $fotoSQL = ", foto='$fotoName'";
    }
}

// Update user data
mysqli_query($conn, "
    UPDATE users SET
        nama='$nama',
        no_hp='$no_hp',
        bio='$bio'
        $fotoSQL
    WHERE id_user=$id_user
");

// Update session nama
$_SESSION['nama'] = $nama;

header("Location: index.php?updated=1");
exit();
