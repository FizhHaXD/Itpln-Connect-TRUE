-- =============================================
-- SQL LENGKAP - SEMUA TABEL YANG DIPERLUKAN
-- =============================================
-- Jalankan SQL ini di phpMyAdmin
-- Database: projek_uas
-- =============================================

-- ===== 1. TABEL CHATS (untuk fitur chat) =====
CREATE TABLE IF NOT EXISTS chats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id_user) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id_user) ON DELETE CASCADE,
  INDEX idx_sender_receiver (sender_id, receiver_id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 2. TABEL EVENT_PARTICIPANTS (untuk join event) =====
CREATE TABLE IF NOT EXISTS event_participants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  id_event INT NOT NULL,
  joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_event) REFERENCES events(id_event) ON DELETE CASCADE,
  UNIQUE KEY unique_participant (id_user, id_event),
  INDEX idx_event (id_event),
  INDEX idx_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 3. UPDATE USERS TABLE (tambah kolom untuk profile dan Google OAuth) =====
-- Cek dulu apakah kolom sudah ada, kalau belum baru ditambah

-- Tambah kolom no_hp (nomor HP)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS no_hp VARCHAR(20) NULL AFTER password;

-- Tambah kolom bio
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS bio TEXT NULL AFTER no_hp;

-- Tambah kolom foto (foto profil)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS foto VARCHAR(255) NULL AFTER bio;

-- Tambah kolom google_id (untuk Google OAuth)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) UNIQUE NULL AFTER password;

-- Tambah kolom created_at (jika belum ada)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER google_id;

-- ===== 4. UPDATE COMMUNITIES TABLE (tambah kolom social links) =====
-- Telegram
ALTER TABLE communities 
ADD COLUMN IF NOT EXISTS telegram_link VARCHAR(255) NULL AFTER instagram_link;

-- Website
ALTER TABLE communities 
ADD COLUMN IF NOT EXISTS website_link VARCHAR(255) NULL AFTER telegram_link;

-- Email
ALTER TABLE communities 
ADD COLUMN IF NOT EXISTS email_contact VARCHAR(100) NULL AFTER website_link;

-- ===== 5. UPDATE COMMUNITY_POSTS TABLE (tambah kolom social links) =====
-- WhatsApp
ALTER TABLE community_posts 
ADD COLUMN IF NOT EXISTS whatsapp_link VARCHAR(255) NULL AFTER isi;

-- Discord
ALTER TABLE community_posts 
ADD COLUMN IF NOT EXISTS discord_link VARCHAR(255) NULL AFTER whatsapp_link;

-- Instagram
ALTER TABLE community_posts 
ADD COLUMN IF NOT EXISTS instagram_link VARCHAR(255) NULL AFTER discord_link;

-- ===== CEK HASIL =====
-- Lihat struktur tabel yang sudah dibuat/diupdate

SHOW TABLES;

DESCRIBE users;
DESCRIBE communities;
DESCRIBE community_posts;
DESCRIBE chats;
DESCRIBE event_participants;

-- ===== SELESAI! =====
-- Semua tabel sudah siap digunakan
