<?php
// Mengimpor file konfigurasi database untuk koneksi pdo dan session global
require_once 'config.php';

// Menyiapkan array keranjang belanja kosong sebagai default penampung data
$cart_items = [];
// Mendefinisikan nominal total belanja bernilai nol di awal perhitungan
$total_price = 0.00;

// Memeriksa apakah session user sudah login atau belum, jika belum, dialihkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Menghubungkan variabel session user_id ke sebuah variabel lokal agar mudah dipanggil
$user_id = $_SESSION['user_id'];

// Mengambil seluruh item belanja milik user bersangkutan dari tabel database cart dan items secara relasi
$stmt = $conn->prepare("SELECT c.id AS cart_id, i.* FROM cart c JOIN items i ON c.item_id = i.id WHERE c.user_id = :user_id");
// Mengeksekusi penarikan data keranjang belanja milik user tersebut
$stmt->execute(['user_id' => $user_id]);
// Menyimpan list item belanja ke dalam variabel $cart_items sebagai array asosiatif
$cart_items = $stmt->fetchAll();

// Menghitung total akumulasi harga dari seluruh skin CS2 yang dimasukkan ke dalam keranjang belanja
foreach ($cart_items as $item) {
    // Menambahkan harga tiap skin yang ada di keranjang belanja ke nominal total harga
    $total_price += floatval($item['price']);
}
?>
<?php
// Menentukan judul halaman khusus untuk tab cart.php
$page_title = "Keranjang Belanja - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- Konten Keranjang Belanja -->
    <div class="container my-5">
        <h3 class="fw-bold text-gradient mb-4"><i class="bi bi-cart3 me-2"></i> Keranjang Belanja Anda</h3>
        
        <div class="row">
            <!-- Sisi Kiri: Daftar Skin dalam Keranjang Belanja -->
            <div class="col-lg-8 mb-4">
                <?php if (empty($cart_items)): ?>
                    <div class="text-center py-5 cart-card p-4 rounded-4 border border-secondary border-dashed">
                        <i class="bi bi-cart-x fs-1 text-muted mb-3"></i>
                        <h5>Keranjang belanja Anda kosong!</h5>
                        <p class="text-muted">Kembali ke pasar dan temukan skin CS2 impianmu.</p>
                        <a href="index.php" class="btn btn-gradient px-4 py-2 mt-2 rounded-3">Cari Skin</a>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-card p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-dark p-2 rounded text-center" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid" style="max-height: 60px; object-fit: contain;">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-white"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($item['collection']); ?> | Float: <?php echo number_format($item['wear'], 4); ?></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-4 w-100 w-md-auto justify-content-between justify-content-md-end">
                                    <h5 class="fw-bold text-success mb-0">$<?php echo number_format($item['price'], 2); ?></h5>
                                    <!-- Form untuk menghapus item terpilih dari keranjang belanja -->
                                    <form action="cart_action.php" method="POST">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sisi Kanan: Box Ringkasan Belanja & Tombol Checkout Transaksi -->
            <div class="col-lg-4">
                <div class="checkout-box p-4 shadow">
                    <h5 class="fw-bold mb-4">Ringkasan Pembelian</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Item</span>
                        <span class="fw-bold"><?php echo count($cart_items); ?> Skin</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-secondary">
                        <span class="text-muted">Saldo Anda</span>
                        <span class="fw-bold text-info">$<?php echo number_format($_SESSION['balance'] ?? 0, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <h6 class="fw-bold">Total Tagihan</h6>
                        <h4 class="fw-bold text-success">$<?php echo number_format($total_price, 2); ?></h4>
                    </div>

                    <?php if (!empty($cart_items)): ?>
                        <!-- Form untuk melakukan konfirmasi checkout instan -->
                        <form action="cart_action.php" method="POST">
                            <input type="hidden" name="action" value="checkout">
                            <!-- Tombol checkout dengan validasi ketersediaan saldo sebelum klik -->
                            <button type="submit" class="btn btn-gradient w-100 py-3 rounded-3" <?php echo ($_SESSION['balance'] < $total_price) ? 'disabled' : ''; ?>>
                                <i class="bi bi-wallet2 me-1"></i> Bayar Sekarang
                            </button>
                        </form>
                        <?php if ($_SESSION['balance'] < $total_price): ?>
                            <div class="alert alert-danger mt-3 small text-center" role="alert">
                                Saldo akun Anda tidak mencukupi untuk melakukan checkout belanjaan ini!
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php require_once 'includes/footer.php'; ?>
