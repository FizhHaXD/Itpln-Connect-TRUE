<?php
session_start();
require_once "../config/database.php";
require_once "../config/google_config.php";

// Check for error
if (isset($_GET['error'])) {
    header("Location: login.php?error=google_signin_failed");
    exit();
}

// ==========================================
// 1. TANGKAP "SURAT IZIN" DARI GOOGLE
// ==========================================
// Setelah user login, Google melempar user ke halaman ini sambil membawa parameter ?code=...
if (!isset($_GET['code'])) {
    header("Location: login.php");
    exit();
}
$code = $_GET['code'];

// ==========================================
// 2. TUKARKAN "SURAT IZIN" DENGAN "KUNCI AKSES" (ACCESS TOKEN)
// ==========================================
// Siapkan paket data untuk dikirim ke Google via POST
$tokenData = [
    'code' => $code, // Code yang baru saja kita dapatkan
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code' // Kita meminta Google: "Tukar code ini jadi token"
];

// Jalankan request HTTP (cURL) ke server Google untuk menukar Token
$ch = curl_init(GOOGLE_TOKEN_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true); // Harus POST
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ⚠️ Untuk development (localhost) only, di server asli harus dihapus/true!

$response = curl_exec($ch); // Eksekusi dan dapatkan balasannya
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    header("Location: login.php?error=token_exchange_failed");
    exit();
}

$tokenResponse = json_decode($response, true);

if (!isset($tokenResponse['access_token'])) {
    header("Location: login.php?error=no_access_token");
    exit();
}

$accessToken = $tokenResponse['access_token'];

// ==========================================
// 3. GUNAKAN ACCESS TOKEN UNTUK MEMINTA DATA PROFIL
// ==========================================
// Sekarang kita punya kunci, ayo minta nama, email, dan foto user dari Google
$ch = curl_init(GOOGLE_USERINFO_URL . '?access_token=' . $accessToken);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ⚠️ Untuk development (localhost) only

$userInfoResponse = curl_exec($ch); // Ambil balasannya
curl_close($ch);

$userInfo = json_decode($userInfoResponse, true); // Ubah teks JSON dari Google menjadi Array PHP

if (!isset($userInfo['email'])) {
    header("Location: login.php?error=no_user_info");
    exit();
}

// Extract user data
$email = mysqli_real_escape_string($conn, $userInfo['email']);
$nama = mysqli_real_escape_string($conn, $userInfo['name']);
$google_id = mysqli_real_escape_string($conn, $userInfo['id']);
$foto_google = isset($userInfo['picture']) ? $userInfo['picture'] : '';

// ==========================================
// 4. SINKRONISASI DENGAN DATABASE
// ==========================================
// Cek apakah email user sudah terdaftar di website kita
$checkUser = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if (mysqli_num_rows($checkUser) > 0) {
    // KONDISI A: User SUDAH PERNAH terdaftar (Login biasa)
    $user = mysqli_fetch_assoc($checkUser);
    
    // Jika dia dulu daftar manual, lalu sekarang login Google, kita update kolom google_id-nya biar terhubung
    if (empty($user['google_id'])) {
        mysqli_query($conn, "UPDATE users SET google_id='$google_id' WHERE id_user={$user['id_user']}");
    }
    
    // Set Sesi (Tandanya dia berhasil masuk)
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    
} else {
    // KONDISI B: User BELUM PERNAH terdaftar (Auto-Register)
    // Buat password acak yang aman (karena user login tanpa password)
    $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
    
    // Masukkan data baru ke database otomatis
    $query = mysqli_query($conn, "
        INSERT INTO users (nama, email, password, google_id, created_at)
        VALUES ('$nama', '$email', '$randomPassword', '$google_id', NOW())
    ");
    
    if ($query) {
        $newUserId = mysqli_insert_id($conn);
        
        // Set Sesi (Tandanya berhasil register sekaligus login)
        $_SESSION['id_user'] = $newUserId;
        $_SESSION['nama'] = $nama;
        
        // Download and save profile picture if available
        if (!empty($foto_google)) {
            $ext = 'jpg';
            $fotoName = 'profile_' . $newUserId . '_google.' . $ext;
            $fotoPath = "../uploads/profiles/" . $fotoName;
            
            $imageData = @file_get_contents($foto_google);
            if ($imageData) {
                file_put_contents($fotoPath, $imageData);
                mysqli_query($conn, "UPDATE users SET foto='$fotoName' WHERE id_user=$newUserId");
            }
        }
    } else {
        header("Location: login.php?error=registration_failed");
        exit();
    }
}

// Redirect to dashboard
if (isset($_GET['return'])) {
    header("Location: ../" . $_GET['return']);
} else {
    header("Location: ../dashboard/index.php");
}
exit();
