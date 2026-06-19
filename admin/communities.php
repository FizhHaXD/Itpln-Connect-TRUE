<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn);

// Handle delete community
if (isset($_GET['delete'])) {
    $id_community = (int)$_GET['delete'];
    
    // Get community data
    $community = mysqli_query($conn, "SELECT foto FROM communities WHERE id_community = $id_community");
    $communityData = mysqli_fetch_assoc($community);
    
    if ($communityData) {
        // Delete photo
        if ($communityData['foto']) @unlink("../uploads/communities/" . $communityData['foto']);
        
        // Delete related data
        mysqli_query($conn, "DELETE FROM community_members WHERE id_community = $id_community");
        mysqli_query($conn, "DELETE FROM community_posts WHERE id_community = $id_community");
        mysqli_query($conn, "DELETE FROM events WHERE id_community = $id_community");
        
        // Delete community
        mysqli_query($conn, "DELETE FROM communities WHERE id_community = $id_community");
        $success = "Komunitas berhasil dihapus!";
    }
}

// Get all communities with stats
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM communities");
$totalCommunities = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalCommunities / $perPage);

$communities = mysqli_query($conn, "
    SELECT c.*,
           (SELECT COUNT(*) FROM community_members WHERE id_community = c.id_community) as members_count,
           (SELECT COUNT(*) FROM community_posts WHERE id_community = c.id_community) as posts_count
    FROM communities c
    ORDER BY c.id_community DESC
    LIMIT $perPage OFFSET $offset
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Komunitas - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container-wide">
    <div class="page-header">
    <h2><i class="fa-solid fa-building"></i> Kelola Komunitas</h2>
    <a href="index.php" class="btn btn-sm">← Kembali ke Admin</a>
  </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="text-muted">Total: <?= $totalCommunities ?> komunitas | Halaman <?= $page ?> dari <?= $totalPages ?></p>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Komunitas</th>
                        <th>Kategori</th>
                        <th>Members</th>
                        <th>Posts</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($community = mysqli_fetch_assoc($communities)): ?>
                    <tr>
                        <td><?= $community['id_community'] ?></td>
                        <td><strong><?= safe($community['nama_community']) ?></strong></td>
                        <td><span class="badge"><?= safe($community['kategori']) ?></span></td>
                        <td><?= $community['members_count'] ?> <i class="fa-solid fa-users" style="color:var(--text-muted); font-size:0.8rem;"></i></td>
                        <td><?= $community['posts_count'] ?> <i class="fa-solid fa-file" style="color:var(--text-muted); font-size:0.8rem;"></i></td>
                        <td><?= date('d M Y', strtotime($community['created_at'])) ?></td>
                        <td>
                            <div class="admin-actions">
                                <a href="../communities/detail.php?id=<?= $community['id_community'] ?>" 
                                   class="btn btn-sm" target="_blank">Lihat</a>
                                <a href="?delete=<?= $community['id_community'] ?>" 
                                   class="btn btn-sm btn-logout"
                                   onclick="return confirm('HAPUS komunitas <?= safe($community['nama_community']) ?>?\n\nSemua members, posts, dan events akan hilang!')">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
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
