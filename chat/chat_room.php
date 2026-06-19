<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$sender_id = $_SESSION['id_user'];
$receiver_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Update sender's last_activity (mark as online)
mysqli_query($conn, "UPDATE users SET last_activity = NOW() WHERE id_user = $sender_id");

// Get receiver info with online status
$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        nama, 
        foto,
        IF(TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 5, 1, 0) as is_online
    FROM users 
    WHERE id_user=$receiver_id
"));

if (!$user) {
    header("Location: chat_list.php");
    exit();
}

// ✅ VALIDASI: Cek apakah kedua user share komunitas atau event
$shareCheck = mysqli_query($conn, "
    SELECT 1 
    FROM (
        -- Share komunitas
        SELECT cm2.id_user 
        FROM community_members cm1
        JOIN community_members cm2 ON cm1.id_community = cm2.id_community
        WHERE cm1.id_user = $sender_id AND cm2.id_user = $receiver_id
        
        UNION
        
        -- Share event
        SELECT ep2.id_user 
        FROM event_participants ep1
        JOIN event_participants ep2 ON ep1.id_event = ep2.id_event
        WHERE ep1.id_user = $sender_id AND ep2.id_user = $receiver_id
    ) AS shared
    LIMIT 1
");

if (mysqli_num_rows($shareCheck) == 0) {
    // Tidak share komunitas atau event
    header("Location: chat_list.php?error=no_connection");
    exit();
}

$foto = !empty($user['foto']) ? '../uploads/profiles/' . $user['foto'] : '../uploads/profiles/default.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat dengan <?= htmlspecialchars($user['nama']); ?> - ITPLN Connect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<main class="main-content">
<div class="container">
  <!-- Chat Header Card -->
  <div class="card chat-header-card">
    <div class="chat-header">
      <a href="chat_list.php" class="btn btn-sm">← Kembali</a>
      <div class="chat-user-info-header">
        <div class="chat-user-avatar-wrapper">
          <img src="<?= htmlspecialchars($foto); ?>" class="chat-avatar-small" alt="<?= htmlspecialchars($user['nama']); ?>">
          <span class="online-status-small <?= $user['is_online'] ? 'online' : 'offline' ?>"></span>
        </div>
        <div>
          <h3><?= htmlspecialchars($user['nama']); ?></h3>
          <small class="text-muted" style="color:<?= $user['is_online'] ? '#10b981' : 'var(--text-muted)' ?>">
            <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> <?= $user['is_online'] ? 'Online' : 'Offline' ?>
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Chat Box Card -->
  <div class="card">
    <div id="chat-box" class="chat-box"></div>
  </div>

  <!-- Chat Form Card -->
  <div class="card">
    <form id="chatForm" class="chat-form">
      <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
      <textarea name="message" placeholder="Ketik pesan..." required></textarea>
      <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Kirim</button>
    </form>
  </div>
</div>
</main>

<script>
function loadChat() {
    fetch("load_message.php?receiver_id=<?= $receiver_id ?>")
        .then(res => res.text())
        .then(data => {
            const chatBox = document.getElementById("chat-box");
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Load messages every 2 seconds
setInterval(loadChat, 2000);
loadChat();

// Handle form submission
document.getElementById("chatForm").addEventListener("submit", function(e){
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("send_message.php", {
        method: "POST",
        body: formData
    }).then(() => {
        this.reset();
        loadChat();
    });
});
</script>

</body>
</html>
