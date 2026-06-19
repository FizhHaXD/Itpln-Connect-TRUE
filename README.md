# ITPLN-Connect (KampusKu)

![PHP](https://img.shields.io/badge/PHP_8+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Vanilla CSS](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Google OAuth](https://img.shields.io/badge/Google_OAuth_2.0-4285F4?style=for-the-badge&logo=google&logoColor=white)

Sistem jejaring sosial dan akademik terintegrasi yang dibangun menggunakan PHP Native dan Vanilla CSS. Aplikasi ini dirancang untuk membantu mahasiswa dan civitas akademika mengelola informasi kampus, memproses distribusi materi kuliah, dan memfasilitasi diskusi lintas komunitas.

---

## Daftar Isi
- [Problem Solving](#problem-solving)
- [Fungsi Utama](#fungsi-utama)
- [Arsitektur & Alur Kerja](#arsitektur--alur-kerja)
- [Teknologi & Tools](#teknologi--tools)
- [Struktur Proyek](#struktur-proyek)
- [Cara Menjalankan](#cara-menjalankan)
- [Catatan Tambahan](#catatan-tambahan)

---

## Problem Solving

Aplikasi ini dibuat untuk menyelesaikan masalah utama dalam pengelolaan informasi di lingkungan kampus:

- Mengurangi fragmentasi penyebaran informasi akademik dan non-akademik.
- Mempercepat pencarian materi kuliah dan referensi belajar dari angkatan sebelumnya.
- Menyediakan wadah terpusat untuk diskusi dan komunikasi antar mahasiswa.
- Memadukan kebutuhan interaksi sosial dengan alur kerja akademik.

---

## Fungsi Utama

1. Global Timeline
   - Tampilan beranda interaktif untuk berbagi postingan dan diskusi.
   - Menampilkan aktivitas terkini dari seluruh pengguna secara real-time.
<br>
<img width="800" alt="Global Timeline" src="https://via.placeholder.com/800x400.png?text=Global+Timeline" />

2. Study Center
   - Area unggah dan unduh dokumen materi perkuliahan.
   - Menyediakan fitur pencarian dan pengelompokan file berdasarkan mata kuliah.
<br>
<img width="800" alt="Study Center" src="https://via.placeholder.com/800x400.png?text=Study+Center" />

3. Community Management
   - Menampilkan daftar Unit Kegiatan Mahasiswa (UKM) dan komunitas.
   - Menyediakan kontrol pendaftaran anggota dan ruang diskusi internal.
<br>
<img width="800" alt="Community Management" src="https://via.placeholder.com/800x400.png?text=Community+Management" />

4. Live Chat & Events
   - Chat interface untuk komunikasi personal antar pengguna.
   - Kalender akademik dan kalender agenda kegiatan kampus.
<br>
<img width="800" alt="Live Chat and Events" src="https://via.placeholder.com/800x400.png?text=Live+Chat+and+Events" />

---

## Arsitektur & Alur Kerja

Aplikasi mengimplementasikan pemisahan fungsi berdasarkan modul domain untuk kemudahan skalabilitas:

- **Authentication Flow:** Autentikasi Google Identity -> Verifikasi Callback -> Manajemen Sesi PHP.
- **Academic Flow:** Dasbor Study Center -> Unggah Dokumen PDF/Slide -> Kategorisasi Mata Kuliah.
- **Social Flow:** Beranda Utama -> Distribusi Postingan -> Notifikasi Aktivitas.

---

## Teknologi & Tools

| Komponen Utama | Teknologi | Deskripsi Penggunaan |
|---|---|---|
| **Backend Logic** | PHP Native (8+) | Mengelola pemrosesan server-side, routing, dan validasi sesi pengguna. |
| **Database** | MySQL / MariaDB | Sistem penyimpanan relasional untuk data pengguna, entitas komunitas, dan rekam obrolan. |
| **Styling** | Vanilla CSS | Desain antarmuka kustom tanpa framework eksternal untuk menjaga kecepatan render. |
| **Interaktivitas** | Vanilla JavaScript | Menangani operasi asinkron (AJAX) dan manipulasi DOM di sisi klien. |
| **Keamanan** | Google Identity Services | Standar OAuth 2.0 untuk proses login terpusat dan aman. |

---

## Struktur Proyek

```text
PROJEK_UAS/
├── admin/          # Panel kontrol manajemen administrator
├── assets/         # Aset statis (CSS, JS, sumber daya desain)
├── auth/           # Modul otentikasi dan callback Google OAuth
├── chat/           # Logika pemrosesan obrolan real-time
├── communities/    # Pengelolaan daftar dan anggota komunitas
├── config/         # Direktori konfigurasi kredensial (diabaikan oleh git)
├── database/       # Migrasi struktur skema tabel SQL
├── events/         # Pengelolaan kalender dan acara akademik
├── study/          # Logika bank materi dan manajemen dokumen
├── uploads/        # Repositori penyimpanan file unggahan lokal
└── index.php       # Berkas utama inisialisasi aplikasi
```

---

## Cara Menjalankan

1. Clone repositori ke dalam direktori server lokal (misalnya `htdocs` atau `www`):

```bash
git clone https://github.com/FizhHaXD/Itpln-Connect-TRUE.git PROJEK_UAS
```

2. Konfigurasi basis data `kampus_connect` dan lakukan import skema dasar:

```bash
# Gunakan antarmuka phpMyAdmin atau jalankan perintah CLI berikut:
mysql -u root -p kampus_connect < database/SETUP_TABLES.sql
```

3. Terapkan kredensial rahasia:
   - Duplikasi `config/database.example.php` menjadi `config/database.php` dan sesuaikan kredensial server.
   - Tambahkan berkas `google_config.php` yang memuat Google Client ID dan Secret ke dalam folder `config/`.

4. Inisialisasi aplikasi melalui browser pada alamat:

```text
http://localhost/PROJEK_UAS
```

---

## Catatan Tambahan

- Proyek ini dikembangkan secara spesifik untuk memenuhi persyaratan evaluasi Ujian Akhir Semester.
- Fitur autentikasi mewajibkan ketersediaan kredensial API Google OAuth yang terverifikasi.
- Desain arsitektural telah disiapkan agar dapat diperluas untuk integrasi layanan komputasi awan di fase pengembangan selanjutnya.
