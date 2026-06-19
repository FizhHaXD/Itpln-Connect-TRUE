<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_materi'])) {
    $id_user = $_SESSION['id_user'];
    $id_materi = (int)$_POST['id_materi'];
    
    // Ambil info materi dan cek hak akses (hanya pengunggah, pembuat grup, atau admin)
    $query = mysqli_query($conn, "
        SELECT sm.*, sg.id_creator 
        FROM study_materials sm 
        LEFT JOIN study_groups sg ON sm.id_group = sg.id 
        WHERE sm.id = $id_materi
    ");
    
    if (mysqli_num_rows($query) > 0) {
        $materi = mysqli_fetch_assoc($query);
        
        $roleQ = mysqli_query($conn, "SELECT role FROM users WHERE id_user = $id_user");
        $rData = mysqli_fetch_assoc($roleQ);
        $isAdmin = ($rData && $rData['role'] === 'admin');
        
        if ($materi['id_user'] == $id_user || $materi['id_creator'] == $id_user || $isAdmin) {
            $file_path = "../uploads/materials/" . $materi['file_path'];
            
            // Hapus file fisik
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Hapus dari database
            mysqli_query($conn, "DELETE FROM study_materials WHERE id = $id_materi");
            
            if (isset($_POST['return_to_global'])) {
                header("Location: materials.php?matkul=" . urlencode($_POST['return_to_global']) . "&delete_success=1");
            } else {
                header("Location: detail.php?id=" . $materi['id_group'] . "&delete_success=1");
            }
        } else {
            if (isset($_POST['return_to_global'])) {
                header("Location: materials.php?matkul=" . urlencode($_POST['return_to_global']) . "&error=unauthorized");
            } else {
                header("Location: detail.php?id=" . $materi['id_group'] . "&error=unauthorized");
            }
        }
    } else {
        if (isset($_POST['return_to_global'])) {
            header("Location: materials.php?matkul=" . urlencode($_POST['return_to_global']));
        } else {
            header("Location: index.php");
        }
    }
    exit();
} else {
    header("Location: index.php");
    exit();
}
