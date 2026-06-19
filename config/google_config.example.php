<?php
/**
 * ⚠️ PENTING: Setup Google OAuth terlebih dahulu!
 * Baca file GOOGLE_OAUTH_SETUP.md untuk instruksi lengkap
 * 
 * Setelah setup:
 * 1. Ganti YOUR_GOOGLE_CLIENT_ID_HERE dengan Client ID dari Google Console
 * 2. Ganti YOUR_GOOGLE_CLIENT_SECRET_HERE dengan Client Secret
 * 3. Sesuaikan GOOGLE_REDIRECT_URI dengan URL lokal/production kamu
 */

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');

// 3. GOOGLE_REDIRECT_URI
// Development: http://localhost/PROJEK_UAS/auth/google-callback.php
// Production: https://yourdomain.com/auth/google-callback.php
define('GOOGLE_REDIRECT_URI', 'http://localhost/PROJEK_UAS/auth/google-callback.php');

// ==========================================
// Google OAuth URLs
// ==========================================
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// Helper function to check if configured
function isGoogleOAuthConfigured() {
    return GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID_HERE' 
        && GOOGLE_CLIENT_SECRET !== 'YOUR_GOOGLE_CLIENT_SECRET_HERE';
}
?>
