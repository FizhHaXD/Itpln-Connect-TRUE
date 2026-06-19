-- ============================================
-- SQL UPDATE FOTO COMMUNITIES
-- Berdasarkan file yang ada di folder uploads/communities/
-- ============================================
-- ✅ Tinggal copy-paste dan jalankan di phpMyAdmin!
-- ============================================

-- Update foto communities berdasarkan kategori/nama
UPDATE communities SET foto = 'MOBILE_DEV.png' WHERE nama_community LIKE '%Mobile%';
UPDATE communities SET foto = 'WEB_DEV.png' WHERE nama_community LIKE '%Programming%';
UPDATE communities SET foto = 'UIUX.png' WHERE nama_community LIKE '%UI%UX%' OR nama_community LIKE '%Creative Space%';
UPDATE communities SET foto = 'AI.png' WHERE nama_community LIKE '%Game%' OR nama_community LIKE '%AI%';
UPDATE communities SET foto = 'ENGLISH.png' WHERE nama_community LIKE '%English%';
UPDATE communities SET foto = 'SPORT.png' WHERE nama_community LIKE '%Esports%';
UPDATE communities SET foto = 'MATH.png' WHERE nama_community LIKE '%Nature%Logic%' OR nama_community LIKE '%Math%';
UPDATE communities SET foto = 'DATA_SCIENCE.png' WHERE nama_community LIKE '%Future Tech%' OR nama_community LIKE '%Data%';
UPDATE communities SET foto = 'WEB_DEV.png' WHERE nama_community LIKE '%Digital Art%';
UPDATE communities SET foto = 'PHOTOGRAPHY.png' WHERE nama_community LIKE '%Film%' OR nama_community LIKE '%Photography%';
UPDATE communities SET foto = 'FUTSAL.png' WHERE nama_community LIKE '%Futsal%';
UPDATE communities SET foto = 'AI.png' WHERE nama_community LIKE '%Entrepreneur%';
UPDATE communities SET foto = 'SPORT.png' WHERE nama_community LIKE '%Volleyball%';
UPDATE communities SET foto = 'SOCIAL CARE.png' WHERE nama_community LIKE '%Social Care%';

-- Atau update per ID (lebih akurat):
UPDATE communities SET foto = 'MOBILE_DEV.png' WHERE id_community = 26;  -- Mobile Innovation Lab
UPDATE communities SET foto = 'WEB_DEV.png' WHERE id_community = 27;     -- Programming Community
UPDATE communities SET foto = 'UIUX.png' WHERE id_community = 28;        -- UI/UX Creative Space
UPDATE communities SET foto = 'AI.png' WHERE id_community = 29;          -- Game Development Society
UPDATE communities SET foto = 'ENGLISH.png' WHERE id_community = 31;     -- English Debate Club
UPDATE communities SET foto = 'SPORT.png' WHERE id_community = 32;       -- Esports Academy
UPDATE communities SET foto = 'MATH.png' WHERE id_community = 33;        -- Nature & Logic Forum
UPDATE communities SET foto = 'DATA_SCIENCE.png' WHERE id_community = 34;-- Future Tech Enthusiast
UPDATE communities SET foto = 'WEB_DEV.png' WHERE id_community = 37;     -- Digital Art Community
UPDATE communities SET foto = 'PHOTOGRAPHY.png' WHERE id_community = 38; -- Film & Photography Club
UPDATE communities SET foto = 'FUTSAL.png' WHERE id_community = 39;      -- Futsal Mania Kampus
UPDATE communities SET foto = 'AI.png' WHERE id_community = 40;          -- Entrepreneur Circle
UPDATE communities SET foto = 'SPORT.png' WHERE id_community = 41;       -- Indonesia Volleyball Club
UPDATE communities SET foto = 'SOCIAL CARE.png' WHERE id_community = 42; -- Social Care Movement

-- Update sisanya dengan default
UPDATE communities SET foto = 'default_community.png' WHERE foto IS NULL OR foto = '';

-- ===== UPDATE EVENTS =====
UPDATE events SET photo = 'default_event.png' WHERE photo IS NULL OR photo = '';

-- ===== CEK HASIL =====
SELECT id_community, nama_community, foto FROM communities ORDER BY id_community;
SELECT id_event, nama_event, photo FROM events ORDER BY id_event;
