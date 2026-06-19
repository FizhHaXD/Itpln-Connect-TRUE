<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    $return_url = urlencode("events/index.php");
    header("Location: ../auth/login.php?need_login=1&return=$return_url");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_event = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if already joined
$check = mysqli_query($conn, "
    SELECT * FROM event_participants 
    WHERE id_user=$id_user AND id_event=$id_event
");

if (mysqli_num_rows($check) > 0) {
    header("Location: detail.php?id=$id_event&already=1");
    exit();
}

// Insert to event_participants
mysqli_query($conn, "
    INSERT INTO event_participants (id_user, id_event, joined_at)
    VALUES ($id_user, $id_event, NOW())
");

header("Location: detail.php?id=$id_event&joined=1");
exit();
