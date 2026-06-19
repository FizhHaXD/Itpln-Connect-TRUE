<?php
session_start();
require_once "../config/database.php";
require_once "../config/google_config.php";

// Check if Google OAuth is configured
if (!isGoogleOAuthConfigured()) {
    die("
    <h2>⚠️ Google OAuth Belum Dikonfigurasi</h2>
    <p>Silakan setup terlebih dahulu:</p>
    <ol>
        <li>Baca file <code>GOOGLE_OAUTH_SETUP.md</code></li>
        <li>Buat Google Cloud Project</li>
        <li>Dapatkan Client ID & Secret</li>
        <li>Update file <code>config/google_config.php</code></li>
    </ol>
    <a href='login.php'>← Kembali ke Login</a>
    ");
}

// ==========================================
// RAKIT URL MENUJU GOOGLE
// ==========================================
$params = [
    'client_id' => GOOGLE_CLIENT_ID,      // Identitas aplikasi kita
    'redirect_uri' => GOOGLE_REDIRECT_URI,// Kemana Google harus mengembalikan user setelah login
    'response_type' => 'code',            // Kita meminta "Authorization Code" sebagai bukti izin sementara
    'scope' => 'email profile',           // Hak akses apa saja yang kita minta (hanya email & nama)
    'access_type' => 'online',            // Akses standar (kita tidak butuh offline access / refresh token)
    'prompt' => 'select_account'          // Selalu paksa user untuk memilih akun (biar bisa ganti akun)
];

// Gabungkan URL dasar Google Auth dengan parameter di atas menjadi 1 link utuh
$googleAuthUrl = GOOGLE_AUTH_URL . '?' . http_build_query($params);

// ==========================================
// REDIRECT (LEMPAR USER KE GOOGLE)
// ==========================================
header("Location: $googleAuthUrl");
exit();
