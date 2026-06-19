<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    exit("Unauthorized");
}

$sender_id = $_SESSION['id_user'];
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;

// Get all messages between two users
$query = mysqli_query($conn, "
    SELECT c.*, u.nama, u.foto
    FROM chats c
    JOIN users u ON c.sender_id = u.id_user
    WHERE (c.sender_id=$sender_id AND c.receiver_id=$receiver_id)
       OR (c.sender_id=$receiver_id AND c.receiver_id=$sender_id)
    ORDER BY c.created_at ASC
");

if (mysqli_num_rows($query) == 0) {
    echo '<p class="empty-message" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-comments" style="font-size: 3rem; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
            Belum ada pesan. Mulai percakapan!
          </p>';
} else {
    while ($chat = mysqli_fetch_assoc($query)) {
        $foto = !empty($chat['foto']) ? '../uploads/profiles/' . $chat['foto'] : '../uploads/profiles/default.png';
        $isSender = ($chat['sender_id'] == $sender_id);
        $messageClass = $isSender ? 'chat-message-sent' : 'chat-message-received';
        $bubbleClass = $isSender ? 'chat-bubble-sent' : 'chat-bubble-received';
        ?>
        <div class="chat-message <?= $messageClass ?>">
            <?php if (!$isSender): ?>
                <img src="<?= htmlspecialchars($foto); ?>" class="chat-avatar-tiny" alt="<?= htmlspecialchars($chat['nama']); ?>">
            <?php endif; ?>
            
            <div class="chat-bubble <?= $bubbleClass ?>">
                <div class="chat-bubble-content">
                    <?= nl2br(htmlspecialchars($chat['message'])); ?>
                </div>
                <div class="chat-time">
                    <?= date('H:i', strtotime($chat['created_at'])); ?>
                </div>
            </div>
            
            <?php if ($isSender): ?>
                <img src="<?= htmlspecialchars($foto); ?>" class="chat-avatar-tiny" alt="<?= htmlspecialchars($chat['nama']); ?>">
            <?php endif; ?>
        </div>
        <?php
    }
}
