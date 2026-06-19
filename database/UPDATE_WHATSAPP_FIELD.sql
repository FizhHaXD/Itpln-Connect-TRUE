-- =============================================
-- UPDATE KOLOM WHATSAPP_LINK → ADMIN_WHATSAPP
-- =============================================
-- Ganti dari link ke nomor WhatsApp admin
-- Auto-generate wa.me link di frontend
-- =============================================

-- Option 1: Rename existing column
ALTER TABLE communities 
CHANGE COLUMN whatsapp_link admin_whatsapp VARCHAR(20) NULL 
COMMENT 'Nomor WhatsApp admin (hanya angka), contoh: 628123456789';

-- Option 2: Jika kolom belum ada, tambah baru
ALTER TABLE communities 
ADD COLUMN IF NOT EXISTS admin_whatsapp VARCHAR(20) NULL 
COMMENT 'Nomor WhatsApp admin (hanya angka), contoh: 628123456789';

-- Verifikasi
DESCRIBE communities;
