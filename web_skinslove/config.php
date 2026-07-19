<?php
// Mengaktifkan pelaporan error agar memudahkan debugging saat proses pembuatan website
error_reporting(E_ALL);
// Mengatur agar error langsung ditampilkan di halaman web untuk mempermudah deteksi bug
ini_set('display_errors', 1);


$db_host = 'localhost';
$db_user = 'angc2828_rafli_user';
$db_pass = 'K1ngZr4G3r';
$db_name = 'angc2828_skinslove_rafli';

try {
    // Membuat instansiasi PDO baru untuk koneksi ke database MySQL secara aman dan fleksibel
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    // Mengatur atribut PDO Error Mode agar melempar Exception jika terjadi error query database
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Mengatur fetch mode default dari database menjadi bentuk array asosiatif agar mudah dibaca
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
// Menangkap kegagalan koneksi ke database dan menampilkan pesan error
} catch (PDOException $e) {
    // Menghentikan eksekusi script dan menampilkan pesan error koneksi ke layar browser
    die("Koneksi ke database SkinsLove gagal: " . $e->getMessage());
}

// Memulai session PHP secara global jika session belum pernah dimulai di script sebelumnya
if (session_status() == PHP_SESSION_NONE) {
    // Memulai session baru untuk menyimpan data login pengguna
    session_start();
}
// Tag penutup PHP sengaja ditiadakan untuk mencegah output whitespace tidak sengaja pada file config
