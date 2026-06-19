-- =============================================
-- UPGRADE POST: SUPPORT 3 IMAGES
-- =============================================

-- Add foto2 and foto3 columns
ALTER TABLE community_posts 
ADD COLUMN foto2 VARCHAR(255) NULL AFTER foto;

ALTER TABLE community_posts 
ADD COLUMN foto3 VARCHAR(255) NULL AFTER foto2;

-- Verify
DESCRIBE community_posts;
