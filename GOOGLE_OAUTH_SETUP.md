# 🔐 Setup Google OAuth untuk KampusKu

## 📋 Langkah-langkah Setup

### 1. Buat Google Cloud Project

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Login dengan akun Google kamu
3. Klik **"Create Project"** atau pilih project yang sudah ada
4. Beri nama project, misalnya: `KampusKu-OAuth`

### 2. Aktifkan Google+ API

1. Di sidebar, pilih **"APIs & Services" → "Library"**
2. Cari **"Google+ API"** atau **"Google Identity Services"**
3. Klik **"Enable"**

### 3. Buat OAuth Credentials

1. Di sidebar, pilih **"APIs & Services" → "Credentials"**
2. Klik **"+ CREATE CREDENTIALS"** → **"OAuth client ID"**
3. Jika diminta, configure consent screen dulu:
   - User Type: **External**
   - App name: `KampusKu`
   - User support email: email kamu
   - Developer contact: email kamu
   - Klik **Save and Continue**
4. Kembali ke Create OAuth client ID:
   - Application type: **Web application**
   - Name: `KampusKu Web Client`
   - Authorized redirect URIs:
     ```
     http://localhost/PROJEK_UAS/auth/google-callback.php
     ```
     ⚠️ **Penting:** Sesuaikan dengan URL lokal kamu!
5. Klik **"Create"**
6. **Copy** Client ID dan Client Secret yang muncul

### 4. Konfigurasi di Aplikasi

1. Buka file `config/google_config.php`
2. Ganti placeholder dengan credentials kamu:
   ```php
   define('GOOGLE_CLIENT_ID', 'PASTE_CLIENT_ID_KAMU_DISINI');
   define('GOOGLE_CLIENT_SECRET', 'PASTE_CLIENT_SECRET_KAMU_DISINI');
   ```
3. Sesuaikan REDIRECT_URI jika perlu:
   ```php
   define('GOOGLE_REDIRECT_URI', 'http://localhost/PROJEK_UAS/auth/google-callback.php');
   ```

### 5. Update Database

Jalankan SQL berikut di phpMyAdmin:

```sql
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) UNIQUE AFTER password;
```

### 6. Test!

1. Buka halaman login: `http://localhost/PROJEK_UAS/auth/login.php`
2. Klik tombol **"Login dengan Google"**
3. Pilih akun Google
4. Authorize aplikasi
5. Seharusnya redirect ke dashboard! 🎉

## 🔍 Troubleshooting

### Error: redirect_uri_mismatch
- Pastikan URL di Google Console **EXACTLY match** dengan URL callback kamu
- Include `http://` atau `https://`
- Cek ada/tidak trailing slash

### Error: Client ID not configured
- Pastikan sudah paste Client ID yang benar di `google_config.php`
- Refresh halaman

### Error: Unable to get user info
- Pastikan Google+ API sudah enabled
- Coba re-authorize aplikasi

## 📝 Catatan Penting

- **Development**: Pakai `http://localhost`
- **Production**: Ganti dengan domain produksi (harus HTTPS!)
- **Security**: Jangan commit file `google_config.php` dengan credentials asli ke Git!

## ✅ Checklist

- [ ] Buat Google Cloud Project
- [ ] Enable Google+ API
- [ ] Buat OAuth credentials
- [ ] Copy Client ID & Secret
- [ ] Update `config/google_config.php`
- [ ] Run SQL migration
- [ ] Test login dengan Google
- [ ] Success! 🎊
