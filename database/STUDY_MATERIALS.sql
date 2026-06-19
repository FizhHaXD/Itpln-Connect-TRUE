-- =============================================
-- TABEL MATERI KELOMPOK BELAJAR (STUDY MATERIALS)
-- =============================================

CREATE TABLE IF NOT EXISTS study_materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_group INT NOT NULL,
  id_user INT NOT NULL,
  nama_file VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  ukuran_file INT NOT NULL COMMENT 'Ukuran dalam bytes',
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_group) REFERENCES study_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
  INDEX idx_group_material (id_group),
  INDEX idx_user_material (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
