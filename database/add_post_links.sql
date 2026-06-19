-- SQL untuk menambahkan kolom link komunikasi ke tabel community_posts
-- Jalankan query ini di phpMyAdmin atau MySQL client

ALTER TABLE `community_posts` 
ADD COLUMN `wa_link` VARCHAR(255) DEFAULT NULL AFTER `content`,
ADD COLUMN `discord_link` VARCHAR(255) DEFAULT NULL AFTER `wa_link`,
ADD COLUMN `instagram_link` VARCHAR(255) DEFAULT NULL AFTER `discord_link`;
