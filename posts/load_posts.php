<?php
session_start();
require_once "../config/database.php";

$id_user = $_SESSION['id_user'] ?? null;

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filters
$filterMyPosts = isset($_GET['my_posts']) && $_GET['my_posts'] == 1;
$filterMyCommunities = isset($_GET['my_communities']) && $_GET['my_communities'] == 1;

// Build WHERE clause
$whereConditions = [];

if ($filterMyPosts && $id_user) {
    $whereConditions[] = "p.id_user = $id_user";
}

if ($filterMyCommunities && $id_user) {
    $whereConditions[] = "c.id_community IN (
        SELECT id_community FROM community_members WHERE id_user = $id_user
    )";
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM community_posts p
    JOIN communities c ON p.id_community = c.id_community
    JOIN users u ON p.id_user = u.id_user
    $whereClause
");
$totalCount = mysqli_fetch_assoc($countQuery)['total'];
$totalPages = ceil($totalCount / $perPage);

// Get posts
$posts = mysqli_query($conn, "
    SELECT 
        p.*, 
        c.nama_community, 
        u.nama as nama_user, 
        u.foto as foto_user
    FROM community_posts p
    JOIN communities c ON p.id_community = c.id_community
    JOIN users u ON p.id_user = u.id_user
    $whereClause
    ORDER BY p.created_at DESC
    LIMIT $perPage OFFSET $offset
");

// Generate HTML
ob_start();

if (mysqli_num_rows($posts) == 0) {
    echo '<p class="empty-message">Tidak ada postingan.</p>';
} else {
    while ($p = mysqli_fetch_assoc($posts)) {
        $fotoUser = !empty($p['foto_user']) 
            ? '../uploads/profiles/' . $p['foto_user'] 
            : '../uploads/profiles/default.png';
        ?>
        <div class="card post-card">
          <div class="post-header">
            <img src="<?= htmlspecialchars($fotoUser); ?>" class="post-avatar" alt="<?= htmlspecialchars($p['nama_user']); ?>">
            <div style="flex: 1;">
              <b><?= htmlspecialchars($p['nama_user']); ?></b>
              <p class="text-muted" style="font-size: 0.85rem; margin: 0;">
                di <a href="../communities/detail.php?id=<?= $p['id_community']; ?>"><?= htmlspecialchars($p['nama_community']); ?></a>
                • <?= date('d M Y, H:i', strtotime($p['created_at'])); ?>
              </p>
            </div>
          </div>

          <?php if (!empty($p['title'])): ?>
            <h3><?= htmlspecialchars($p['title']); ?></h3>
          <?php endif; ?>
          
          <p><?= nl2br(htmlspecialchars($p['content'] ?? '')); ?></p>

          <?php if (!empty($p['foto']) || !empty($p['foto2']) || !empty($p['foto3'])): ?>
            <div class="post-photos">
              <?php if (!empty($p['foto'])): ?>
                <img src="../uploads/posts/<?= htmlspecialchars($p['foto']); ?>" alt="Post" class="post-img">
              <?php endif; ?>
              <?php if (!empty($p['foto2'])): ?>
                <img src="../uploads/posts/<?= htmlspecialchars($p['foto2']); ?>" alt="Post" class="post-img">
              <?php endif; ?>
              <?php if (!empty($p['foto3'])): ?>
                <img src="../uploads/posts/<?= htmlspecialchars($p['foto3']); ?>" alt="Post" class="post-img">
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($p['whatsapp_link']) || !empty($p['discord_link']) || !empty($p['instagram_link'])): ?>
            <hr>
            <div class="post-links">
              <small class="text-muted"><i class="fa-solid fa-link"></i> Link Komunikasi:</small>
              <?php if (!empty($p['whatsapp_link'])): ?>
                <a href="<?= htmlspecialchars($p['whatsapp_link']); ?>" class="btn btn-sm btn-wa" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
              <?php endif; ?>
              <?php if (!empty($p['discord_link'])): ?>
                <a href="<?= htmlspecialchars($p['discord_link']); ?>" class="btn btn-sm btn-discord" target="_blank"><i class="fa-brands fa-discord"></i> Discord</a>
              <?php endif; ?>
              <?php if (!empty($p['instagram_link'])): ?>
                <a href="<?= htmlspecialchars($p['instagram_link']); ?>" class="btn btn-sm btn-ig" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram</a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
        <?php
    }
}

$html = ob_get_clean();

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'totalPosts' => $totalCount
]);
