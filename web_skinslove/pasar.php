<?php // Membuka tag PHP untuk inisialisasi filter pencarian pasar
require_once 'config.php'; // Memanggil file konfigurasi database global dan session

// Menyiapkan variabel filter pencarian default kosong agar tidak memunculkan error undefined
// Menggunakan $_REQUEST agar filter pencarian tetap berfungsi baik dikirim lewat metode GET maupun POST
$search = trim($_REQUEST['search'] ?? ''); // Mengambil teks pencarian kata kunci dari parameter URL (GET) maupun form (POST)
$category = $_GET['category'] ?? ''; // Mengambil kategori terpilih dari parameter filter URL
$collection = $_GET['collection'] ?? ''; // Mengambil koleksi senjata terpilih dari parameter filter URL

// Mengambil filter batas harga minimal, standarnya bernilai 0 jika tidak dikonfigurasi atau kosong
$min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? floatval($_GET['min_price']) : 0; // Memfilter nilai minimum harga
$max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? floatval($_GET['max_price']) : 999999; // Memfilter nilai maksimum harga

// Mengambil filter wear/float minimal yang diinginkan oleh kolektor skin CS2
$min_wear = (isset($_GET['min_wear']) && $_GET['min_wear'] !== '') ? floatval($_GET['min_wear']) : 0.0; // Memfilter wear rate minimum
$max_wear = (isset($_GET['max_wear']) && $_GET['max_wear'] !== '') ? floatval($_GET['max_wear']) : 1.0; // Memfilter wear rate maksimum

// Mengambil filter pengurutan data skin, defaultnya berdasarkan tanggal item ditambahkan terbaru
$sort_by = $_GET['sort_by'] ?? 'newest'; // Mengambil opsi urutan tampilan dari URL

// Menyusun kerangka utama query SQL untuk memilah item skin yang berstatus masih tersedia di pasar
$query = "SELECT * FROM items WHERE is_available = 1"; // Klausa dasar mengambil produk yang tersedia
$params = []; // Inisialisasi parameter array kosong untuk pengikatan PDO secara aman

// Memeriksa jika pengguna menginputkan teks pencarian pada kolom search
if (!empty($search)) { // Jika parameter search terisi
    $query .= " AND name LIKE :search"; // Menambahkan filter name dengan klausa LIKE
    $params['search'] = '%' . $search . '%'; // Mengikat nilai search dengan tambahan wildcard %
} // Selesai pengecekan string search

// Memeriksa jika pengguna memfilter skin berdasarkan kategori tertentu seperti Knife, Glove, dll
if (!empty($category)) { // Jika parameter category terisi
    $query .= " AND category = :category"; // Menambahkan filter kategori pada SQL
    $params['category'] = $category; // Mengikat nilai parameter category
} // Selesai pengecekan category

// Memeriksa jika pengguna memilah skin berdasarkan koleksi game CS2 tertentu
if (!empty($collection)) { // Jika parameter collection terisi
    $query .= " AND collection = :collection"; // Menambahkan filter koleksi pada SQL
    $params['collection'] = $collection; // Mengikat nilai parameter collection
} // Selesai pengecekan collection

// Menambahkan filter rentang harga minimum dan rentang harga maksimum yang fleksibel ke query
$query .= " AND price >= :min_price AND price <= :max_price"; // Tambahkan klausa rentang harga
$params['min_price'] = $min_price; // Mengikat parameter nilai min_price
$params['max_price'] = $max_price; // Mengikat parameter nilai max_price

// Menambahkan filter rentang float wear rate skin CS2 (Factory New s/d Battle-Scarred)
$query .= " AND wear >= :min_wear AND wear <= :max_wear"; // Tambahkan klausa rentang wear rate
$params['min_wear'] = $min_wear; // Mengikat parameter nilai min_wear
$params['max_wear'] = $max_wear; // Mengikat parameter nilai max_wear

// Menentukan klausa pengurutan tampilan data skin berdasarkan pilihan user di antarmuka
if ($sort_by === 'price_asc') { // Jika memilih harga termurah
    $query .= " ORDER BY price ASC"; // Urutkan berdasarkan harga terkecil
} elseif ($sort_by === 'price_desc') { // Jika memilih harga termahal
    $query .= " ORDER BY price DESC"; // Urutkan berdasarkan harga terbesar
} elseif ($sort_by === 'float_asc') { // Jika memilih float terbaik
    $query .= " ORDER BY wear ASC"; // Urutkan berdasarkan nilai wear terkecil
} else { // Jika memilih default
    $query .= " ORDER BY id DESC"; // Urutkan berdasarkan ID secara menurun (terbaru)
} // Selesai pengkondisian sort_by

// Mempersiapkan eksekusi query gabungan filter pencarian yang kompleks ke server database
$stmt = $conn->prepare($query); // Menyiapkan statement PDO
$stmt->execute($params); // Mengeksekusi query database dengan parameter yang aman
$items = $stmt->fetchAll(); // Menyimpan hasil query ke dalam variabel array $items

// Mendapatkan daftar koleksi unik dari database untuk ditampilkan pada opsi filter dropdown
$collection_stmt = $conn->query("SELECT DISTINCT collection FROM items ORDER BY collection ASC"); // Mengambil nama koleksi unik
$collections = $collection_stmt->fetchAll(PDO::FETCH_COLUMN); // Menyimpan daftar koleksi ke array satu dimensi
?> <!-- Mengakhiri blok inisialisasi PHP -->

<?php // Membuka tag PHP untuk inisialisasi judul halaman dan menyertakan header navigasi
$page_title = "Pasar Skins - SkinsLove.gg"; // Menetapkan judul halaman pasar
require_once 'includes/header.php'; // Menyertakan file header
?> <!-- Mengakhiri tag PHP penyertaan header -->

<!-- Konten Utama Marketplace dengan Grid Dua Kolom (Filter dan Produk) -->
<div class="container my-5">
    <div class="row">
        <!-- Kolom Samping untuk Filter Pencarian Skin yang Lengkap -->
        <div class="col-lg-3 mb-4">
            <div class="sidebar-filter p-4 shadow">
                <h5 class="mb-4 fw-bold text-gradient"><i class="bi bi-sliders me-2"></i> Filter Pencarian</h5>
                <!-- Form pencarian diarahkan kembali ke pasar.php agar filter berfungsi sempurna -->
                <form action="pasar.php" method="GET">
                    <!-- Grup pencarian kata kunci nama skin -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Cari Nama Skin</label>
                        <input type="text" name="search" class="form-control" placeholder="Contoh: Fade, Howl..." value="<?php echo htmlspecialchars($search); // Menampilkan nilai pencarian sebelumnya ?>">
                    </div>
                    
                    <!-- Grup pemilihan kategori skin -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Kategori Senjata</label>
                        <select name="category" class="form-select">
                            <option value="">-- Semua Kategori --</option>
                            <option value="Knife" <?php echo $category === 'Knife' ? 'selected' : ''; // Pilih Knife jika cocok ?>>Knife (Pisau)</option>
                            <option value="Glove" <?php echo $category === 'Glove' ? 'selected' : ''; // Pilih Glove jika cocok ?>>Glove (Sarung Tangan)</option>
                            <option value="Rifle" <?php echo $category === 'Rifle' ? 'selected' : ''; // Pilih Rifle jika cocok ?>>Rifle (Senapan)</option>
                            <option value="Pistol" <?php echo $category === 'Pistol' ? 'selected' : ''; // Pilih Pistol jika cocok ?>>Pistol</option>
                            <option value="SMG" <?php echo $category === 'SMG' ? 'selected' : ''; // Pilih SMG jika cocok ?>>SMG</option>
                        </select>
                    </div>

                    <!-- Grup pemilihan koleksi game CS2 -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Koleksi Senjata</label>
                        <select name="collection" class="form-select">
                            <option value="">-- Semua Koleksi --</option>
                            <?php foreach ($collections as $col): // Memulai perulangan daftar koleksi unik ?>
                                <option value="<?php echo htmlspecialchars($col); ?>" <?php echo $collection === $col ? 'selected' : ''; ?>><?php echo htmlspecialchars($col); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Rentang Harga Min dan Max -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Rentang Harga ($)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo $min_price > 0 ? $min_price : ''; // Tampilkan nilai minimum jika di atas nol ?>">
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo $max_price < 999999 ? $max_price : ''; // Tampilkan nilai maksimum jika di bawah batas atas ?>">
                        </div>
                    </div>

                    <!-- Rentang Wear Float Rate -->
                    <div class="mb-4">
                        <label class="form-label text-muted small">Rentang Float/Wear (0 s/d 1)</label>
                        <div class="d-flex gap-2">
                            <input type="number" step="0.0001" name="min_wear" class="form-control" placeholder="Min Float (0.0)" value="<?php echo $min_wear > 0 ? $min_wear : ''; // Tampilkan wear rate minimum ?>">
                            <input type="number" step="0.0001" name="max_wear" class="form-control" placeholder="Max Float (1.0)" value="<?php echo $max_wear < 1 ? $max_wear : ''; // Tampilkan wear rate maksimum ?>">
                        </div>
                    </div>

                    <!-- Opsi Pengurutan / Sorting -->
                    <div class="mb-4">
                        <label class="form-label text-muted small">Urutkan Berdasarkan</label>
                        <select name="sort_by" class="form-select">
                            <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; // Set selected jika default terbaru ?>>Terbaru</option>
                            <option value="price_asc" <?php echo $sort_by === 'price_asc' ? 'selected' : ''; // Set selected jika harga terendah ?>>Harga Terendah</option>
                            <option value="price_desc" <?php echo $sort_by === 'price_desc' ? 'selected' : ''; // Set selected jika harga tertinggi ?>>Harga Tertinggi</option>
                            <option value="float_asc" <?php echo $sort_by === 'float_asc' ? 'selected' : ''; // Set selected jika float terbaik ?>>Float Terbaik</option>
                        </select>
                    </div>

                    <!-- Tombol Terapkan Filter -->
                    <button type="submit" class="btn btn-gradient w-100 mb-2 py-2 rounded-3"><i class="bi bi-filter"></i> Terapkan Filter</button>
                    <!-- Tombol Reset pencarian diarahkan kembali ke pasar.php agar bersih -->
                    <a href="pasar.php" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-white">Reset Semua</a>
                </form>
            </div>
        </div>

        <!-- Kolom Daftar Produk Skin CS2 Terpilih -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 text-start">
                <div>
                    <h4 class="fw-bold text-gradient mb-0">Pasar Skins CS2</h4>
                    <p class="text-muted small mb-0">Ditemukan <?php echo count($items); // Mencetak jumlah total skin yang terfilter ?> skin eksklusif siap beli</p>
                </div>
            </div>

            <!-- Tampilan jika tidak ada skin yang cocok dengan filter -->
            <?php if (empty($items)): // Membuka pengecekan jika hasil filter kosong ?>
                <div class="text-center py-5 border border-secondary border-dashed rounded-4">
                    <i class="bi bi-shield-slash fs-1 text-muted mb-3"></i>
                    <h5>Skins tidak ditemukan!</h5>
                    <p class="text-muted">Coba ubah kata kunci atau rentang filter pencarian Anda.</p>
                </div>
            <?php else: // Kondisi alternatif jika item ditemukan ?>
                <!-- Grid Responsive Daftar Kartu Produk Skin Game -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php foreach ($items as $skin): // Memulai perulangan data skin ?>
                        <div class="col">
                            <div class="skin-card h-100 d-flex flex-column p-3 text-start">
                                <!-- Menampilkan Gambar Skin dengan layout yang rapi dan badge diletakkan di pojok -->
                                <div class="text-center py-3 position-relative bg-dark rounded-3 mb-3" style="min-height: 160px; display: flex; align-items: center; justify-content: center;">
                                    <img src="<?php echo htmlspecialchars($skin['image']); // Menampilkan URL gambar skin ?>" alt="<?php echo htmlspecialchars($skin['name']); // Menampilkan nama skin untuk atribut alt ?>" class="img-fluid" style="max-height: 120px; object-fit: contain;">
                                    <span class="badge-wear"><?php echo htmlspecialchars($skin['category']); // Menampilkan kategori skin di pojok kiri atas card secara absolut ?></span>
                                </div>
                                
                                <!-- Detail Spesifikasi Skin -->
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($skin['name']); // Menampilkan nama skin ?></h6>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($skin['collection']); // Menampilkan nama koleksi skin ?></p>
                                    
                                    <!-- Wear Float rate Visual progress bar -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between text-muted small mb-1">
                                            <span>Wear Rate</span>
                                            <span class="fw-bold text-light"><?php echo number_format($skin['wear'], 4); // Menampilkan nilai wear skin ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px; background-color: #1a1a22;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo (1 - $skin['wear']) * 100; // Hitung persentase progress wear ?>%; background: linear-gradient(45deg, #f43f5e, #ec4899);" aria-valuenow="<?php echo (1 - $skin['wear']) * 100; // Mengisi value-now progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Harga dan Tombol Beli / Tambah ke Keranjang -->
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                                    <div>
                                        <div class="small text-muted">Harga Instant</div>
                                        <h5 class="fw-bold text-success mb-0"><?php echo formatHarga($skin['price']); // Memanggil function formatHarga untuk mencetak harga skin ?></h5>
                                    </div>
                                    <!-- Form untuk submit order ke keranjang belanja -->
                                    <form action="cart_action.php" method="POST" class="mb-0">
                                        <input type="hidden" name="action" value="add"> <!-- Menyimpan instruksi penambahan -->
                                        <input type="hidden" name="item_id" value="<?php echo $skin['id']; // Menyimpan ID skin ?>">
                                        <button type="submit" class="btn btn-gradient btn-sm px-3 py-2 rounded-3"><i class="bi bi-cart-plus me-1"></i> Beli</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; // Menutup loop data skin ?>
                </div>
            <?php endif; // Menutup tag pengecekan kondisi data items kosong ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; // Melampirkan file footer secara terpisah dan terpusat ?>