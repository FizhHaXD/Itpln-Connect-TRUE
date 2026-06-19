# (ITPLN-Connect)

KampusKu (atau ITPLN-Connect) adalah sebuah platform web terintegrasi yang dibangun untuk menjawab kebutuhan mahasiswa akan ruang digital yang interaktif, informatif, dan kolaboratif. Proyek ini awalnya dikembangkan sebagai pemenuhan tugas Ujian Akhir Semester (UAS), namun dirancang dengan standar arsitektur dan UI/UX yang dapat terus dikembangkan ke depannya.

Latar belakang pembuatan aplikasi ini berangkat dari masalah tersebarnya informasi akademik dan non-akademik di lingkungan kampus. Seringkali mahasiswa kesulitan mencari referensi atau materi kuliah dari angkatan sebelumnya, atau tertinggal informasi mengenai kegiatan komunitas dan agenda kampus. Oleh karena itu, KampusKu hadir untuk menyatukan fungsi jejaring sosial dan pusat akademik dalam satu pintu.

## Sorotan Fitur

Aplikasi ini tidak hanya sekadar tempat ngobrol, melainkan memiliki beberapa modul utama yang saling mendukung:

- **Sistem Autentikasi Terintegrasi:** Daripada repot membuat akun baru dan menghafal password, pengguna bisa langsung masuk menggunakan akun Google mereka masing-masing. Sistem di balik layar menggunakan Google OAuth 2.0 API untuk memastikan keamanan data saat proses login berlangsung.
- **Ruang Interaksi Publik:** Terdapat linimasa (timeline) utama di mana mahasiswa bisa memposting pemikiran, bertanya, atau berbagi informasi yang bisa dilihat serta ditanggapi oleh seluruh pengguna terdaftar.
- **Ekosistem Komunitas:** Mahasiswa bisa menelusuri berbagai Unit Kegiatan Mahasiswa (UKM) atau komunitas belajar yang ada di kampus, melihat profil dan visi-misi komunitas tersebut, serta mendaftarkan diri secara langsung melalui platform.
- **Pusat Belajar (Bank Materi):** Ini adalah salah satu fitur paling krusial. Mahasiswa bisa saling berbagi modul, diktat, atau catatan kuliah yang dikelompokkan berdasarkan mata kuliah, sehingga adik tingkat bisa dengan mudah menemukan referensi belajar dari kakak tingkat mereka.
- **Kalender Agenda (Events):** Fitur manajemen acara ini memastikan tidak ada mahasiswa yang ketinggalan informasi acara penting kampus, seminar, perlombaan, atau sekadar kumpul rutin dari komunitas yang mereka ikuti.
- **Komunikasi Real-time:** Dilengkapi dengan fitur obrolan (Live Chat) sederhana agar diskusi antarmahasiswa tidak perlu berpindah ke aplikasi lain.

## Stack Teknologi

Proyek ini dibangun tanpa terlalu bergantung pada framework pihak ketiga (zero-bloatware) untuk memastikan performa yang cepat:
- **Frontend:** Antarmuka dibangun secara mandiri menggunakan HTML5 dan Vanilla CSS. Kami mengembangkan sistem desain (Design System) kustom sendiri alih-alih menggunakan framework berat seperti Bootstrap untuk menjaga ukuran file tetap kecil. Interaksi sisi klien ditangani murni menggunakan Vanilla JavaScript.
- **Backend:** Logika server dan pemrosesan data dikerjakan menggunakan PHP Native dengan pendekatan prosedural yang terstruktur dan mudah dibaca (maintainable).
- **Database:** Menggunakan MySQL/MariaDB dengan rancangan struktur relasional yang mengikat data entitas pengguna, komunitas, unggahan, dan riwayat obrolan secara efisien.

---

## Petunjuk Menjalankan Proyek di Komputer Lokal

Jika Anda adalah dosen penguji atau rekan pengembang yang ingin mencoba menjalankan proyek ini secara lokal, silakan ikuti langkah-langkah di bawah ini. Kami sudah mengatur agar proses instalasinya minim hambatan.

### Kebutuhan Sistem
Pastikan komputer Anda sudah terinstal aplikasi web server lokal seperti **XAMPP** atau **Laragon**, dan pastikan layanan Apache serta MySQL dalam keadaan berjalan.

### 1. Persiapan File Proyek
1. *Clone* repositori ini atau ekstrak file ZIP proyek ke dalam folder root server lokal Anda.
   - Jika menggunakan **XAMPP**: Letakkan di dalam folder `htdocs`.
   - Jika menggunakan **Laragon**: Letakkan di dalam folder `www`.
2. **Perhatian:** Sangat disarankan agar folder utama proyek ini diberi nama `PROJEK_UAS`. Hal ini untuk memastikan bahwa *routing* dan pemanggilan aset statis di dalam sistem berjalan dengan sempurna tanpa terjadi broken link.

### 2. Persiapan Database
1. Buka browser dan masuk ke aplikasi pengelola database (misalnya mengakses `http://localhost/phpmyadmin` atau melalui aplikasi semacam HeidiSQL).
2. Buat sebuah database kosong baru dan beri nama persis seperti ini: `kampus_connect`.
3. Buka folder `database/` yang ada di dalam proyek ini. Anda akan menemukan sekumpulan file dengan ekstensi `.sql`.
4. Import file SQL tersebut ke dalam database yang baru dibuat. Pastikan Anda memulai dengan mengimpor `SETUP_TABLES.sql` terlebih dahulu untuk membentuk kerangka dasar tabelnya.

### 3. Pengaturan Kredensial (Penting)
Karena aplikasi ini mengandalkan sistem login milik Google, server membutuhkan file konfigurasi yang memuat API Key rahasia (Client ID dan Client Secret). Mengunggah data rahasia seperti ini ke repositori publik seperti GitHub adalah praktik yang sangat berbahaya. Oleh sebab itu, file konfigurasi asli tidak ada di repositori ini.

**Khusus Bagi Dosen Penguji:**
Kami telah melampirkan file konfigurasi asli di dalam arsip ZIP terpisah yang diserahkan melalui portal e-learning kampus.
1. Ekstrak ZIP kredensial tersebut, di mana Anda akan menemukan file `database.php` dan `google_config.php`.
2. Salin dan pindahkan kedua file tersebut ke dalam folder `config/` yang ada di dalam proyek ini.
3. Selesai! Anda tidak perlu lagi repot mengatur API Key di Google Cloud Console dari awal.

*(Bagi pengembang luar: Jika Anda ingin mencobanya sendiri, ubah nama file `config/database.example.php` menjadi `database.php` agar koneksi database berfungsi. Namun, tanpa API Key yang valid di `google_config.php`, proses login Google akan gagal.)*

### 4. Menjalankan Aplikasi
Setelah tahapan di atas selesai:
1. Buka kembali browser Anda.
2. Ketikkan alamat `http://localhost/PROJEK_UAS/`
3. Halaman utama KampusKu akan muncul, dan Anda bisa langsung masuk untuk menguji berbagai fitur yang ada.

---
*Dikembangkan dengan penuh dedikasi sebagai Proyek Akhir Semester.*
