# ITPLN-Connect (KampusKu)

Sistem Jejaring Sosial dan Akademik Mahasiswa yang dibangun menggunakan PHP Native dan Vanilla CSS. Aplikasi ini dirancang untuk membantu mahasiswa berinteraksi, mengelola diskusi, berbagi referensi perkuliahan, serta mengeksplorasi komunitas kampus secara terintegrasi.

## Problem Solving

Aplikasi ini dibuat untuk menyelesaikan masalah utama dalam komunikasi dan pengelolaan informasi di lingkungan akademik kampus:

- Mengurangi kesulitan pencarian materi kuliah dan referensi belajar dari angkatan sebelumnya.
- Mempercepat penyebaran informasi terkait agenda kampus dan kegiatan komunitas/UKM.
- Menyediakan wadah terpusat (*one-stop platform*) untuk diskusi lintas jurusan dan angkatan.
- Memadukan kemudahan antarmuka jejaring sosial dengan kebutuhan spesifik akademik mahasiswa.

## Fungsi Utama

1. Global Posts (Timeline Diskusi)
   - Tampilan beranda interaktif untuk berbagi pemikiran, bertanya, dan berdiskusi.
   - Menampilkan postingan terbaru dari semua pengguna terdaftar secara *real-time*.
   <img width="1818" height="912" alt="Tampilan Global Posts" src="https://via.placeholder.com/1818x912.png?text=Screenshot+Global+Posts" />

2. Communities (Manajemen Komunitas)
   - Direktori lengkap Unit Kegiatan Mahasiswa (UKM) dan komunitas hobi/keilmuan.
   - Menyediakan fitur pendaftaran anggota dan ruang khusus bagi anggota komunitas.
   <img width="1812" height="892" alt="Tampilan Komunitas" src="https://via.placeholder.com/1812x892.png?text=Screenshot+Communities" />

3. Study Center (Bank Materi)
   - Area khusus untuk mengunggah dan mengunduh referensi belajar berupa dokumen PDF/Slide.
   - Materi dikelompokkan secara rapi berdasarkan mata kuliah agar mudah dicari oleh mahasiswa.
   <img width="1823" height="895" alt="Tampilan Pusat Belajar" src="https://via.placeholder.com/1823x895.png?text=Screenshot+Study+Center" />

4. Live Chat & Agenda Kalender
   - Chat interface sederhana untuk komunikasi instan antarmahasiswa.
   - Kalender terpadu untuk memantau jadwal kegiatan dan acara penting kampus.
   <img width="1825" height="910" alt="Tampilan Chat dan Agenda" src="https://via.placeholder.com/1825x910.png?text=Screenshot+Chat+and+Events" />

## Teknologi & Tools

- **PHP Native (Procedural)**
  - Bahasa pemrograman backend utama untuk pemrosesan logika server, *routing*, dan manajemen *session*.

- **MySQL / MariaDB**
  - Sistem manajemen database relasional (RDBMS) tangguh untuk menyimpan data pengguna, materi, komunitas, dan riwayat obrolan.

- **Vanilla CSS & Custom Design System**
  - *Styling* antarmuka dilakukan secara mandiri (tanpa framework berat seperti Bootstrap/Tailwind) untuk memastikan ukuran file tetap kecil dan render halaman yang super cepat (*zero-bloatware*).

- **Vanilla JavaScript**
  - Mengelola interaksi *User Interface*, validasi form, dan proses asinkron (AJAX) secara efisien di sisi klien.

- **Google OAuth 2.0 API**
  - Integrasi keamanan pihak ketiga yang memungkinkan mahasiswa melakukan *login* instan menggunakan akun Google secara aman.

## Struktur Proyek

- `index.php` - Halaman utama beranda, memuat *Timeline* dan navigasi dasar.
- `study/` - Modul Pusat Belajar (Bank Materi) dan manajemen pengunggahan dokumen akademik.
- `communities/` - Modul direktori komunitas, detail UKM, dan pendaftaran keanggotaan.
- `events/` - Modul pengelolaan acara, agenda rutin, dan kalender kegiatan kampus.
- `auth/` - Modul autentikasi pengguna, termasuk pemrosesan *callback* dari Google OAuth.
- `config/` - Penyimpanan file konfigurasi koneksi database dan kredensial API rahasia.

## Cara Menjalankan

1. *Clone* repositori ke dalam direktori web server lokal Anda (`htdocs` untuk XAMPP, atau `www` untuk Laragon):

```bash
git clone https://github.com/FizhHaXD/Itpln-Connect-TRUE.git PROJEK_UAS
```

2. Buat database baru bernama `kampus_connect`, lalu import struktur tabel dasar dari file SQL berikut:

```text
database/SETUP_TABLES.sql
```

3. Siapkan file konfigurasi rahasia:
   - Salin dan ubah nama file `config/database.example.php` menjadi `config/database.php`, lalu sesuaikan *username/password* server lokal Anda.
   - **Bagi Dosen Penguji:** Ekstrak file ZIP konfigurasi (yang dikumpulkan di portal kampus), lalu letakkan file `database.php` dan `google_config.php` ke dalam folder `config/`.

4. Jalankan layanan Apache & MySQL, kemudian buka browser Anda di:

```text
http://localhost/PROJEK_UAS
```

## Catatan

- Aplikasi sangat bergantung pada integrasi akun Google. Jika tidak terdapat *API Key* dan *Client Secret* yang valid di dalam `config/google_config.php`, maka fitur Login Google otomatis tidak akan berfungsi.
- Folder `uploads/` sengaja diabaikan di repositori (*gitignored*) untuk menghindari pembengkakan ukuran server. Folder dan file unggahan pengguna akan dibuat secara lokal saat aplikasi dijalankan.
- File konfigurasi sengaja dirancang menggunakan sistem `.example.php` untuk mematuhi praktik keamanan siber yang baik.
