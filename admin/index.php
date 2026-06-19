<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn); // Only admin can access

// Get statistics
$stats = [];

// Total Users
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$stats['users'] = mysqli_fetch_assoc($result)['total'];

// Total Communities
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM communities");
$stats['communities'] = mysqli_fetch_assoc($result)['total'];

// Total Posts (Community + Global)
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM community_posts");
$stats['community_posts'] = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM global_posts");
$stats['global_posts'] = mysqli_fetch_assoc($result)['total'];

// Total Events
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM events");
$stats['events'] = mysqli_fetch_assoc($result)['total'];

// Total Master Matkul
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM master_matkul");
$stats['matkul'] = mysqli_fetch_assoc($result)['total'];

// Recent Activities (last 10 posts)
$recentPosts = mysqli_query($conn, "
    SELECT cp.*, u.nama as user_nama, c.nama_community
    FROM community_posts cp
    JOIN users u ON cp.id_user = u.id_user
    JOIN communities c ON cp.id_community = c.id_community
    ORDER BY cp.created_at DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - ITPLN Connect</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="fa-solid fa-shield-halved"></i> Admin Dashboard</h2>
            <p class="text-muted">Kelola seluruh konten dan user</p>
        </div>
    </div>

    <!-- Quick Summary -->
    <div class="card" style="margin-bottom: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-color: #5a67d8;">
        <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;"><?= $stats['users'] ?></div>
                <div style="opacity: 0.9;">Total Users</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;"><?= $stats['communities'] ?></div>
                <div style="opacity: 0.9;">Komunitas</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;"><?= $stats['community_posts'] + $stats['global_posts'] ?></div>
                <div style="opacity: 0.9;">Total Posts</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;"><?= $stats['events'] ?></div>
                <div style="opacity: 0.9;">Events</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-value"><?= $stats['users'] ?></div>
            <div class="stat-label">Total Users</div>
            <a href="users.php" class="btn btn-sm">Kelola →</a>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
            <div class="stat-value"><?= $stats['communities'] ?></div>
            <div class="stat-label">Komunitas</div>
            <a href="communities.php" class="btn btn-sm">Kelola →</a>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-bullhorn"></i></div>
            <div class="stat-value"><?= $stats['community_posts'] ?></div>
            <div class="stat-label">Postingan Komunitas</div>
            <a href="posts.php" class="btn btn-sm">Kelola →</a>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="stat-value"><?= $stats['events'] ?></div>
            <div class="stat-label">Events</div>
            <a href="events.php" class="btn btn-sm">Kelola →</a>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-comment-dots"></i></div>
            <div class="stat-value"><?= $stats['global_posts'] ?></div>
            <div class="stat-label">Postingan Global</div>
            <a href="global_posts.php" class="btn btn-sm">Kelola →</a>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-book-open"></i></div>
            <div class="stat-value"><?= $stats['matkul'] ?></div>
            <div class="stat-label">Master Matkul</div>
            <a href="matkul.php" class="btn btn-sm">Kelola →</a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card" style="margin-top: 24px;">
        <h3><i class="fa-solid fa-list"></i> Aktivitas Terbaru</h3>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Komunitas</th>
                        <th>Title</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = mysqli_fetch_assoc($recentPosts)): ?>
                    <tr>
                        <td><?= safe($post['user_nama']) ?></td>
                        <td><?= safe($post['nama_community']) ?></td>
                        <td><?= safe(substr($post['title'] ?? $post['content'], 0, 50)) ?>...</td>
                        <td><?= date('d M, H:i', strtotime($post['created_at'])) ?></td>
                        <td>
                            <a href="../communities/detail.php?id=<?= $post['id_community'] ?>" 
                               class="btn btn-sm">Lihat</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
