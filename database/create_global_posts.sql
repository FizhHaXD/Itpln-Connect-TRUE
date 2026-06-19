-- SQL untuk membuat tabel postingan global
-- Postingan global sederhana: hanya teks dan nama pengirim

CREATE TABLE IF NOT EXISTS `global_posts` (
  `id_post` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_post`),
  KEY `id_user` (`id_user`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
