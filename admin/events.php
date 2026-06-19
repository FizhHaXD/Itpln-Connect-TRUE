<?php
session_start();
include "../config/database.php";
include "../config/security.php";

require_login();
require_admin($conn);

// Handle delete event
if (isset($_GET['delete'])) {
    $id_event = (int)$_GET['delete'];
    
    // Get event data
    $event = mysqli_query($conn, "SELECT foto FROM events WHERE id_event = $id_event");
    $eventData = mysqli_fetch_assoc($event);
    
    if ($eventData) {
        // Delete photo
        if ($eventData['foto']) @unlink("../uploads/events/" . $eventData['foto']);
        
        // Delete participants
        mysqli_query($conn, "DELETE FROM event_participants WHERE id_event = $id_event");
        
        // Delete event
        mysqli_query($conn, "DELETE FROM events WHERE id_event = $id_event");
        $success = "Event berhasil dihapus!";
    }
}

// Get all events with stats
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM events");
$totalEvents = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalEvents / $perPage);

$events = mysqli_query($conn, "
    SELECT e.*,
           c.nama_community,
           (SELECT COUNT(*) FROM event_participants WHERE id_event = e.id_event) as participants_count
    FROM events e
    JOIN communities c ON e.id_community = c.id_community
    ORDER BY e.tanggal DESC
    LIMIT $perPage OFFSET $offset
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Events - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container-wide">
    <div class="page-header">
    <h2><i class="fa-solid fa-calendar-days"></i> Kelola Events</h2>
    <a href="index.php" class="btn btn-sm">← Kembali ke Admin</a>
  </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="text-muted">Total: <?= $totalEvents ?> events | Halaman <?= $page ?> dari <?= $totalPages ?></p>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Event</th>
                        <th>Komunitas</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th>Peserta</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = mysqli_fetch_assoc($events)): ?>
                    <tr>
                        <td><?= $event['id_event'] ?></td>
                        <td><strong><?= safe($event['nama_event']) ?></strong></td>
                        <td><?= safe($event['nama_community']) ?></td>
                        <td><?= date('d M Y', strtotime($event['tanggal'])) ?></td>
                        <td><?= date('H:i', strtotime($event['waktu'])) ?></td>
                        <td><?= safe($event['lokasi']) ?></td>
                        <td><?= $event['participants_count'] ?> <i class="fa-solid fa-users" style="color:var(--text-muted); font-size:0.8rem;"></i></td>
                        <td>
                            <div class="admin-actions">
                                <a href="../events/detail.php?id=<?= $event['id_event'] ?>" 
                                   class="btn btn-sm" target="_blank">Lihat</a>
                                <a href="?delete=<?= $event['id_event'] ?>" 
                                   class="btn btn-sm btn-logout"
                                   onclick="return confirm('HAPUS event <?= safe($event['nama_event']) ?>?')">
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
