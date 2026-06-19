<?php
// ====================================
// DATABASE CONFIGURATION (TEMPLATE)
// ====================================
// Ubah nama file ini menjadi database.php dan isi dengan data yang benar

// --- PRODUCTION (Database Dosen/Hosting) ---
// $host = "localhost";
// $user = "username_database_production";
// $pass = "password_rahasia";
// $db   = "nama_database_production";

// --- LOCAL (Laragon / XAMPP) ---
// Konfigurasi bawaan untuk komputer lokal (Dosen bisa langsung pakai ini)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kampus_connect";

// Koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ====================================
// BASE URL
// ====================================
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$project_root = str_replace('\\', '/', dirname(__DIR__));
$base_path = str_replace($doc_root, '', $project_root);

if (empty($base_path) || $base_path === '/') {
    define('BASE_URL', '/');
} else {
    define('BASE_URL', $base_path . '/');
}
?>
