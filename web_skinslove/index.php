<?php // Membuka tag PHP untuk kode inisialisasi awal halaman beranda
require_once 'config.php'; // Memanggil file konfigurasi database dan session global
$page_title = 'Beranda - SkinsLove.gg'; // Mendefinisikan judul halaman web untuk ditampilkan di browser
include 'includes/header.php'; // Menyertakan file header untuk kerangka navigasi atas
// Mengambil data 4 skin yang tersedia sebagai item unggulan (featured) di halaman utama
$stmt = $conn->prepare("SELECT * FROM items WHERE is_available = 1 LIMIT 4"); // Menyiapkan query mengambil 4 item aktif
$stmt->execute(); // Mengeksekusi instruksi query SQL ke database
$featured_skins = $stmt->fetchAll(PDO::FETCH_ASSOC); // Menyimpan seluruh hasil query ke dalam array $featured_skins
// Mengambil total jumlah item dari database secara dinamis untuk data counter statistik
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM items"); // Menyiapkan query untuk menghitung total baris item
$count_stmt->execute(); // Mengeksekusi query hitung total item
$total_items = $count_stmt->fetchColumn(); // Menyimpan angka total item ke dalam variabel $total_items
?> <!-- Menutup tag PHP untuk memulai rendering layout HTML beranda -->

<div class="container py-5">
    <!-- 1. HERO SECTION DENGAN LAYOUT PREMIUM DAN TEKS GRADIENT -->
    <div class="p-5 mb-5 rounded-4 hero-section border border-secondary" style="background-color: #121216;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-start">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill text-uppercase fw-bold mb-3" style="font-size: 0.75rem; tracking-wider: 1px;">
                    <i class="bi bi-patch-check-fill me-1"></i> #1 Trusted CS2 Skins Platform
                </span>
                <h1 class="display-4 fw-black text-white leading-tight mb-3">
                    Jual Beli Skins CS2 <br>
                    <span class="text-gradient">Instant, Aman & Murah</span>
                </h1>
                <p class="lead text-muted fs-6 mb-4" style="line-height: 1.7;">
                    Selamat datang di SkinsLove.gg! Dapatkan penawaran harga terbaik untuk pisau, sarung tangan, rifle, dan koleksi skin langka Counter-Strike 2 dengan wear float presisi tinggi. Transaksi otomatis dan terenkripsi aman 100%.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="pasar.php" class="btn btn-gradient px-4 py-3 rounded-3"><i class="bi bi-cart-fill me-1"></i> Jelajahi Pasar</a>
                    <a href="inventory.php" class="btn btn-outline-light px-4 py-3 rounded-3"><i class="bi bi-wallet2 me-1"></i> Inventori Saya</a>
                </div>
            </div>
            <div class="col-lg-5">
                <!-- 2. CAROUSEL PREVIEW ITEM UNGGULAN -->
                <div id="heroSkinsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroSkinsCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#heroSkinsCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#heroSkinsCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner rounded-4" style="border: 1px solid #2d2d39;">
                        <!-- Slide 1 -->
                        <div class="carousel-item active p-4 text-center bg-dark" style="min-height: 280px;">
                            <span class="badge bg-danger position-absolute top-3 start-3">Hot Deal</span>
                            <img src="assets/img/bfk_fade.png" class="img-fluid my-3" style="max-height: 140px; object-fit: contain;" alt="Knife Fade">
                            <h6 class="text-white fw-bold mb-1">★ Butterfly Knife | Fade</h6>
                            <p class="text-success small fw-bold mb-0">$1,850.00</p>
                        </div>
                        <!-- Slide 2 -->
                        <div class="carousel-item p-4 text-center bg-dark" style="min-height: 280px;">
                            <span class="badge bg-warning text-dark position-absolute top-3 start-3">Rare Item</span>
                            <img src="assets/img/dlore.png" class="img-fluid my-3" style="max-height: 140px; object-fit: contain;" alt="AWP Dragon Lore">
                            <h6 class="text-white fw-bold mb-1">AWP | Dragon Lore</h6>
                            <p class="text-success small fw-bold mb-0">$4,500.00</p>
                        </div>
                        <!-- Slide 3 -->
                        <div class="carousel-item p-4 text-center bg-dark" style="min-height: 280px;">
                            <span class="badge bg-info position-absolute top-3 start-3">Collector Item</span>
                            <img src="assets/img/howl.png" class="img-fluid my-3" style="max-height: 140px; object-fit: contain;" alt="M4A4 Howl">
                            <h6 class="text-white fw-bold mb-1">M4A4 | Howl</h6>
                            <p class="text-success small fw-bold mb-0">$3,100.00</p>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroSkinsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroSkinsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. STATS SECTION (Cards + Counters) -->
    <div class="row g-4 mb-5 text-start">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <h3 class="text-white fw-black font-mono mb-1"><span class="stat-counter" data-target="18450">0</span>+</h3>
                <p class="text-muted small mb-0">Total Pengunjung / Visits</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-pink-500 bg-opacity-10 text-pink">
                    <i class="bi bi-grid-3x3-gap-fill fs-4 text-gradient"></i>
                </div>
                <h3 class="text-white fw-black font-mono mb-1"><span class="stat-counter" data-target="<?php echo $total_items; // Mencetak total item dinamis sebagai target counter ?>">0</span>+</h3>
                <p class="text-muted small mb-0">Total Skins Terdaftar</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-shield-lock-fill fs-4"></i>
                </div>
                <h3 class="text-white fw-black font-mono mb-1"><span class="stat-counter" data-target="100">0</span>%</h3>
                <p class="text-muted small mb-0">Sistem Keamanan Terjamin</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
                <h3 class="text-white fw-black font-mono mb-1"><span class="stat-counter" data-target="4.9" data-is-float="true">0</span> / 5.0</h3>
                <p class="text-muted small mb-0">Rating & Review Pelanggan</p>
            </div>
        </div>
    </div>

    <!-- 4. FEATURED SKINS/ITEM SECTION -->
    <div class="mb-5 text-start">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h4 class="fw-bold text-gradient mb-0"><i class="bi bi-fire text-danger me-1"></i> Featured Skins</h4>
                <p class="text-muted small mb-0">Daftar skin CS2 premium dengan kualitas terbaik yang siap dibeli instan.</p>
            </div>
            <a href="pasar.php" class="btn btn-sm btn-outline-danger px-3 rounded-3">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <?php // Memulai pengecekan jika data skin unggulan tidak kosong
            if (!empty($featured_skins)): // Jika terdapat skin unggulan aktif
                foreach ($featured_skins as $skin): // Melakukan loop melalui setiap skin unggulan
            ?> <!-- Menutup kode PHP sementara untuk rendering HTML card -->
                <div class="col">
                    <div class="skin-card h-100 d-flex flex-column p-3">
                        <!-- Menampilkan gambar skin dengan badge kategori diletakkan di sudut kiri atas -->
                        <div class="text-center py-3 position-relative bg-dark rounded-3 mb-3" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?php echo htmlspecialchars($skin['image']); // Mencetak path gambar skin dengan aman ?>" alt="<?php echo htmlspecialchars($skin['name']); // Mencetak deskripsi nama skin ?>" class="img-fluid" style="max-height: 100px; object-fit: contain;">
                            <!-- Memperbaiki letak badge kategori ke sudut kiri atas agar tidak menutupi gambar produk -->
                            <span class="badge-wear"><?php echo htmlspecialchars($skin['category']); // Mencetak nama kategori skin secara absolut di pojok kiri ?></span>
                        </div>
                        
                        <!-- Detail Spesifikasi Skin -->
                        <div class="flex-grow-1 text-start">
                            <h6 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($skin['name']); // Menampilkan nama produk skin ?></h6>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($skin['collection']); // Menampilkan asal koleksi senjata skin ?></p>
                            <div class="small text-warning fw-bold mb-3">Float Wear: <?php echo number_format($skin['wear'], 4); // Menampilkan float wear dengan presisi 4 desimal ?></div>
                        </div>

                        <!-- Harga dan Tombol Beli Cepat -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                            <div class="text-start">
                                <span class="small text-muted d-block" style="font-size: 0.7rem;">Harga</span>
                                <div class="fw-bold text-success fs-5"><?php echo formatHarga($skin['price']); // Memanggil function formatHarga untuk mencetak nominal harga skin ?></div>
                            </div>
                            <form action="cart_action.php" method="POST" class="mb-0">
                                <input type="hidden" name="action" value="add"> <!-- Mengirimkan perintah tambah item -->
                                <input type="hidden" name="item_id" value="<?php echo $skin['id']; // Mengirimkan ID skin yang ingin dibeli ?>"> <!-- Menyimpan ID skin -->
                                <button type="submit" class="btn btn-sm btn-gradient py-2 px-2.5 rounded-3" title="Tambah ke Keranjang">
                                    <i class="bi bi-cart-plus fs-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php // Membuka blok PHP kembali untuk menutup iterasi loop
                endforeach; // Mengakhiri perulangan foreach
            endif; // Mengakhiri pengecekan kondisi array
            ?> <!-- Menutup blok PHP penutup card loop -->
        </div>
    </div>
</div>

<?php // Membuka PHP kembali untuk melampirkan footer halaman global
include 'includes/footer.php'; // Menyertakan file footer penutup website
?> <!-- Mengakhiri seluruh baris kode halaman PHP beranda -->
