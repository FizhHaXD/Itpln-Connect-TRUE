# 🎓 KampusKu (ITPLN-Connect)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Vanilla CSS](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Google OAuth](https://img.shields.io/badge/Google_OAuth-4285F4?style=for-the-badge&logo=google&logoColor=white)

KampusKu adalah platform web sederhana buat mahasiswa untuk ngobrol, cari komunitas, dan berbagi materi kuliah. Dibuat pakai PHP Native dan Vanilla CSS murni tanpa framework tambahan supaya aplikasinya ringan dan gampang di-*maintain*.

Proyek ini awalnya dibuat untuk tugas Ujian Akhir Semester (UAS), tapi strukturnya sudah dirancang supaya bisa dipakai dan dikembangkan lebih lanjut.

---

## 📑 Daftar Isi
- [Kenapa Bikin Ini?](#-kenapa-bikin-ini)
- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Dipakai](#-teknologi-yang-dipakai)
- [Struktur Folder](#-struktur-folder)
- [Cara Install (Untuk Dosen / Tester)](#-cara-install-untuk-dosen--tester)
- [Catatan Tambahan](#-catatan-tambahan)

---

## 💡 Kenapa Bikin Ini?

Biasanya informasi soal tugas, materi kuliah dari kating (kakak tingkat), sampai pendaftaran UKM itu nyebar di banyak grup chat yang berbeda. Begitu chat-nya ketumpuk, file atau info penting pasti susah dicari.

Nah, aplikasi ini dibikin buat nyelesaiin masalah itu:
- Nyatuin semua info kampus dan diskusi di satu tempat.
- Bikin gampang cari materi kuliah (ada fitur Bank Materi).
- Gak perlu repot bikin akun baru yang bikin lupa password, cukup login pakai akun Google kampus.

---

## ✨ Fitur Utama

### 1. 🌐 Timeline & Diskusi (Global Posts)
- Mirip timeline sosmed biasa.
- Mahasiswa bisa bikin post, nanya soal tugas, atau sekadar diskusi.
<img width="800" alt="Tampilan Global Posts" src="https://via.placeholder.com/800x400.png?text=Screenshot+Timeline+Di+Sini" />

### 2. 📚 Bank Materi (Study Center)
- Tempat buat *upload* dan nyimpen PDF/Slide kuliah.
- Filenya dikelompokkan per mata kuliah, jadi nyarinya gampang banget.
<img width="800" alt="Tampilan Pusat Belajar" src="https://via.placeholder.com/800x400.png?text=Screenshot+Bank+Materi+Di+Sini" />

### 3. ⛺ Direktori UKM & Komunitas
- List semua UKM dan komunitas di kampus.
- Mahasiswa bisa lihat info UKM dan langsung klik tombol "Join".
<img width="800" alt="Tampilan Komunitas" src="https://via.placeholder.com/800x400.png?text=Screenshot+Halaman+Komunitas+Di+Sini" />

### 4. 📅 Event & Live Chat
- **Kalender Event:** Buat ngelihat jadwal seminar, acara kampus, dll.
- **Live Chat:** Fitur chat basic buat ngobrol langsung antar user tanpa pindah aplikasi.

---

## 🛠️ Teknologi yang Dipakai

| Bagian | Teknologi | Kenapa Pakai Ini? |
|---|---|---|
| **UI / Frontend** | HTML5, Vanilla JS | Simpel, cepat, dan gak butuh proses build/compile. |
| **Styling** | Vanilla CSS | Gak pakai Bootstrap atau Tailwind. CSS dibikin dari nol biar kodenya spesifik cuma untuk yang dipakai aja. |
| **Backend** | PHP Native | Sengaja gak pakai framework kayak Laravel biar fokus ke fundamental PHP dan gampang di-setup di localhost biasa. |
| **Database** | MySQL / MariaDB | Relasi tabelnya cukup standar buat *handle* user, post, dan grup. |
| **Login / Auth** | Google OAuth 2.0 | Biar user gampang login pakai Gmail tanpa mikirin password. |

---

## 📁 Struktur Folder

```text
PROJEK_UAS/
├── admin/          # Panel admin buat ngatur user dan hapus post bermasalah
├── assets/         # File CSS, JavaScript, dan Icon
├── auth/           # Logika buat login Google dan register
├── chat/           # Script PHP buat handle obrolan real-time
├── communities/    # Fitur daftar komunitas dan halaman grup
├── config/         # Tempat naruh file database.php dan google_config.php (Dilarang push ke GitHub!)
├── database/       # Kumpulan file .sql buat bikin tabel di phpMyAdmin
├── events/         # Halaman kalender dan daftar event
├── study/          # Logika upload/download file di Bank Materi
├── uploads/        # Folder otomatis buat nyimpen file yang di-upload user
└── index.php       # Halaman awal aplikasi (Dashboard)
```

---

## 🚀 Cara Install (Untuk Dosen / Tester)

Kalau mau nyoba jalanin aplikasi ini di laptop (localhost), ikuti step-step ini:

### 1. Setup Server & Database
1. Pastikan udah ada **XAMPP** atau **Laragon**.
2. Download atau *clone* repo ini, lalu taruh di folder `htdocs` (XAMPP) atau `www` (Laragon).
   > **Note:** Namain foldernya `PROJEK_UAS` ya, biar link CSS sama JS-nya gak error.
3. Buka phpMyAdmin, bikin database baru namanya `kampus_connect`.
4. Buka folder `database/` di proyek ini, terus *import* file `SETUP_TABLES.sql` ke dalam database tadi. (Bisa lanjut *import* file SQL lain kalau ada).

### 2. Setup Kredensial (Penting Buat Dosen)
Di GitHub ini, kami sengaja gak nge-upload file konfigurasi password database dan API Key Google demi keamanan.
1. Download file **ZIP Konfigurasi** yang udah dikumpulin bareng tugas di portal e-learning kampus.
2. Ekstrak, terus *copy* file `database.php` dan `google_config.php` ke dalam folder `config/` yang ada di proyek ini.

*(Buat yang cuma iseng nyoba dari GitHub: Kalian bisa copy file `config/database.example.php` terus ubah namanya jadi `database.php`. Tapi fitur Login Google-nya bakal error kalau kalian gak bikin API Key sendiri di Google Cloud Console).*

### 3. Jalanin Aplikasi
Kalau database sama file config udah siap:
1. Nyalain Apache sama MySQL.
2. Buka browser ke `http://localhost/PROJEK_UAS`.
3. Klik "Login with Google" buat nyoba masuk.

---
*Proyek ini dibikin buat Tugas Akhir Semester, tapi feel free kalau ada yang mau ikutan ngembangin kodenya!*
