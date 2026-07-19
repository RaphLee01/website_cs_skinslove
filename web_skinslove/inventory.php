<?php // Membuka tag PHP untuk inisialisasi status log masuk dan penarikan inventori user
require_once 'config.php'; // Memanggil file konfigurasi global database dan session

// Memeriksa status login pembeli, wajib diarahkan ke login jika belum otentikasi session
if (!isset($_SESSION['user_id'])) { // Jika id user tidak ditemukan di dalam session aktif
    header('Location: login.php'); // Melakukan redirect paksa ke halaman login
    exit; // Menyudahi jalannya script setelah redirect
} // Penutup pengecekan session

// Menyimpan id login pembeli ke variabel lokal
$user_id = $_SESSION['user_id']; // Mengambil nilai user_id dari data session login

// Mempersiapkan query SQL untuk mengambil seluruh skin CS2 yang sukses dibeli oleh user tersebut dan saat ini masih dimiliki (menggunakan subquery MAX ID agar tidak duplikat)
$stmt = $conn->prepare("SELECT i.*, t.created_at AS buy_date FROM items i JOIN transactions t ON t.item_id = i.id WHERE i.is_available = 0 AND t.user_id = :user_id AND t.transaction_type = 'buy' AND t.status = 'success' AND t.id = (SELECT MAX(t2.id) FROM transactions t2 WHERE t2.item_id = i.id) ORDER BY t.id DESC"); // SQL query select join item dan transaksi milik pembeli
$stmt->execute(['user_id' => $user_id]); // Mengeksekusi query database dengan parameter ID user terkait
$my_skins = $stmt->fetchAll(); // Menyimpan seluruh baris data inventori milik pembeli ke array $my_skins

// Mempersiapkan query untuk mengambil history log transaksi pembelian barang secara detail
$hist_stmt = $conn->prepare("SELECT t.*, i.name AS skin_name, i.image AS skin_image FROM transactions t JOIN items i ON t.item_id = i.id WHERE t.user_id = :user_id ORDER BY t.id DESC"); // SQL query select join item dan transaksi untuk riwayat
$hist_stmt->execute(['user_id' => $user_id]); // Mengeksekusi query riwayat transaksi
$history = $hist_stmt->fetchAll(); // Menyimpan seluruh log riwayat transaksi keuangan ke array $history

// Mempersiapkan penarikan notifikasi real-time terbaru milik user bersangkutan
$notif_stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY id DESC LIMIT 5"); // SQL query mengambil notifikasi terbaru limit 5 baris
$notif_stmt->execute(['user_id' => $user_id]); // Mengeksekusi query notifikasi database
$notifications = $notif_stmt->fetchAll(); // Menyimpan list notifikasi terbaru milik pembeli ke array $notifications
?> <!-- Menutup tag PHP inisialisasi halaman -->

<?php // Membuka tag PHP untuk inisialisasi judul tab halaman dan include file header
$page_title = "Inventori & Riwayat - SkinsLove.gg"; // Menetapkan judul halaman inventori
require_once 'includes/header.php'; // Menyertakan file navigasi atas
?> <!-- Mengakhiri tag PHP penyertaan header -->

<!-- Konten Tab Inventori & Riwayat -->
<div class="container my-5">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success_sell'): // Membuka pengecekan jika proses penjualan berhasil 
    ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #1c3d30; color: #a3e635;">
            <i class="bi bi-check-circle-fill me-2"></i> <strong>Penjualan Sukses!</strong> Skin Anda telah berhasil dikembalikan ke pasar dan saldo Anda telah ditambahkan!
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error_sell'): // Membuka pengecekan alternatif jika penjualan gagal 
    ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #4c1d1d; color: #f87171;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal Menjual!</strong> Terjadi kesalahan atau kepemilikan skin tidak valid.
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; // Penutup pengecekan alert status penjualan 
    ?>

    <div class="row">
        <!-- Kolom Notifikasi Terbaru (Real-time update) -->
        <div class="col-lg-4 mb-4">
            <div class="tab-custom p-4 shadow mb-4">
                <h5 class="fw-bold mb-3 text-gradient"><i class="bi bi-bell-fill me-2"></i> Notifikasi Transaksi</h5>
                <?php if (empty($notifications)): // Membuka pengecekan jika array notifikasi kosong 
                ?>
                    <p class="text-muted small mb-0">Belum ada pembaruan status transaksi terbaru saat ini.</p>
                <?php else: // Blok alternatif jika notifikasi tersedia 
                ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($notifications as $notif): // Melakukan loop melalui daftar notifikasi 
                        ?>
                            <div class="notif-item p-3 text-start">
                                <div class="fw-bold text-white small mb-1"><?php echo htmlspecialchars($notif['title']); // Menampilkan judul notifikasi 
                                                                            ?></div>
                                <div class="text-muted small mb-1"><?php echo htmlspecialchars($notif['message']); // Menampilkan isi pesan notifikasi 
                                                                    ?></div>
                                <div class="text-muted" style="font-size: 0.65rem;"><?php echo $notif['created_at']; // Menampilkan tanggal pembuatan notifikasi 
                                                                                    ?></div>
                            </div>
                        <?php endforeach; // Menutup perulangan notifikasi 
                        ?>
                    </div>
                <?php endif; // Menutup pengecekan data notifikasi kosong 
                ?>
            </div>
        </div>

        <!-- Kolom Display Utama Tab Inventori Skin & Log History -->
        <div class="col-lg-8 text-start">
            <!-- Navigasi Tabs Bootstrap kustom untuk berpindah sub-halaman di internal menu -->
            <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="pills-inventory-tab" data-bs-toggle="pill" data-bs-target="#pills-inventory" type="button" role="tab"><i class="bi bi-grid-3x3-gap-fill me-1"></i> Skin Saya (<?php echo count($my_skins); // Menampilkan total jumlah skin milik user 
                                                                                                                                                                                                                                        ?>)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Riwayat Transaksi</button>
                </li>
            </ul>

            <!-- Konten Masing-Masing Tab Menu -->
            <div class="tab-content" id="pills-tabContent">
                <!-- PANEL TAB INVENTORI SKIN -->
                <div class="tab-pane fade show active" id="pills-inventory" role="tabpanel">
                    <?php if (empty($my_skins)): // Membuka pengecekan jika user belum memiliki skin sama sekali 
                    ?>
                        <div class="text-center py-5 tab-custom p-4 border border-secondary border-dashed">
                            <i class="bi bi-emoji-frown fs-1 text-muted mb-3"></i>
                            <h5>Anda belum memiliki koleksi skin!</h5>
                            <p class="text-muted">Mulai bertransaksi dan beli skin pertamamu di pasar SkinsLove.</p>
                            <a href="pasar.php" class="btn btn-gradient px-4 py-2 mt-2 rounded-3">Beli Skin Pertama</a>
                        </div>
                    <?php else: // Blok alternatif jika user memiliki skin 
                    ?>
                        <div class="row row-cols-1 row-cols-sm-2 g-3">
                            <?php foreach ($my_skins as $skin): // Melakukan loop melalui array skin milik user 
                            ?>
                                <div class="col">
                                    <div class="skin-card p-3 h-100 d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="bg-dark p-2 rounded text-center" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <img src="<?php echo htmlspecialchars($skin['image']); // Mencetak path gambar skin dengan aman 
                                                            ?>" alt="<?php echo htmlspecialchars($skin['name']); // Mencetak nama skin 
                                                                                                                                                            ?>" class="img-fluid" style="max-height: 60px; object-fit: contain;">
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-white"><?php echo htmlspecialchars($skin['name']); // Menampilkan nama skin 
                                                                                    ?></h6>
                                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($skin['collection']); // Menampilkan nama koleksi skin 
                                                                                    ?></p>
                                                <div class="small text-warning fw-bold">Float: <?php echo number_format($skin['wear'], 4); // Menampilkan wear float rate skin 
                                                                                                ?></div>
                                                <div class="fw-bold text-success">$<?php echo number_format($skin['price'], 2); // Menampilkan harga skin 
                                                                                    ?></div>
                                            </div>
                                        </div>
                                        <div>
                                            <!-- Form untuk Menjual Kembali Skin dengan interceptor Bootstrap Toast -->
                                            <form action="sell_action.php" method="POST" class="mb-0 sell-back-form" data-skin-name="<?php echo htmlspecialchars($skin['name']); // Menyimpan nama skin untuk Toast 
                                                                                                                                        ?>" data-skin-price="<?php echo number_format($skin['price'], 2); // Menyimpan harga skin untuk Toast 
                                                                                                                                                                                                                                            ?>">
                                                <input type="hidden" name="action" value="sell"> <!-- Menyimpan tipe aksi kiriman form -->
                                                <input type="hidden" name="item_id" value="<?php echo $skin['id']; // Menyimpan ID skin untuk dikirim 
                                                                                            ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-3 py-2">
                                                    <i class="bi bi-currency-dollar me-1"></i> Jual Kembali
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; // Menutup loop daftar skin user 
                            ?>
                        </div>
                    <?php endif; // Menutup kondisional inventori skin 
                    ?>
                </div>

                <!-- PANEL TAB HISTORY TRANSAKSI -->
                <div class="tab-pane fade" id="pills-history" role="tabpanel">
                    <div class="tab-custom p-4 shadow">
                        <h5 class="fw-bold mb-4 text-white">Log Transaksi Belanja</h5>
                        <?php if (empty($history)): // Membuka pengecekan jika riwayat transaksi kosong 
                        ?>
                            <p class="text-muted small mb-0">Belum ada riwayat transaksi keuangan yang terekam.</p>
                        <?php else: // Blok alternatif jika riwayat tersedia 
                        ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-striped align-middle text-center">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Produk Skin</th>
                                            <th>Tipe</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $log): // Memulai perulangan data log riwayat transaksi 
                                        ?>
                                            <tr>
                                                <td class="small text-muted"><?php echo $log['created_at']; // Menampilkan tanggal transaksi 
                                                                                ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-start">
                                                        <img src="<?php echo htmlspecialchars($log['skin_image']); // Menampilkan icon gambar skin 
                                                                    ?>" alt="" style="width: 30px; object-fit: contain;">
                                                        <span class="small text-white fw-bold"><?php echo htmlspecialchars($log['skin_name']); // Menampilkan nama skin 
                                                                                                ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Menambahkan kolom status/tipe yang jelas buy/sell secara kontras -->
                                                    <?php if ($log['transaction_type'] === 'buy'): // Deteksi jika transaksi adalah pembelian 
                                                    ?>
                                                        <span class="badge bg-danger text-uppercase px-2 py-1">BUY / BELI</span>
                                                    <?php else: // Jika transaksi adalah penjualan 
                                                    ?>
                                                        <span class="badge bg-success text-uppercase px-2 py-1">SELL / JUAL</span>
                                                    <?php endif; // Selesai pengecekan jenis transaksi 
                                                    ?>
                                                </td>
                                                <td class="text-success fw-bold">$<?php echo number_format($log['price_paid'], 2); // Menampilkan harga transaksi 
                                                                                    ?></td>
                                                <td><span class="badge bg-success">Success</span></td>
                                            </tr>
                                        <?php endforeach; // Mengakhiri perulangan data log riwayat 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; // Menutup pengecekan kondisi riwayat kosong 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5. TEMPLATE BOOTSTRAP TOAST UNTUK KONFIRMASI PENJUALAN SECARA LUXURY -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="confirmSellToast" class="toast align-items-center text-white bg-dark border-danger" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false" style="border: 1px solid rgba(244, 63, 94, 0.3);">
        <div class="toast-header bg-dark text-white border-bottom border-secondary d-flex justify-content-between">
            <span class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                <strong class="me-auto">Konfirmasi Penjualan</strong>
            </span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-[#121216]">
            <p id="toastPromptText" class="mb-3 small">Apakah Anda yakin ingin menjual kembali skin ini seharga $0.00?</p>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-sm btn-secondary px-3 rounded-3" data-bs-dismiss="toast">Batal</button>
                <button type="button" id="toastConfirmBtn" class="btn btn-sm btn-gradient px-3 rounded-3">Ya, Jual</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; // Menyertakan file footer penutup global 
?>