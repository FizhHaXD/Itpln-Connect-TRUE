<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    exit("Unauthorized");
}

$sender_id = $_SESSION['id_user'];
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? mysqli_real_escape_string($conn, trim($_POST['message'])) : '';

if (!empty($message) && $receiver_id > 0) {
    // ✅ VALIDASI: Cek apakah share komunitas atau event
    $shareCheck = mysqli_query($conn, "
        SELECT 1 
        FROM (
            SELECT cm2.id_user 
            FROM community_members cm1
            JOIN community_members cm2 ON cm1.id_community = cm2.id_community
            WHERE cm1.id_user = $sender_id AND cm2.id_user = $receiver_id
            
            UNION
            
            SELECT ep2.id_user 
            FROM event_participants ep1
            JOIN event_participants ep2 ON ep1.id_event = ep2.id_event
            WHERE ep1.id_user = $sender_id AND ep2.id_user = $receiver_id
        ) AS shared
        LIMIT 1
    ");
    
    if (mysqli_num_rows($shareCheck) > 0) {
        // OK, share komunitas/event - boleh kirim pesan
        mysqli_query($conn, "
            INSERT INTO chats (sender_id, receiver_id, message, created_at)
            VALUES ($sender_id, $receiver_id, '$message', NOW())
        ");
    }
}
