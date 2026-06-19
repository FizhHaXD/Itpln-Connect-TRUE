<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn);

// Handle delete
if (isset($_GET['delete'])) {
    $id_post = (int)$_GET['delete'];
    
    // Delete related photos first
    $post = mysqli_query($conn, "SELECT foto, foto2, foto3 FROM community_posts WHERE id_post = $id_post");
    $postData = mysqli_fetch_assoc($post);
    
    if ($postData) {
        // Delete files
        if ($postData['foto']) @unlink("../uploads/posts/" . $postData['foto']);
        if ($postData['foto2']) @unlink("../uploads/posts/" . $postData['foto2']);
        if ($postData['foto3']) @unlink("../uploads/posts/" . $postData['foto3']);
        
        // Delete from database
        mysqli_query($conn, "DELETE FROM community_posts WHERE id_post = $id_post");
        $success = "Postingan berhasil dihapus!";
    }
}

// Get all posts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM community_posts");
$totalPosts = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalPosts / $perPage);

$posts = mysqli_query($conn, "
    SELECT cp.*, u.nama as user_nama, c.nama_community
    FROM community_posts cp
    JOIN users u ON cp.id_user = u.id_user
    JOIN communities c ON cp.id_community = c.id_community
    ORDER BY cp.created_at DESC
    LIMIT $perPage OFFSET $offset
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Postingan - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container-wide">
    <div class="page-header">
    <h2><i class="fa-solid fa-bullhorn"></i> Kelola Postingan Komunitas</h2>
    <a href="index.php" class="btn btn-sm">← Kembali ke Admin</a>
  </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="text-muted">Total: <?= $totalPosts ?> postingan | Halaman <?= $page ?> dari <?= $totalPages ?></p>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Komunitas</th>
                        <th>Title / Content</th>
                        <th>Visibility</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                    <tr>
                        <td><?= $post['id_post'] ?></td>
                        <td><?= safe($post['user_nama']) ?></td>
                        <td><?= safe($post['nama_community']) ?></td>
                        <td>
                            <strong><?= safe(substr($post['title'] ?? '', 0, 30)) ?></strong><br>
                            <small class="text-muted"><?= safe(substr($post['content'], 0, 50)) ?>...</small>
                        </td>
                        <td>
                            <span class="badge"><?= $post['visibility'] ?></span>
                        </td>
                        <td><?= date('d M Y, H:i', strtotime($post['created_at'])) ?></td>
                        <td>
                            <div class="admin-actions">
                                <a href="../communities/detail.php?id=<?= $post['id_community'] ?>" 
                                   class="btn btn-sm" target="_blank">Lihat</a>
                                <a href="?delete=<?= $post['id_post'] ?>" 
                                   class="btn btn-sm btn-logout"
                                   onclick="return confirm('Yakin hapus postingan ini?')">Hapus</a>
                            </div>
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
