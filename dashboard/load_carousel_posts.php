<?php
session_start();
require_once "../config/database.php";

// Get eksternal posts for carousel
$posts = mysqli_query($conn, "
    SELECT 
        p.*, 
        c.nama_community,
        u.nama as nama_user, 
        u.foto as foto_user
    FROM community_posts p
    JOIN communities c ON p.id_community = c.id_community
    JOIN users u ON p.id_user = u.id_user
    WHERE p.visibility = 'eksternal'
    ORDER BY p.created_at DESC
    LIMIT 10
");

$data = [];

while ($p = mysqli_fetch_assoc($posts)) {
    $fotoUser = !empty($p['foto_user']) 
        ? '../uploads/profiles/' . $p['foto_user'] 
        : '../uploads/profiles/default.png';
    
    $data[] = [
        'id' => $p['id_post'],
        'title' => $p['title'] ?? '',
        'content' => $p['content'],
        'foto' => $p['foto'] ? '../uploads/posts/' . $p['foto'] : null,
        'foto2' => $p['foto2'] ? '../uploads/posts/' . $p['foto2'] : null,
        'foto3' => $p['foto3'] ? '../uploads/posts/' . $p['foto3'] : null,
        'community' => $p['nama_community'],
        'id_community' => $p['id_community'],
        'user' => $p['nama_user'],
        'foto_user' => $fotoUser,
        'created_at' => date('d M Y, H:i', strtotime($p['created_at']))
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
