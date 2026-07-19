<?php // Memulai tag PHP untuk inisialisasi header navigasi global
require_once 'config.php'; // Memanggil file konfigurasi database dan session global
require_once 'functions.php'; // Memanggil file kumpulan custom function/procedure bantuan global
$current_page = basename($_SERVER['PHP_SELF']); // Mengambil nama file PHP saat ini untuk deteksi halaman aktif aktif
?> <!-- Mengakhiri tag PHP inisialisasi header -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul halaman dinamis, menggunakan fallback default jika variabel tidak didefinisikan di halaman pemanggil -->
    <title><?php echo isset($page_title) ? $page_title : 'SkinsLove.gg - CS2 Skins Buy & Trade Marketplace'; // Menampilkan judul halaman dinamis atau teks fallback default ?></title>
    <!-- Mengimpor framework CSS Bootstrap 5 melalui CDN eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mengimpor ikon-ikon dari library Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Mengimpor file CSS eksternal terpisah untuk kerapian kode -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasi Utama Terpusat -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 text-white" href="index.php">
                Skins<span class="text-gradient"><?php echo ($current_page === 'admin_dashboard.php') ? 'Love Admin' : 'Love.gg'; // Menampilkan logo dinamis jika berada di dashboard admin ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; // Deteksi halaman aktif beranda ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'pasar.php') ? 'active' : ''; // Deteksi halaman aktif pasar ?>" href="pasar.php">
                            <i class="bi bi-shop me-1"></i> Market
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'inventory.php') ? 'active' : ''; // Deteksi halaman aktif inventori ?>" href="inventory.php">
                            <i class="bi bi-archive me-1"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'about.php') ? 'active' : ''; ?>" href="about.php">
                            <i class="bi bi-info-circle me-1"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'news.php') ? 'active' : ''; ?>" href="news.php">
                            <i class="bi bi-newspaper me-1"></i> News
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'faq.php') ? 'active' : ''; ?>" href="faq.php">
                            <i class="bi bi-chat-right-text me-1"></i> FAQ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'contact.php') ? 'active' : ''; ?>" href="contact.php">
                            <i class="bi bi-envelope me-1"></i> Contact
                        </a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): // Menampilkan menu panel admin jika pengguna terdeteksi admin ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning <?php echo ($current_page === 'admin_dashboard.php') ? 'active' : ''; // Deteksi halaman aktif dashboard admin ?>" href="admin_dashboard.php">
                                <i class="bi bi-speedometer2 me-1"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; // Penutup kondisi role admin ?>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="cart.php" class="btn btn-outline-light position-relative px-3 py-1.5 rounded-3">
                        <i class="bi bi-cart3"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php // Memulai blok hitung keranjang belanja menggunakan custom function
                            // Memanggil function hitungJumlahKeranjang dengan parameter koneksi PDO dan id user dari session
                            $cart_count = hitungJumlahKeranjang($conn, $_SESSION['user_id'] ?? null);
                            echo $cart_count; // Mencetak nilai jumlah item keranjang belanja ke layar
                            ?> <!-- Menutup blok PHP hitung keranjang -->
                        </span>
                    </a>
                    <?php if (isset($_SESSION['user_id'])): // Membuka blok pengecekan jika user sudah log masuk ?>
                        <div class="text-end d-none d-md-block">
                            <div class="small text-muted">Selamat datang,</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); // Mencetak nama pengguna dengan proteksi XSS ?></div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <div class="bg-dark px-3 py-1.5 rounded-3 border border-secondary text-success fw-bold">
                                <?php echo formatHarga($_SESSION['balance'] ?? 0); // Memanggil function formatHarga untuk mencetak saldo dengan format mata uang ?>
                            </div>
                            <a href="deposit.php" class="btn btn-sm btn-gradient py-1.5 px-2 rounded-3" title="Tambah Saldo"><i class="bi bi-plus-lg"></i></a>
                        </div>
                        <a href="logout.php" class="btn btn-danger btn-sm px-3 rounded-3"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                    <?php else: // Blok alternatif jika pengguna belum login ?>
                        <a href="login.php" class="btn btn-outline-light px-4 rounded-3">Masuk</a>
                        <a href="register.php" class="btn btn-gradient px-4 rounded-3">Daftar</a>
                    <?php endif; // Menutup blok kondisional session user ID ?>
                </div>
            </div>
        </div>
    </nav>