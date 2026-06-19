<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Update current user's last_activity (mark as online)
mysqli_query($conn, "UPDATE users SET last_activity = NOW() WHERE id_user = $id_user");

// Get users yang share komunitas atau event yang sama
// PLUS get last message dan online status
$users = mysqli_query($conn, "
    SELECT DISTINCT 
        u.id_user, 
        u.nama, 
        u.foto,
        u.last_activity,
        -- Check if online (active in last 5 minutes)
        IF(TIMESTAMPDIFF(MINUTE, u.last_activity, NOW()) <= 5, 1, 0) as is_online,
        -- Get last message
        (
            SELECT message 
            FROM chats 
            WHERE (sender_id = $id_user AND receiver_id = u.id_user)
               OR (sender_id = u.id_user AND receiver_id = $id_user)
            ORDER BY created_at DESC 
            LIMIT 1
        ) as last_message,
        -- Get last message time
        (
            SELECT created_at 
            FROM chats 
            WHERE (sender_id = $id_user AND receiver_id = u.id_user)
               OR (sender_id = u.id_user AND receiver_id = $id_user)
            ORDER BY created_at DESC 
            LIMIT 1
        ) as last_message_time
    FROM users u
    WHERE u.id_user != $id_user 
    AND (
        -- Share komunitas yang sama
        u.id_user IN (
            SELECT cm2.id_user 
            FROM community_members cm1
            JOIN community_members cm2 ON cm1.id_community = cm2.id_community
            WHERE cm1.id_user = $id_user AND cm2.id_user != $id_user
        )
        OR
        -- Share event yang sama
        u.id_user IN (
            SELECT ep2.id_user 
            FROM event_participants ep1
            JOIN event_participants ep2 ON ep1.id_event = ep2.id_event
            WHERE ep1.id_user = $id_user AND ep2.id_user != $id_user
        )
    )
    ORDER BY last_message_time DESC, u.nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat - ITPLN Connect</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <div class="page-header">
    <h2><i class="fa-solid fa-comments"></i> Chat Pribadi</h2>
    <p class="text-muted">Chat dengan teman di komunitas atau event yang sama</p>
  </div>

  <?php if (isset($_GET['error']) && $_GET['error'] == 'no_connection'): ?>
    <div class="alert alert-info">
      <i class="fa-solid fa-circle-info"></i> Kamu hanya bisa chat dengan user yang berada di komunitas atau event yang sama.
    </div>
  <?php endif; ?>

  <?php if (mysqli_num_rows($users) == 0): ?>
    <div class="card">
      <p class="empty-message" style="text-align: center; padding: 40px 20px;">
        <i class="fa-solid fa-comments" style="font-size: 2.5rem; display: block; margin-bottom: 16px; color:var(--primary-light);"></i>
        <strong>Belum ada user yang bisa kamu chat.</strong><br>
        <span class="text-muted">Join komunitas atau event untuk bertemu user lain!</span>
      </p>
    </div>
  <?php else: ?>
    <div class="card">
      <div class="chat-user-list">
        <?php while ($u = mysqli_fetch_assoc($users)): ?>
          <?php 
            $foto = !empty($u['foto']) ? '../uploads/profiles/' . $u['foto'] : '../uploads/profiles/default.png';
            $isOnline = $u['is_online'] == 1;
            
            // Format last message
            if (!empty($u['last_message'])) {
                $lastMsg = strlen($u['last_message']) > 50 
                    ? substr($u['last_message'], 0, 50) . '...' 
                    : $u['last_message'];
            } else {
                $lastMsg = 'Belum ada pesan';
            }
          ?>
          <a href="chat_room.php?id=<?= $u['id_user']; ?>" class="chat-user-item">
            <div class="chat-user-avatar-wrapper">
              <img src="<?= htmlspecialchars($foto); ?>" class="chat-avatar" alt="<?= htmlspecialchars($u['nama']); ?>">
              <span class="online-status <?= $isOnline ? 'online' : 'offline' ?>"></span>
            </div>
            <div class="chat-user-info">
              <strong class="chat-user-name"><?= htmlspecialchars($u['nama']); ?></strong>
              <small class="text-muted chat-last-message"><?= htmlspecialchars($lastMsg); ?></small>
            </div>
            <span class="chat-arrow"><i class="fa-solid fa-chevron-right"></i></span>
          </a>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>
