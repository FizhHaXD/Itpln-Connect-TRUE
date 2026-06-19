<?php
session_start();
include "../config/database.php";

// Ambil 5 postingan global terbaru dengan info user
$globalPosts = mysqli_query($conn, "
  SELECT gp.*, u.nama, u.foto
  FROM global_posts gp
  JOIN users u ON gp.id_user = u.id_user
  ORDER BY gp.created_at DESC
  LIMIT 10
");

$posts = [];
while ($row = mysqli_fetch_assoc($globalPosts)) {
    // Fix path foto profil
    $fotoPath = '../uploads/profiles/default.png'; // default
    if (!empty($row['foto'])) {
        $fotoPath = '../uploads/profiles/' . $row['foto'];
    }
    
    $posts[] = [
        'nama' => $row['nama'],
        'foto' => $fotoPath,
        'content' => $row['content'],
        'created_at' => date('d M Y, H:i', strtotime($row['created_at']))
    ];
}

header('Content-Type: application/json');
echo json_encode($posts);
