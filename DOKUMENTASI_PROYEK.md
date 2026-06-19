# Dokumentasi Proyek: ITPLN Connect (Kampus Connect)

## 1. Pendahuluan
**ITPLN Connect** (atau Kampus Connect) adalah sebuah platform web untuk mahasiswa kampus (khususnya ITPLN) yang bertujuan sebagai wadah komunitas, pengelolaan event, diskusi, pencarian rekan belajar (study group), dan komunikasi antar mahasiswa.

Platform ini dibangun menggunakan arsitektur **PHP Native (Procedural)** dengan basis data **MySQL**, serta styling modern menggunakan **CSS murni** dan ikon dari **FontAwesome**.

## 2. Struktur Direktori Utama
Proyek ini memiliki struktur modular agar lebih mudah dikelola:
- `/admin` - Panel khusus untuk admin platform.
- `/assets` - File statis (CSS, JS, gambar).
- `/auth` - Sistem autentikasi (login, register, logout, Google OAuth).
- `/chat` - Fitur pesan langsung (Direct Message) antar pengguna.
- `/communities` - Modul komunitas (daftar, detail, post komunitas, anggota).
- `/config` - Konfigurasi inti (koneksi database, kredensial Google OAuth, security).
- `/dashboard` - Halaman utama setelah user berhasil login (Feed pengguna).
- `/database` - File SQL untuk setup struktur tabel dan update database.
- `/events` - Modul acara/event kampus (daftar event, detail, partisipasi).
- `/includes` - Komponen UI yang digunakan berulang (navbar, footer).
- `/posts` - Modul postingan/feed global.
- `/profile` - Manajemen profil pengguna.
- `/study` - Modul "Study Groups" & "Mentoring".
- `/uploads` - Direktori penyimpanan file yang diunggah pengguna (foto profil, foto komunitas, dll).

## 3. Penjelasan Pola Kode Penting (Berulang)
Di dalam proyek PHP native ini, terdapat beberapa pola kode (code pattern) yang digunakan secara berulang di hampir seluruh halaman.

### A. Pola Koneksi & Inisiasi Sesi
Setiap file yang membutuhkan akses ke database dan mengecek status login pengguna akan selalu diawali dengan:

```php
<?php
// Memasukkan konfigurasi database
include "../config/database.php"; 

// Memulai atau melanjutkan sesi PHP
session_start();

// Proteksi Halaman: Jika user belum login, lemparkan kembali ke halaman login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
```
**Penjelasan:**
- `include "../config/database.php"` digunakan untuk mendapatkan variabel `$conn` (koneksi MySQL) dan konstanta `BASE_URL`.
- `session_start()` wajib dipanggil sebelum kita bisa mengakses `$_SESSION`.
- **Proteksi Halaman:** Baris `if (!isset(...))` bertindak sebagai *middleware* sederhana untuk melindungi rute privat.

### B. Pola Pemanggilan Layout (Header & Footer)
Untuk menghindari redundansi HTML (seperti tag `<head>`, `<nav>`, dan `<footer>`), proyek ini memisahkan bagian-bagian tersebut ke dalam folder `includes/`.

```php
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Judul Halaman</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Memanggil Navbar -->
<?php include "../includes/navbar.php"; ?>

<main>
    <!-- KONTEN UTAMA HALAMAN DI SINI -->
</main>

<!-- Memanggil Footer -->
<?php include "../includes/footer.php"; ?>

</body>
</html>
```

### C. Pola Query Database (Menampilkan Data)
Sistem ini menggunakan ekstensi `mysqli` secara prosedural. Untuk mengambil dan menampilkan data (contoh: mengambil data komunitas):

```php
// 1. Eksekusi Query
$query = mysqli_query($conn, "SELECT * FROM communities ORDER BY created_at DESC");

// 2. Looping data di dalam HTML
<?php if (mysqli_num_rows($query) > 0): ?>
    <div class="grid">
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="card">
                <h3><?= htmlspecialchars($row['nama_community']); ?></h3>
                <p><?= htmlspecialchars($row['deskripsi']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>Belum ada data.</p>
<?php endif; ?>
```
**Catatan Keamanan:** Selalu digunakan fungsi `htmlspecialchars()` ketika me-render data ke HTML untuk mencegah serangan **XSS (Cross-Site Scripting)**.

### D. Pola Penanganan Form (Insert/Update)
Ketika ada form yang disubmit via metode POST, polanya adalah menangkap input, membersihkan data, dan melakukan query `INSERT` atau `UPDATE`.

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Tangkap & bersihkan data untuk mencegah SQL Injection
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $id_user = $_SESSION['id_user'];
    
    // 2. Eksekusi query
    $insert = mysqli_query($conn, "INSERT INTO tabel_tujuan (nama, id_user) VALUES ('$nama', $id_user)");
    
    // 3. Handle hasil dan redirect
    if ($insert) {
        header("Location: index.php?status=success");
        exit();
    } else {
        $error = "Terjadi kesalahan: " . mysqli_error($conn);
    }
}
```

## 4. Logika dan Kode Unik (Advanced Patterns)
Selain pola dasar di atas, terdapat beberapa implementasi kode yang cukup unik dan spesifik untuk fitur-fitur kompleks.

### A. Alur Kerja Google OAuth (`/auth/google-callback.php`)
Fitur Login dengan Google tidak menggunakan *library* tambahan berat, melainkan menggunakan `cURL` murni untuk menukar "kode otorisasi" dengan data profil.

**Logika Utama:**
1. **Token Exchange:** Mengambil `$_GET['code']` yang dikembalikan Google, lalu mengirim permintaan POST (`curl_init()`) ke URL token Google beserta `client_id` dan `client_secret`.
2. **Fetch User Info:** Mendapatkan `access_token` yang valid, lalu melakukan `cURL` kedua ke `https://www.googleapis.com/oauth2/v2/userinfo` untuk mengambil nama, email, dan foto pengguna.
3. **Auto-Registration (Sinkronisasi):** Jika email belum ada di *database*, sistem otomatis akan membuatkan baris baru (`INSERT INTO users`) dengan `password` acak (karena autentikasi ditangani Google). Jika foto dari Google tersedia, `file_get_contents()` digunakan untuk mengunduh foto profil langsung ke direktori `/uploads/profiles/`.

### B. Kalkulasi Status Online & Relasi Chat (`/chat/chat_list.php`)
Fitur chat didesain sedemikian rupa agar seorang mahasiswa hanya dapat mengirim pesan ke **orang yang berada di komunitas atau acara yang sama**. Kalkulasi status *online* juga tidak bergantung pada *WebSockets*, melainkan manipulasi waktu di tingkat MySQL.

```sql
SELECT DISTINCT 
    u.id_user, 
    u.nama, 
    -- Kalkulasi Status Online Dinamis:
    -- Jika waktu aktif terakhir (last_activity) kurang dari atau sama dengan 5 menit yang lalu, maka dianggap online (1)
    IF(TIMESTAMPDIFF(MINUTE, u.last_activity, NOW()) <= 5, 1, 0) as is_online
FROM users u
WHERE u.id_user != $id_user 
AND (
    -- Sub-query Relasi Komunitas: 
    -- Mengecek apakah user ini tergabung di tabel community_members di komunitas yang sama
    u.id_user IN (
        SELECT cm2.id_user 
        FROM community_members cm1
        JOIN community_members cm2 ON cm1.id_community = cm2.id_community
        WHERE cm1.id_user = $id_user AND cm2.id_user != $id_user
    )
    OR
    -- Sub-query Relasi Event: Sama seperti di atas
    u.id_user IN (
        SELECT ep2.id_user 
        FROM event_participants ep1
        JOIN event_participants ep2 ON ep1.id_event = ep2.id_event
        WHERE ep1.id_user = $id_user AND ep2.id_user != $id_user
    )
)
```
Setiap kali pengguna memuat halaman `chat_list.php`, sistem otomatis menjalankan `UPDATE users SET last_activity = NOW()`.

### C. Carousel & Ekstraksi Post Terpopuler (`/index.php`)
Pada halaman Landing (*Beranda tamu*), terdapat *Carousel* interaktif menggunakan *Vanilla JavaScript*.
Sistem me-lempar data hasil *query* MySQL secara langsung ke JavaScript lewat manipulasi JSON:

```php
// PHP merender JSON ke dalam variabel JavaScript di dalam tag <script>
const posts = <?= json_encode(mysqli_fetch_all($carouselPosts, MYSQLI_ASSOC)) ?>;
```
Pendekatan ini sangat efisien dibanding harus menggunakan *AJAX* tambahan hanya untuk mengambil *feed* awal.

## 5. Struktur & Penjelasan Arsitektur CSS
Salah satu daya tarik platform ini adalah estetika dan penggunaan teknik desain modern. Semua pengaturan CSS terletak di `/assets/css/` yang dibuat sangat **modular**.

### A. Pemisahan Berkas (Modularitas)
- `base.css`: Mendefinisikan reset global, *font face* impor (menggunakan Google Fonts seperti 'Plus Jakarta Sans'), dan variabel-variabel warna.
- `layout.css`: Mengatur struktur global seperti Navbar lengket (*sticky*), sistem grid kolom utama (contoh `.grid-2`), dan Footer raksasa (*Fat Footer*).
- `components.css`: Khusus mengatur bentuk komponen modular seperti `.btn` (tombol-tombol bergradasi), `.card` (kartu postingan/profil yang memiliki efek melayang saat di-*hover*), dan input *form*.
- `features.css` & `study.css`: Spesifik menampung tata letak *grid* dan *list* untuk halaman fitur tertentu.

### B. Gaya Desain Visual: Neo-Brutalism (Pop UI) & Modern Styling
Alih-alih menggunakan desain *Clean UI* biasa dengan bayangan halus atau *Glassmorphism* (kaca transparan), platform ini secara dominan mengadopsi gaya visual **Neo-Brutalism** atau Pop UI yang kental dengan nuansa anak muda dan *playful*.
Ciri khas dari gaya ini terlihat sangat jelas pada pengaturan CSS untuk *card* dan *button* di file `components.css`:

```css
/* Contoh dari assets/css/components.css */
.card {
  background: var(--card); /* Warna solid (putih/terang) */
  border: var(--border-width) solid var(--border); /* Garis tepi yang tegas */
  border-bottom-width: 5px; /* Garis bawah yang sengaja ditebalkan */
  border-radius: var(--radius-lg);
  padding: 24px;
  
  /* Bayangan awal yang statis */
  box-shadow: var(--shadow); 
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover { 
  /* Efek melayang tajam (hard shadow) berwarna hitam pekat */
  box-shadow: 8px 8px 0px 0px #000000; 
  transform: translateY(-4px) translateX(-4px); 
}
```
**Penjelasan:**
- **Neo-Brutalism (Hard Shadows & Thick Borders):** Semua kartu (*card*), *container*, dan elemen antarmuka dirancang dengan *border* tegas (garis tepi tebal, terutama aksen di bagian bawah) serta menggunakan *box-shadow* berwarna hitam solid (tanpa efek *blur*). Hal ini menciptakan kesan "kasar" namun tertata (*bold*).
- **Micro-Interactions yang Dinamis:** Transisi CSS seperti pergerakan `translateY(-4px) translateX(-4px)` saat di-*hover* memunculkan ilusi elemen yang tiba-tiba "melompat" atau terangkat ke kiri atas, memperkuat estetika interaktif dari Neo-Brutalism.
- Walaupun *Navbar* memiliki efek blur sederhana (`backdrop-filter`), identitas visual inti dari aplikasi ini sejatinya digerakkan oleh desain Neo-Brutalism dan struktur tata letak modern yang menggunakan `display: grid;` serta variabel warna sentral.

## 6. Fitur-Fitur Utama Proyek

### 1. Autentikasi & Akun (`/auth`, `/profile`)
- **Register & Login Tradisional:** Menggunakan email/username dan password yang di-hash dengan fungsi `password_hash()`.
- **Google OAuth:** Memungkinkan mahasiswa login cepat menggunakan akun Google (Kampus). Konfigurasinya berada di `config/google_config.php`.
- **Manajemen Profil:** Pengguna dapat mengubah foto profil, biodata, dan informasi kontak.

### 2. Komunitas (`/communities`)
- **Eksplorasi Komunitas:** Pengguna dapat melihat daftar komunitas berdasarkan kategori (Teknologi, Seni, Olahraga, dll).
- **Gabung Komunitas:** Relasi many-to-many antara pengguna dan komunitas dicatat dalam tabel *members*.
- **Postingan Internal:** Setiap komunitas memiliki *feed* khusus untuk anggota komunitasnya berdiskusi.

### 3. Event (`/events`)
- Modul untuk mempublikasikan acara. Event dapat terikat pada sebuah komunitas atau bersifat umum/global.
- Terdapat fitur RSVP/Join event yang akan mencatat kehadiran pengguna di tabel `event_participants`.

### 4. Direct Message / Chat (`/chat`)
- Sistem *real-time* sederhana berbasis database (atau *polling* JS).
- Tabel `chats` mencatat `sender_id`, `receiver_id`, dan `message`. Digunakan untuk diskusi pribadi (1-on-1).

### 5. Study Groups & Mentoring (`/study`)
- Fitur pencarian teman belajar.
- Mahasiswa dapat membuka sesi *"Belajar Bersama"* atau *"Open Mentoring"*.
- Terdiri dari tabel `study_groups` (jadwal, materi, tipe) dan `study_group_members` (pendaftar sesi).

## 7. Konfigurasi Sistem (Deployment / Setup)

Jika Anda ingin menjalankan atau memindahkan aplikasi ini ke server lain, berikut konfigurasinya yang terletak di `config/database.php`:

```php
// CONTOH config/database.php

// Kredensial Database
$host = "localhost";
$user = "root"; // sesuaikan
$pass = "";     // sesuaikan
$db   = "kampus_connect"; // sesuaikan

// Deteksi BASE_URL dinamis
// Skrip ini secara otomatis mendeteksi URL path project
// sehingga tidak perlu mengganti link secara manual saat dipindah ke server.
```

Untuk inisialisasi awal, semua struktur tabel dapat diimpor langsung dari folder `/database`. File utama adalah `SETUP_TABLES.sql`.

---
*Dokumentasi ini mencakup pemahaman arsitektur secara keseluruhan agar developer atau kontributor baru dapat dengan mudah melanjutkan pengembangan sistem Kampus Connect.*
