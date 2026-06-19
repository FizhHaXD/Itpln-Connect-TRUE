-- =============================================
-- STUDY GROUPS & MENTORING TABLES
-- =============================================
-- Jalankan SQL ini di phpMyAdmin
-- Database: kampus_connect
-- =============================================

-- ===== 1. TABEL UTAMA SESI BELAJAR =====
CREATE TABLE IF NOT EXISTS study_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_creator INT NOT NULL,
  judul VARCHAR(150) NOT NULL,
  mata_kuliah VARCHAR(100) NOT NULL,
  deskripsi TEXT NOT NULL,
  tipe ENUM('belajar_bersama', 'open_mentoring') NOT NULL DEFAULT 'belajar_bersama',
  jadwal DATETIME NOT NULL,
  lokasi VARCHAR(200) NOT NULL,
  maks_anggota INT NOT NULL DEFAULT 10,
  status ENUM('open', 'penuh', 'selesai') NOT NULL DEFAULT 'open',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_creator) REFERENCES users(id_user) ON DELETE CASCADE,
  INDEX idx_status (status),
  INDEX idx_tipe (tipe),
  INDEX idx_mata_kuliah (mata_kuliah),
  INDEX idx_jadwal (jadwal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 2. TABEL ANGGOTA SESI =====
CREATE TABLE IF NOT EXISTS study_group_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_group INT NOT NULL,
  id_user INT NOT NULL,
  joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_group) REFERENCES study_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
  UNIQUE KEY unique_member (id_group, id_user),
  INDEX idx_group (id_group),
  INDEX idx_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== CEK HASIL =====
SHOW TABLES LIKE 'study%';
DESCRIBE study_groups;
DESCRIBE study_group_members;

-- ===== SELESAI! =====
-- Tabel study_groups dan study_group_members sudah siap.
