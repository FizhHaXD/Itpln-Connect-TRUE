<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn);

// Handle promote to admin
if (isset($_GET['promote'])) {
    $id_user = (int)$_GET['promote'];
    mysqli_query($conn, "UPDATE users SET role = 'admin' WHERE id_user = $id_user");
    $success = "User berhasil dipromote jadi admin!";
}

// Handle demote to user
if (isset($_GET['demote'])) {
    $id_user = (int)$_GET['demote'];
    
    // Prevent demoting yourself
    if ($id_user == $_SESSION['id_user']) {
        $error = "Tidak bisa demote diri sendiri!";
    } else {
        mysqli_query($conn, "UPDATE users SET role = 'user' WHERE id_user = $id_user");
        $success = "Admin berhasil didemote jadi user biasa!";
    }
}

// Handle delete user
if (isset($_GET['delete'])) {
    $id_user = (int)$_GET['delete'];
    
    // Prevent deleting yourself
    if ($id_user == $_SESSION['id_user']) {
        $error = "Tidak bisa delete diri sendiri!";
    } else {
        // Delete related data first
        mysqli_query($conn, "DELETE FROM community_members WHERE id_user = $id_user");
        mysqli_query($conn, "DELETE FROM event_participants WHERE id_user = $id_user");
        mysqli_query($conn, "DELETE FROM community_posts WHERE id_user = $id_user");
        mysqli_query($conn, "DELETE FROM global_posts WHERE id_user = $id_user");
        
        // Delete user
        mysqli_query($conn, "DELETE FROM users WHERE id_user = $id_user");
        $success = "User berhasil dihapus!";
    }
}

// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filter by role
$roleFilter = isset($_GET['role']) ? $_GET['role'] : 'all';
$whereClause = ($roleFilter != 'all') ? "WHERE role = '$roleFilter'" : "";

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users $whereClause");
$totalUsers = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalUsers / $perPage);

$users = mysqli_query($conn, "
    SELECT * FROM users
    $whereClause
    ORDER BY id_user DESC
    LIMIT $perPage OFFSET $offset
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Users - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container-wide">
    <div class="page-header">
        <h2><i class="fa-solid fa-users"></i> Kelola Users</h2>
        <a href="index.php" class="btn btn-sm">← Kembali ke Admin</a>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="card" style="margin-bottom: 16px;">
        <div style="display: flex; gap: 12px; align-items: center;">
            <strong>Filter:</strong>
            <a href="?role=all" class="btn btn-sm <?= $roleFilter == 'all' ? '' : 'btn-outline' ?>">Semua</a>
            <a href="?role=admin" class="btn btn-sm <?= $roleFilter == 'admin' ? '' : 'btn-outline' ?>">Admin Only</a>
            <a href="?role=user" class="btn btn-sm <?= $roleFilter == 'user' ? '' : 'btn-outline' ?>">User Only</a>
        </div>
    </div>

    <div class="card">
        <p class="text-muted">Total: <?= $totalUsers ?> users | Halaman <?= $page ?> dari <?= $totalPages ?></p>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= $user['id_user'] ?></td>
                        <td><?= safe($user['nama']) ?></td>
                        <td><?= safe($user['email']) ?></td>
                        <td>
                            <?php if ($user['role'] == 'admin'): ?>
                                <span class="badge" style="background: #dc2626; color: white;"><i class="fa-solid fa-shield-halved"></i> ADMIN</span>
                            <?php else: ?>
                                <span class="badge"><i class="fa-solid fa-user"></i> USER</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                        <td>
                            <div class="admin-actions">
                                <?php if ($user['id_user'] != $_SESSION['id_user']): ?>
                                    <?php if ($user['role'] == 'user'): ?>
                                        <a href="?promote=<?= $user['id_user'] ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Promote <?= safe($user['nama']) ?> jadi admin?')">
                                            <i class="fa-solid fa-arrow-up"></i> Promote
                                        </a>
                                    <?php else: ?>
                                        <a href="?demote=<?= $user['id_user'] ?>" 
                                           class="btn btn-sm"
                                           onclick="return confirm('Demote <?= safe($user['nama']) ?> jadi user biasa?')">
                                            <i class="fa-solid fa-arrow-down"></i> Demote
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="?delete=<?= $user['id_user'] ?>" 
                                       class="btn btn-sm btn-logout"
                                       onclick="return confirm('HAPUS user <?= safe($user['nama']) ?>? Semua data akan hilang!')">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Anda sendiri</span>
                                <?php endif; ?>
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
                <a href="?page=<?= $page - 1 ?>&role=<?= $roleFilter ?>" class="btn btn-sm">← Prev</a>
            <?php endif; ?>
            
            <span style="margin: 0 12px;">Halaman <?= $page ?> / <?= $totalPages ?></span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&role=<?= $roleFilter ?>" class="btn btn-sm">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
