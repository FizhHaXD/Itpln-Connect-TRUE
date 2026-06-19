<?php
session_start();
include "../config/database.php";

// Harus login
if (!isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "auth/login.php?need_login=1");
    exit();
}

// Hanya menerima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$id_user  = (int)$_SESSION['id_user'];
$id_group = (int)($_POST['id_group'] ?? 0);
$action   = $_POST['action'] ?? '';

if (!$id_group || !in_array($action, ['join','leave','delete'])) {
    header("Location: index.php");
    exit();
}

// Ambil data sesi
$group = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT sg.*, COUNT(sgm.id) AS jumlah_anggota
    FROM study_groups sg
    LEFT JOIN study_group_members sgm ON sg.id = sgm.id_group
    WHERE sg.id = $id_group
    GROUP BY sg.id
"));

if (!$group) {
    header("Location: index.php");
    exit();
}

$isCreator = ($id_user == $group['id_creator']);
$cekJoin   = mysqli_query($conn, "SELECT 1 FROM study_group_members WHERE id_group=$id_group AND id_user=$id_user");
$isJoined  = mysqli_num_rows($cekJoin) > 0;

// ===== HAPUS SESI (hanya creator) =====
if ($action === 'delete') {
    if (!$isCreator) {
        header("Location: detail.php?id=$id_group");
        exit();
    }
    // Hapus anggota dulu (harusnya CASCADE, tapi aman-aman saja)
    mysqli_query($conn, "DELETE FROM study_group_members WHERE id_group=$id_group");
    mysqli_query($conn, "DELETE FROM study_groups WHERE id=$id_group AND id_creator=$id_user");
    header("Location: index.php?deleted=1");
    exit();
}

// ===== KELUAR SESI =====
if ($action === 'leave') {
    if (!$isJoined || $isCreator) {
        // Creator tidak bisa keluar (harus hapus sesi)
        header("Location: detail.php?id=$id_group");
        exit();
    }
    mysqli_query($conn, "DELETE FROM study_group_members WHERE id_group=$id_group AND id_user=$id_user");
    // Update status jika tadinya penuh
    mysqli_query($conn, "UPDATE study_groups SET status='open' WHERE id=$id_group AND status='penuh'");
    header("Location: detail.php?id=$id_group&left=1");
    exit();
}

// ===== BERGABUNG =====
if ($action === 'join') {
    // Validasi
    if ($isJoined) {
        header("Location: detail.php?id=$id_group");
        exit();
    }
    if ($group['status'] !== 'open') {
        header("Location: detail.php?id=$id_group&full=1");
        exit();
    }
    if ($group['jumlah_anggota'] >= $group['maks_anggota']) {
        // Update status ke penuh
        mysqli_query($conn, "UPDATE study_groups SET status='penuh' WHERE id=$id_group");
        header("Location: detail.php?id=$id_group&full=1");
        exit();
    }

    mysqli_query($conn, "INSERT INTO study_group_members (id_group, id_user) VALUES ($id_group, $id_user)");
    
    // Cek apakah sekarang penuh
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM study_group_members WHERE id_group=$id_group"));
    if ($total['c'] >= $group['maks_anggota']) {
        mysqli_query($conn, "UPDATE study_groups SET status='penuh' WHERE id=$id_group");
    }

    header("Location: detail.php?id=$id_group&joined=1");
    exit();
}

header("Location: index.php");
exit();
