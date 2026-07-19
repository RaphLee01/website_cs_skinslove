<?php
// Memanggil file config.php untuk memulai koneksi database pdo dan status session user
require_once 'config.php';

$page_title = "Tentang Kami - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- Konten Utama Halaman Tentang Kami (About Us) -->
    <div class="container my-5">
        <div class="row align-items-center mb-5">
            <!-- Deskripsi Filosofi SkinsLove.gg -->
            <div class="col-md-6 mb-4 mb-md-0">
                <span class="badge bg-danger mb-2 px-3 py-1.5 rounded-pill text-uppercase">The Premier CS2 Marketplace</span>
                <h1 class="fw-bold text-white mb-3">Tentang <span class="text-gradient">SkinsLove.gg</span></h1>
                <p class="text-muted" style="line-height: 1.8; text-align: justify;">
                    SkinsLove.gg didirikan pada tahun 2026 dengan visi menjadi marketplace item, skins, dan kosmetik Counter-Strike 2 nomor satu yang mengutamakan keamanan transaksi, kenyamanan pencarian, serta transparansi wear rating (float value) untuk para kolektor dan gamer sejati di seluruh Indonesia.
                </p>
                <p class="text-muted" style="line-height: 1.8; text-align: justify;">
                    Kami memahami betapa berharganya setiap detail skin senjata bagi Anda—mulai dari corak pola warna (fade percentage) hingga wear rating terkecil. Oleh karena itu, SkinsLove.gg menyediakan fitur pencarian super cerdas yang memungkinkan Anda melakukan filter mendalam berdasarkan rentang harga presisi, wear rating, koleksi senjata, dan kategori item demi membantu Anda mengamankan penawaran terbaik di pasar.
                </p>
            </div>
            <!-- Visual Illustrative Mockup / Gambar Cover -->
            <div class="col-md-6 text-center">
                <img src="assets/img/aboutmarket.webp" alt="SkinsLove CS2 Gaming Room" class="img-fluid rounded-4 shadow border border-secondary p-2 bg-dark">
            </div>
        </div>

        <!-- Keunggulan Layanan Grid Cards -->
        <div class="row g-4 mb-5">
            <h3 class="text-center fw-bold text-white mb-2">Mengapa Memilih SkinsLove.gg?</h3>
            <p class="text-center text-muted small col-12 mb-4">Kami merancang setiap detail layanan dengan dedikasi penuh untuk mendukung kebutuhan trade gamer CS2</p>
            
            <!-- Keunggulan 1: Pencarian Akurat -->
            <div class="col-md-4">
                <div class="skin-card p-4 text-center h-100">
                    <div class="text-gradient fs-1 mb-3"><i class="bi bi-search-heart"></i></div>
                    <h5 class="fw-bold text-white mb-2">Pencarian Akurat & Canggih</h5>
                    <p class="small text-muted mb-0">Cari item impian Anda secara instan menggunakan filter float value, range price, nama koleksi, serta klasifikasi kategori skin terlengkap.</p>
                </div>
            </div>

            <!-- Keunggulan 2: Autentikasi Aman -->
            <div class="col-md-4">
                <div class="skin-card p-4 text-center h-100">
                    <div class="text-gradient fs-1 mb-3"><i class="bi bi-shield-lock"></i></div>
                    <h5 class="fw-bold text-white mb-2">Autentikasi & Sesi Aman</h5>
                    <p class="small text-muted mb-0">Dilengkapi dengan enkripsi kata sandi dan penanganan login berbasis session untuk menjamin kerahasiaan inventori Anda.</p>
                </div>
            </div>

            <!-- Keunggulan 3: Real-Time Alerts -->
            <div class="col-md-4">
                <div class="skin-card p-4 text-center h-100">
                    <div class="text-gradient fs-1 mb-3"><i class="bi bi-bell"></i></div>
                    <h5 class="fw-bold text-white mb-2">Notifikasi Real-Time</h5>
                    <p class="small text-muted mb-0">Sistem notifikasi dinamis untuk memantau status checkout belanjaan serta pembaruan riwayat saldo Anda secara instan.</p>
                </div>
            </div>
        </div>

        <!-- Visi & Misi Panel -->
        <div class="row">
            <div class="col-12">
                <div class="checkout-box p-4 p-md-5 text-center shadow-lg">
                    <h4 class="fw-bold text-white mb-3">Visi Kami</h4>
                    <p class="lead text-muted mx-auto" style="max-width: 800px; font-size: 1.1rem; line-height: 1.8;">
                        "Menjadikan ekosistem perdagangan kosmetik game Counter-Strike 2 menjadi aman, adil, transparan, dan mudah dijangkau oleh semua gamer tanpa khawatir akan penipuan atau manipulasi data."
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
