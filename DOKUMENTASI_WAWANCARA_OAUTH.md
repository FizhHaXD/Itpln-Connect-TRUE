# 🎓 Dokumentasi Wawancara: Integrasi Google OAuth 2.0

Dokumen ini disusun sebagai panduan untuk presentasi/wawancara terkait bagaimana sistem autentikasi Google (OAuth 2.0) diimplementasikan pada proyek ini.

---

## 1. Konsep Dasar: Apa itu OAuth 2.0?
**OAuth 2.0 (Open Authorization)** adalah standar protokol industri untuk otorisasi. 
Sederhananya: Sistem kita (ITPLN Connect) meminta *izin* ke Google untuk mengakses profil dasar user (nama, email, foto) *tanpa* meminta password Google user tersebut.

### Alur Kerja (Flow) Aplikasi Ini:
1. **User Request:** User klik "Login dengan Google".
2. **Redirect to Google:** Aplikasi mengarahkan user ke halaman login Google sambil membawa `Client ID`.
3. **User Consent:** User login ke Google dan menyetujui izin akses (Consent Screen).
4. **Authorization Code:** Google melempar kembali user ke aplikasi kita (`google-callback.php`) beserta sebuah `code`.
5. **Token Exchange:** Aplikasi kita (di belakang layar) menukarkan `code` tersebut dengan **Access Token** ke server Google.
6. **Fetch Data:** Menggunakan Access Token, aplikasi kita meminta data diri user dari Google API.
7. **Login System:** Aplikasi mengecek database. Jika ada, set session login. Jika tidak, buat akun baru lalu set session login.

---

## 2. Keputusan Teknis (Technical Decision)
**Pertanyaan Penguji:** *"Kenapa kamu menggunakan cURL manual, bukan memakai library resmi `google/apiclient` via Composer?"*

**Jawaban Anda:**
> "Saya memilih menggunakan **cURL PHP bawaan (Native HTTP Request)** karena kebutuhan aplikasi ini hanya untuk autentikasi sederhana (mendapatkan email dan nama profil). 
> 
> Menggunakan library resmi `google/apiclient` via Composer akan mengunduh ratusan file dan memakan puluhan Megabyte (`vendor/`) yang membebani server dan membuat aplikasi jadi terlalu berat (*overkill*). Dengan pendekatan cURL, logika autentikasi menjadi lebih transparan, *zero-dependency* (tanpa ketergantungan pihak ketiga), dan performa server jauh lebih cepat."

---

## 3. Bedah Kode (Penjelasan File)

Di sini kita memiliki 3 file utama yang saling berinteraksi:

### A. `config/google_config.php` (Pusat Konfigurasi)
Menyimpan "Kunci" komunikasi dengan Google.
- `GOOGLE_CLIENT_ID`: Identitas aplikasi kita yang terdaftar di Google Cloud.
- `GOOGLE_CLIENT_SECRET`: Kunci rahasia (password aplikasi) untuk menukar Access Token.
- `GOOGLE_REDIRECT_URI`: Halaman pendaratan setelah user selesai login dari Google.

### B. `auth/google-login.php` (Pengantar / Jembatan Pertama)
File ini merakit URL dan melempar user ke Google.
```php
$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code', // Kita meminta Authorization Code
    'scope' => 'email profile', // Kita hanya minta izin baca email dan nama
];
$googleAuthUrl = GOOGLE_AUTH_URL . '?' . http_build_query($params);
header("Location: $googleAuthUrl"); // Lempar ke Google
```

### C. `auth/google-callback.php` (Otak Autentikasi / Jembatan Kedua)
Ini adalah file terpenting. 
**Langkah 1: Menukar Code menjadi Access Token**
```php
$tokenData = [
    'code' => $_GET['code'], // Code yang didapat dari Google
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];
// Menggunakan cURL (HTTP POST) untuk meminta Access Token
$ch = curl_init(GOOGLE_TOKEN_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
```

**Langkah 2: Menggunakan Access Token untuk Mengambil Profil**
```php
$accessToken = $tokenResponse['access_token'];

// Menggunakan cURL (HTTP GET) untuk meminta data diri dari Google
$ch = curl_init(GOOGLE_USERINFO_URL . '?access_token=' . $accessToken);
$userInfoResponse = curl_exec($ch);
$userInfo = json_decode($userInfoResponse, true); // Berisi email, name, id
```

**Langkah 3: Sinkronisasi ke Database (Tabel Users)**
1. Cek apakah `$userInfo['email']` sudah ada di tabel `users`.
2. Jika **SUDAH ADA**: Set `$_SESSION['id_user']` (Login Sukses).
3. Jika **BELUM ADA**: Lakukan `INSERT INTO users` dengan data nama & email dari Google, buatkan password acak secara otomatis, lalu Set `$_SESSION` (Register & Login Sukses).

---

## 4. Keamanan Sistem (Security Measures)
Jika penguji menanyakan aspek keamanan:
1. **Password Aman:** Saat user login via Google pertama kali, sistem akan membuatkan password acak rahasia yang di-hash dengan `PASSWORD_BCRYPT` (`password_hash()`), sehingga struktur database tidak ada yang kosong atau rawan diretas.
2. **Kerahasiaan Secret:** `GOOGLE_CLIENT_SECRET` disimpan terpisah di file `google_config.php`. (Saat naik ke server production, file ini harus dijaga ketat).
3. **Session Based:** Aplikasi menggunakan Session bawaan PHP (`session_start()`) yang menyimpan status login di server, sehingga tidak rawan manipulasi *cookies* dari sisi client.
