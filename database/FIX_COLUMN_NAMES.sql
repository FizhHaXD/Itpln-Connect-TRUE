-- =============================================
-- FIX NAMA KOLOM COMMUNITIES - WHATSAPP LINK
-- =============================================
-- Code menggunakan 'whatsapp_link' tapi tabel pakai 'wa_link'
-- Kita rename kolom biar konsisten
-- =============================================

-- Cek kolom yang ada
SHOW COLUMNS FROM communities LIKE '%link%';

-- Rename kolom wa_link menjadi whatsapp_link
ALTER TABLE communities 
CHANGE COLUMN wa_link whatsapp_link VARCHAR(255) NULL;

-- Verifikasi hasil
DESCRIBE communities;
