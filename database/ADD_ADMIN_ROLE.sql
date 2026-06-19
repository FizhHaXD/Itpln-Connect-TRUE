-- Add role system untuk admin
-- Role: 'user' (default) atau 'admin'

-- Add role column
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' NOT NULL;

-- Set user pertama sebagai admin (ganti email sesuai akun kamu)
UPDATE users SET role = 'admin' WHERE email = 'muhammadhafizhwijdan.titl@gmail.com' LIMIT 1;

-- Atau set by id_user (ganti 1 dengan ID user pertama)
-- UPDATE users SET role = 'admin' WHERE id_user = 1 LIMIT 1;
