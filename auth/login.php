<?php
session_start();
include "../config/database.php";

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['last_activity'] = time();

        if (isset($_GET['return'])) {
            header("Location: ../" . $_GET['return']);
        } else {
            header("Location: ../dashboard/index.php");
        }
        exit();
    } else {
        $error = "Email atau password salah!";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Masuk - ITPLN Connect</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; background:var(--bg);">
  <div style="width:100%; max-width:420px;">
    <!-- Logo -->
    <div style="text-align:center; margin-bottom:32px;">
      <div style="display:inline-flex; align-items:center; gap:10px; margin-bottom:8px;">
        <div class="brand-icon" style="width:44px; height:44px;">
          <i class="fa-solid fa-graduation-cap" style="color:white; font-size:1.2rem;"></i>
        </div>
        <span class="brand-text" style="font-size:1.6rem;">ITPLN Connect</span>
      </div>
      <p style="color:var(--text-muted); font-size:0.9rem;">Masuk ke akun kamu</p>
    </div>

    <div class="card" style="padding:32px;">
      <a href="../index.php" class="btn btn-sm btn-outline-primary" style="margin-bottom:20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
      
      <h2 style="font-size:1.4rem; margin-bottom:4px;">Masuk</h2>
      <p style="color:var(--text-muted); font-size:0.88rem; margin-bottom:24px;">Selamat datang kembali!</p>

      <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i> Registrasi berhasil! Silakan masuk.
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['need_login'])): ?>
        <div class="alert alert-info">
          <i class="fa-solid fa-circle-info"></i> Silakan masuk terlebih dahulu untuk melanjutkan.
        </div>
      <?php endif; ?>

      <?php if (isset($error)): ?>
        <div class="alert alert-error">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= $error; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <?php
            $errorMsg = [
              'google_signin_failed' => 'Login Google dibatalkan atau gagal.',
              'token_exchange_failed' => 'Gagal menukar token Google.',
              'no_access_token' => 'Tidak dapat mengakses token Google.',
              'no_user_info' => 'Gagal mendapatkan informasi user dari Google.',
              'registration_failed' => 'Gagal membuat akun baru.'
            ];
            echo $errorMsg[$_GET['error']] ?? 'Terjadi kesalahan saat login dengan Google.';
          ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="email@example.com" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        
        <button class="btn" name="login" style="width:100%; justify-content:center; margin-top:4px;">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
        </button>
      </form>

      <div class="divider">
        <span>ATAU</span>
      </div>

      <a href="google-login.php" class="btn-google">
        <svg width="20" height="20" viewBox="0 0 48 48">
          <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
          <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
          <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
          <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
        </svg>
        Masuk dengan Google
      </a>

      <p style="text-align:center; color:var(--text-muted); font-size:0.88rem; margin-top:8px;">
        Belum punya akun? <a href="register.php" style="color:var(--primary); font-weight:600;">Daftar</a>
      </p>
    </div>
  </div>
</div>

</body>
</html>
