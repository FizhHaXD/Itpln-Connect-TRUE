<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn);

// Handle delete global post
if (isset($_GET['delete'])) {
    $id_post = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM global_posts WHERE id_post = $id_post");
    $success = "Postingan global berhasil dihapus!";
}

// Get all global posts
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM global_posts");
$totalPosts = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalPosts / $perPage);

$posts = mysqli_query($conn, "
    SELECT gp.*, u.nama as user_nama
    FROM global_posts gp
    JOIN users u ON gp.id_user = u.id_user
    ORDER BY gp.created_at DESC
    LIMIT $perPage OFFSET $offset
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Postingan Global - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container-wide">
    <div class="page-header">
    <h2><i class="fa-solid fa-comment-dots"></i> Kelola Postingan Global</h2>
    <a href="index.php" class="btn btn-sm">← Kembali ke Admin</a>
  </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="text-muted">Total: <?= $totalPosts ?> postingan global | Halaman <?= $page ?> dari <?= $totalPages ?></p>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Content</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                    <tr>
                        <td><?= $post['id_post'] ?></td>
                        <td><?= safe($post['user_nama']) ?></td>
                        <td>
                            <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis;">
                                <?= safe(substr($post['content'], 0, 100)) ?><?= strlen($post['content']) > 100 ? '...' : '' ?>
                            </div>
                        </td>
                        <td><?= date('d M Y, H:i', strtotime($post['created_at'])) ?></td>
                        <td>
                            <a href="?delete=<?= $post['id_post'] ?>" 
                               class="btn btn-sm btn-logout"
                               onclick="return confirm('Hapus postingan global ini?')">
                                 <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn-sm">← Prev</a>
            <?php endif; ?>
            
            <span style="margin: 0 12px;">Halaman <?= $page ?> / <?= $totalPages ?></span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-sm">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
