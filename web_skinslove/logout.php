<?php
// Memanggil file config.php agar dapat melacak session yang saat ini sedang aktif
require_once 'config.php';

// Menghapus seluruh data variabel yang tersimpan di dalam session global PHP
$_SESSION = array();

// Memeriksa jika sistem session menggunakan kuki browser untuk menyimpan session id
if (ini_get("session.use_cookies")) {
    // Mengambil parameter kuki session aktif saat ini seperti path, domain, dan tipe secure
    $params = session_get_cookie_params();
    // Menghapus kuki session dari browser dengan mengatur waktu kedaluwarsa ke masa lalu
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Melakukan penghancuran session secara mutlak dan permanen di memori server
session_destroy();

// Mengalihkan pengguna secara instan kembali ke halaman utama pasar SkinsLove.gg
header("Location: index.php");
// Menghentikan proses pembacaan script program untuk keamanan penutupan sesi
exit;
