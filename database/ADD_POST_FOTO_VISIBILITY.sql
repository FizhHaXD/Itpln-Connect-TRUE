-- =============================================
-- ADD FOTO & VISIBILITY TO COMMUNITY_POSTS
-- =============================================

-- Add foto column
ALTER TABLE community_posts 
ADD COLUMN foto VARCHAR(255) NULL COMMENT 'Foto post (opsional)' AFTER content;

-- Add visibility column
ALTER TABLE community_posts 
ADD COLUMN visibility ENUM('internal', 'eksternal', 'private') 
DEFAULT 'internal' COMMENT 'internal=member only, eksternal=public, private=creator only' 
AFTER foto;

-- Add social media links
ALTER TABLE community_posts 
ADD COLUMN whatsapp_link VARCHAR(255) NULL AFTER visibility;

ALTER TABLE community_posts 
ADD COLUMN discord_link VARCHAR(255) NULL AFTER whatsapp_link;

ALTER TABLE community_posts 
ADD COLUMN instagram_link VARCHAR(255) NULL AFTER discord_link;

-- Verify
DESCRIBE community_posts;
