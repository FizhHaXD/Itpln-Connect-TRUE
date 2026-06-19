<?php
session_start();
include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    $id_community = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $return_url = urlencode("communities/detail.php?id=$id_community");
    header("Location: ../auth/login.php?need_login=1&return=$return_url");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_community = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_community <= 0) {
    header("Location: index.php");
    exit();
}

// Cek apakah sudah join
$check = mysqli_query($conn,
    "SELECT * FROM community_members 
     WHERE id_user=$id_user AND id_community=$id_community"
);

if (mysqli_num_rows($check) == 0) {
    // Belum join, tambahkan ke member
    mysqli_query($conn,
        "INSERT INTO community_members (id_user, id_community, joined_at)
         VALUES ($id_user, $id_community, NOW())"
    );
}

header("Location: detail.php?id=$id_community&joined=1");
exit();
?>
