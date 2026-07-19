<?php
// Memanggil file config.php untuk memulai koneksi database pdo dan status session user
require_once 'config.php';

$page_title = "Berita & Pembaruan Game - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- Konten Utama Halaman Berita & Artikel Terkini -->
    <div class="container my-5">
        <div class="text-center mb-5">
            <span class="badge bg-danger mb-2 px-3 py-1.5 rounded-pill text-uppercase">News & Update</span>
            <h1 class="fw-bold text-white mb-2">Berita & Informasi Terbaru</h1>
            <p class="text-muted">Ikuti terus pembaruan harga skin, rilis case senjata terbaru, dan tren pasar Counter-Strike 2</p>
        </div>

        <div class="row g-4">
            <!-- Berita Utama 1: Rilis Armory Update -->
            <div class="col-md-6 col-lg-4">
                <div class="skin-card h-100 d-flex flex-column">
                    <img src="assets/img/news1.avif" class="img-fluid" alt="CS2 Update Banner" style="height: 200px; object-fit: cover;">
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span><i class="bi bi-calendar3 me-1"></i> 10 Jan 2026</span>
                            <span class="text-gradient fw-bold">Update Terkini</span>
                        </div>
                        <h5 class="fw-bold text-white mb-2">CS2 "The Armory" Update Mengguncang Pasar Skin</h5>
                        <p class="small text-muted flex-grow-1" style="text-align: justify;">
                            Valve baru saja merilis pembaruan besar yang memperkenalkan Armory Pass baru dengan berbagai pilihan weapon charms, stiker edisi terbatas, dan tiga koleksi skin senjata terbaru. Pelajari dampaknya terhadap fluktuasi harga pisau legendaris di pasaran.
                        </p>
                        <a href="#" class="btn btn-sm btn-outline-danger w-100 mt-3 rounded-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>

            <!-- Berita Utama 2: Analisis Tren Float -->
            <div class="col-md-6 col-lg-4">
                <div class="skin-card h-100 d-flex flex-column">
                    <img src="assets/img/news2.avif" class="img-fluid" alt="Float Value Banner" style="height: 200px; object-fit: cover;">
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span><i class="bi bi-calendar3 me-1"></i> 05 Jan 2026</span>
                            <span class="text-gradient fw-bold">Tips & Panduan</span>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Mengapa Kolektor Mengincar Float "Low-Wear" di Tahun 2026</h5>
                        <p class="small text-muted flex-grow-1" style="text-align: justify;">
                            Apakah Anda tahu bahwa perbedaan 0.001 dalam wear rating bisa meningkatkan nilai tawar skin senjata hingga ratusan dollar? Simak rahasia para trader kawakan dalam mendeteksi skin berharga tinggi sebelum membelinya di marketplace.
                        </p>
                        <a href="#" class="btn btn-sm btn-outline-danger w-100 mt-3 rounded-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>

            <!-- Berita Utama 3: Tips Aman Bertransaksi -->
            <div class="col-md-6 col-lg-4">
                <div class="skin-card h-100 d-flex flex-column">
                    <img src="assets/img/news3.avif" class="img-fluid" alt="Trade Security Banner" style="height: 200px; object-fit: cover;">
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span><i class="bi bi-calendar3 me-1"></i> 28 Des 2025</span>
                            <span class="text-gradient fw-bold">Keamanan Akun</span>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Panduan Lengkap Menghindari Scam Trade CS2</h5>
                        <p class="small text-muted flex-grow-1" style="text-align: justify;">
                            Keamanan akun Steam Anda adalah prioritas utama kami. Baca langkah demi langkah mengamankan API key akun Anda dan cara bertransaksi dengan aman tanpa terjebak taktik phishing di dunia luar.
                        </p>
                        <a href="#" class="btn btn-sm btn-outline-danger w-100 mt-3 rounded-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
