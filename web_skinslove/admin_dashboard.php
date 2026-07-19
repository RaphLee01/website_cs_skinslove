<?php
// Menyertakan koneksi database PDO dan manajemen session login
require_once 'config.php';

// Menolak akses jika user belum login atau bukan merupakan administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Membawa user kembali ke halaman beranda agar terhindar dari akses bypass ilegal
    header('Location: index.php');
    exit;
}

// Menyiapkan default query pencarian untuk data tabel CRUD skins di dashboard admin
$search_query = trim($_GET['search'] ?? '');

// Membuat query pencarian dinamis
$query = "SELECT * FROM items";
$params = [];

// Memeriksa jika administrator sedang mencari skin berdasarkan nama tertentu di menu CRUD
if (!empty($search_query)) {
    // Menambahkan klausa penyeleksi pencarian nama skin ke database
    $query .= " WHERE name LIKE :search";
    $params['search'] = '%' . $search_query . '%';
}

// Menambahkan pengurutan item terbaru di bagian atas baris CRUD
$query .= " ORDER BY id DESC";

// Mempersiapkan eksekusi query dinamis tersebut ke server
$stmt = $conn->prepare($query);
$stmt->execute($params);
// Menyimpan semua list produk ke variabel $all_skins
$all_skins = $stmt->fetchAll();

// Mengambil data saran/pesan dari tabel suggestions untuk ditampilkan di admin dashboard
$suggestions_stmt = $conn->query("SELECT * FROM suggestions ORDER BY id DESC");
$all_suggestions = $suggestions_stmt->fetchAll();

// Mengambil riwayat transaksi pelanggan (Customer Transactions) secara lengkap
$transactions_stmt = $conn->query("
    SELECT t.*, u.username, u.email AS user_email, i.name AS skin_name, i.image AS skin_image 
    FROM transactions t 
    LEFT JOIN users u ON t.user_id = u.id 
    LEFT JOIN items i ON t.item_id = i.id 
    ORDER BY t.id DESC
");
$all_transactions = $transactions_stmt->fetchAll();
?>
<?php
// Menentukan judul halaman khusus untuk tab admin_dashboard.php
$page_title = "Admin Control Panel - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- CSS Tambahan Khusus Sidebar Admin Panel -->
    <style>
        .admin-nav-pills .nav-link {
            color: #cbd5e1 !important;
            background-color: #1a1a22;
            border: 1px solid #2d2d39;
            transition: all 0.25s ease;
        }
        .admin-nav-pills .nav-link:hover {
            color: white !important;
            background-color: #24242f;
            border-color: #f43f5e;
        }
        .admin-nav-pills .nav-link.active {
            background: linear-gradient(45deg, #f43f5e, #ec4899) !important;
            color: white !important;
            border-color: transparent !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3);
        }
    </style>

    <!-- Konten Utama Panel Admin Dashboard CRUD -->
    <div class="container my-5">
        
        <!-- Status Notification Alerts -->
        <?php
        $status = $_GET['status'] ?? '';
        if ($status === 'success_add'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #1c3d30; color: #a3e635;">
                <i class="bi bi-check-circle-fill me-2"></i> Skin baru berhasil ditambahkan ke pasar!
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($status === 'success_edit'): ?>
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #1e3a5f; color: #60a5fa;">
                <i class="bi bi-info-circle-fill me-2"></i> Informasi skin berhasil diperbarui!
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($status === 'success_delete'): ?>
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #451a03; color: #fb923c;">
                <i class="bi bi-trash-fill me-2"></i> Skin berhasil dihapus dari pasar!
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($status === 'error_delete_sold'): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #4c1d1d; color: #f87171;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal Menghapus!</strong> Skin ini tidak boleh dihapus karena sudah terjual/dibeli oleh user demi menjaga riwayat transaksi dan inventori mereka.
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- SISI KIRI: Sidebar Menu Admin (Seperti Referensi Gambar 6) -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="admin-card p-4 shadow">
                    <div class="text-center mb-4 pb-3 border-bottom border-secondary">
                        <i class="bi bi-shield-fill-check text-gradient fs-1"></i>
                        <h5 class="fw-bold mt-2 mb-1">Administrator</h5>
                        <p class="text-muted small mb-0">Control & Management Panel</p>
                    </div>
                    
                    <div class="nav flex-column admin-nav-pills gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <!-- Menu 1: Kelola Produk -->
                        <button class="nav-link active text-start py-2.5 px-3 rounded-3" id="v-pills-products-tab" data-bs-toggle="pill" data-bs-target="#v-pills-products" type="button" role="tab" aria-controls="v-pills-products" aria-selected="true">
                            <i class="bi bi-grid-3x3-gap-fill me-2"></i> Kelola Produk
                        </button>
                        
                        <!-- Menu 2: Riwayat Transaksi Pelanggan -->
                        <button class="nav-link text-start py-2.5 px-3 rounded-3" id="v-pills-transactions-tab" data-bs-toggle="pill" data-bs-target="#v-pills-transactions" type="button" role="tab" aria-controls="v-pills-transactions" aria-selected="false">
                            <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
                        </button>
                        
                        <!-- Menu 3: Pesan Masuk (Saran) -->
                        <button class="nav-link text-start py-2.5 px-3 rounded-3" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages" type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                            <i class="bi bi-envelope-paper-fill me-2"></i> Pesan Masuk 
                            <?php if (count($all_suggestions) > 0): ?>
                                <span class="badge bg-danger rounded-pill float-end small"><?php echo count($all_suggestions); ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SISI KANAN: Isi Konten Menu Aktif -->
            <div class="col-lg-9 col-md-8">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- TAB CONTAINER 1: KELOLA PRODUK -->
                    <div class="tab-pane fade show active" id="v-pills-products" role="tabpanel" aria-labelledby="v-pills-products-tab">
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <h3 class="fw-bold mb-1"><i class="bi bi-tags-fill me-2 text-gradient"></i>Manajemen Stok Produk</h3>
                                <p class="text-muted small mb-0">Tambah, ubah, cari, dan hapus database skin di pasar SkinsLove.gg</p>
                            </div>
                            <!-- Tombol Tambah Barang Baru -->
                            <div class="col-md-5 text-md-end">
                                <button class="btn btn-gradient px-4 py-2 rounded-3 shadow" data-bs-toggle="modal" data-bs-target="#modalTambahSkin">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Skin Baru
                                </button>
                            </div>
                        </div>

                        <!-- Form Pencarian CRUD -->
                        <div class="admin-card p-4 mb-4 shadow">
                            <form action="admin_dashboard.php" method="GET">
                                <div class="row g-3">
                                    <div class="col-md-10">
                                        <input type="text" name="search" class="form-control py-2.5" placeholder="Cari nama skin di database (contoh: Butterfly, Fade, Asiimov...)" value="<?php echo htmlspecialchars($search_query); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-gradient w-100 py-2.5 rounded-3"><i class="bi bi-search"></i> Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Tabel Manajemen Database Skin CS2 -->
                        <div class="admin-card p-4 shadow">
                            <div class="table-responsive">
                                <table class="table table-dark table-striped align-middle text-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Gambar</th>
                                            <th>Nama Skin</th>
                                            <th>Kategori</th>
                                            <th>Koleksi</th>
                                            <th>Wear/Float</th>
                                            <th>Harga ($)</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($all_skins)): ?>
                                            <tr>
                                                <td colspan="8" class="text-muted py-5">Database kosong atau skin tidak ditemukan.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($all_skins as $skin): ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?php echo htmlspecialchars($skin['image']); ?>" alt="" style="width: 50px; height: 50px; object-fit: contain;" class="bg-dark p-1 rounded">
                                                    </td>
                                                    <td class="text-start fw-bold text-white"><?php echo htmlspecialchars($skin['name']); ?></td>
                                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($skin['category']); ?></span></td>
                                                    <td class="text-muted small"><?php echo htmlspecialchars($skin['collection']); ?></td>
                                                    <td class="font-mono small text-warning"><?php echo number_format($skin['wear'], 4); ?></td>
                                                    <td class="text-success fw-bold">$<?php echo number_format($skin['price'], 2); ?></td>
                                                    <td>
                                                        <?php if ($skin['is_available'] == 1): ?>
                                                            <span class="badge bg-success">Tersedia</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Terjual</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <!-- Tombol Edit -->
                                                            <button class="btn btn-sm btn-outline-info rounded-3" data-bs-toggle="modal" data-bs-target="#modalEditSkin<?php echo $skin['id']; ?>" title="Edit Skin"><i class="bi bi-pencil-square"></i></button>
                                                            
                                                            <?php if ($skin['is_available'] == 1): ?>
                                                                <!-- Form untuk aksi delete barang secara instan -->
                                                                <form action="admin_crud.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skin ini dari pasar?');" class="mb-0">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo $skin['id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus Skin"><i class="bi bi-trash"></i></button>
                                                                </form>
                                                            <?php else: ?>
                                                                <!-- Tombol Hapus Nonaktif untuk item yang sudah terjual -->
                                                                <button type="button" class="btn btn-sm btn-secondary rounded-3" disabled title="Tidak bisa dihapus (Sudah terjual)"><i class="bi bi-trash-fill text-muted"></i></button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- MODAL UPDATE / EDIT SKIN (Dinamis per baris data ID skin) -->
                                                <div class="modal fade" id="modalEditSkin<?php echo $skin['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content bg-dark text-white border border-secondary">
                                                            <div class="modal-header border-secondary">
                                                                <h5 class="modal-title fw-bold">Ubah Data Skin CS2</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="admin_crud.php" method="POST" enctype="multipart/form-data">
                                                                <input type="hidden" name="action" value="edit">
                                                                <input type="hidden" name="id" value="<?php echo $skin['id']; ?>">
                                                                <div class="modal-body text-start">
                                                                    <div class="mb-3">
                                                                        <label class="form-label text-muted">Nama Skin</label>
                                                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($skin['name']); ?>" required>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label text-muted">Kategori</label>
                                                                            <select name="category" class="form-select" required>
                                                                                <option value="Knife" <?php echo $skin['category'] === 'Knife' ? 'selected' : ''; ?>>Knife</option>
                                                                                <option value="Glove" <?php echo $skin['category'] === 'Glove' ? 'selected' : ''; ?>>Glove</option>
                                                                                <option value="Rifle" <?php echo $skin['category'] === 'Rifle' ? 'selected' : ''; ?>>Rifle</option>
                                                                                <option value="Pistol" <?php echo $skin['category'] === 'Pistol' ? 'selected' : ''; ?>>Pistol</option>
                                                                                <option value="SMG" <?php echo $skin['category'] === 'SMG' ? 'selected' : ''; ?>>SMG</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label text-muted">Batas Wear/Float (0-1)</label>
                                                                            <input type="number" step="0.0001" min="0" max="1" name="wear" class="form-control" value="<?php echo $skin['wear']; ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label text-muted">Harga ($)</label>
                                                                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $skin['price']; ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label text-muted">Koleksi</label>
                                                                            <input type="text" name="collection" class="form-control" value="<?php echo htmlspecialchars($skin['collection']); ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label text-muted">Unggah Gambar Baru (Format: .png, .jpg) - Lewati jika tidak diubah</label>
                                                                        <input type="file" name="image_file" class="form-control mb-2">
                                                                        <input type="text" name="image_url" class="form-control" placeholder="Atau paste link URL gambar..." value="<?php echo htmlspecialchars($skin['image']); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-secondary">
                                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-gradient">Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB CONTAINER 2: RIWAYAT TRANSAKSI PELANGGAN -->
                    <div class="tab-pane fade" id="v-pills-transactions" role="tabpanel" aria-labelledby="v-pills-transactions-tab">
                        <div class="row mb-4 align-items-center">
                            <div class="col-12">
                                <h3 class="fw-bold mb-1"><i class="bi bi-receipt-cutoff text-gradient me-2"></i>Riwayat Transaksi Pelanggan</h3>
                                <p class="text-muted small mb-0 font-sans">Semua catatan transaksi pembelian skins yang berhasil diselesaikan oleh seluruh pengguna</p>
                            </div>
                        </div>

                        <div class="admin-card p-4 shadow">
                            <div class="table-responsive">
                                <table class="table table-dark table-striped align-middle text-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID Transaksi</th>
                                            <th>Nama Pembeli</th>
                                            <th>Skin CS2</th>
                                            <th>Tipe</th>
                                            <th>Harga Transaksi</th>
                                            <th>Tanggal & Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($all_transactions)): ?>
                                            <tr>
                                                <td colspan="7" class="text-muted py-5">Belum ada transaksi pembelian atau penjualan yang tercatat di database.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($all_transactions as $tx): ?>
                                                <tr>
                                                    <td class="font-mono text-warning">#TX-<?php echo str_pad($tx['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                    <td class="text-start">
                                                        <div class="fw-bold text-white"><?php echo htmlspecialchars($tx['username'] ?? 'User Dihapus'); ?></div>
                                                        <div class="text-muted small"><?php echo htmlspecialchars($tx['user_email'] ?? '-'); ?></div>
                                                    </td>
                                                    <td class="text-start">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="<?php echo htmlspecialchars($tx['skin_image'] ?? 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=100'); ?>" alt="" style="width: 32px; height: 32px; object-fit: contain;">
                                                            <span class="fw-semibold text-white small"><?php echo htmlspecialchars($tx['skin_name'] ?? 'Skin Dihapus'); ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if (($tx['transaction_type'] ?? 'buy') === 'buy'): ?>
                                                            <span class="badge bg-primary py-1.5 px-2.5 rounded-3 text-uppercase"><i class="bi bi-cart-fill me-1"></i> Beli</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark py-1.5 px-2.5 rounded-3 text-uppercase"><i class="bi bi-currency-dollar me-1"></i> Jual</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-success fw-bold">$<?php echo number_format($tx['price_paid'], 2); ?></td>
                                                    <td class="text-muted small"><?php echo date('d M Y, H:i:s', strtotime($tx['created_at'])); ?></td>
                                                    <td>
                                                        <span class="badge bg-success py-1.5 px-2.5 rounded-3"><i class="bi bi-patch-check-fill me-1"></i> Selesai</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB CONTAINER 3: PESAN MASUK (SARAN) -->
                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                        <div class="row mb-4 align-items-center">
                            <div class="col-12">
                                <h3 class="fw-bold mb-1"><i class="bi bi-chat-square-text-fill text-gradient me-2"></i>Pesan Masuk & Saran</h3>
                                <p class="text-muted small mb-0">Umpan balik, pertanyaan, dan saran yang dikirim pengguna melalui halaman Hubungi Kami</p>
                            </div>
                        </div>

                        <div class="admin-card p-4 shadow">
                            <div class="table-responsive">
                                <table class="table table-dark table-striped align-middle mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Nama Pengirim</th>
                                            <th>Email</th>
                                            <th>Subjek / Topik</th>
                                            <th>Pesan Lengkap</th>
                                            <th>Tanggal Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($all_suggestions)): ?>
                                            <tr>
                                                <td colspan="5" class="text-muted text-center py-5">Belum ada saran atau pesan masuk dari pengguna.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($all_suggestions as $sug): ?>
                                                <tr>
                                                    <td class="fw-bold text-white text-center"><?php echo htmlspecialchars($sug['name']); ?></td>
                                                    <td class="text-center"><a href="mailto:<?php echo htmlspecialchars($sug['email']); ?>" class="text-gradient text-decoration-none fw-semibold"><?php echo htmlspecialchars($sug['email']); ?></a></td>
                                                    <td class="text-warning fw-semibold text-center"><?php echo htmlspecialchars($sug['subject']); ?></td>
                                                    <td style="max-width: 320px; white-space: normal; text-align: justify;" class="small"><?php echo nl2br(htmlspecialchars($sug['message'])); ?></td>
                                                    <td class="small text-muted text-center"><?php echo date('d M Y, H:i', strtotime($sug['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- MODAL INSERT / TAMBAH SKIN BARU -->
    <div class="modal fade" id="modalTambahSkin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-gradient"><i class="bi bi-file-earmark-plus-fill me-1"></i> Tambah Skin Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin_crud.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Lengkap Skin</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: AWP | Asiimov" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Kategori Skin</label>
                                <select name="category" class="form-select" required>
                                    <option value="Knife">Knife</option>
                                    <option value="Glove">Glove</option>
                                    <option value="Rifle" selected>Rifle</option>
                                    <option value="Pistol">Pistol</option>
                                    <option value="SMG">SMG</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Wear/Float Value (0 s/d 1)</label>
                                <input type="number" step="0.0001" min="0" max="1" name="wear" class="form-control" placeholder="0.0125" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Harga Jual ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="250.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nama Koleksi Game</label>
                                <input type="text" name="collection" class="form-control" placeholder="Contoh: Phoenix Collection" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Metode Unggah Gambar Skin</label>
                            <input type="file" name="image_file" class="form-control mb-2">
                            <input type="text" name="image_url" class="form-control" placeholder="Atau tempel link URL gambar skin...">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-gradient">Simpan Skin Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
