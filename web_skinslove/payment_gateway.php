<?php
// Memanggil file config.php untuk memulai koneksi database pdo dan status session user
require_once 'config.php';

// Memeriksa apakah user sudah login, jika belum maka diarahkan ke halaman masuk (login.php)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Mengalihkan ke login jika belum login
    exit; // Berhenti mengeksekusi script
}

// Mengambil parameter nominal pembayaran dari form input GET atau POST
$amount = floatval($_GET['amount'] ?? $_POST['amount'] ?? 0);

// Jika nominal tidak valid (kurang dari atau sama dengan nol), kembalikan ke menu deposit
if ($amount <= 0) {
    header('Location: deposit.php'); // Mengalihkan kembali ke halaman deposit
    exit; // Berhenti mengeksekusi script
}

// Menangani proses eksekusi pembayaran saat tombol submit "Konfirmasi Pembayaran" diklik via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    $payment_method = $_POST['payment_method'] ?? 'QRIS Instan'; // Mengambil pilihan metode pembayaran
    $user_id = $_SESSION['user_id']; // Mengambil ID pengguna dari session aktif

    // Membuka database transaction demi menjaga integritas data saldo pengguna
    $conn->beginTransaction();

    try {
        // 1. Update kolom balance saldo milik user bersangkutan di dalam database
        $update_stmt = $conn->prepare("UPDATE users SET balance = balance + :amount WHERE id = :id");
        $update_stmt->execute(['amount' => $amount, 'id' => $user_id]); // Eksekusi query update saldo

        // 2. Mengambil saldo terbaru dari database untuk diletakkan kembali ke variabel session
        $select_stmt = $conn->prepare("SELECT balance FROM users WHERE id = :id");
        $select_stmt->execute(['id' => $user_id]); // Ambil row user terbaru
        $new_balance = $select_stmt->fetchColumn(); // Dapatkan nilai balance terbaru

        // 3. Mengupdate variabel $_SESSION['balance'] agar saldo di navbar langsung berubah seketika
        $_SESSION['balance'] = $new_balance;

        // 4. Menyisipkan pesan notifikasi masuk real-time ke dalam tabel notifications
        $notif_title = "Top-Up Saldo Berhasil!"; // Judul notifikasi
        $notif_msg = "Selamat! Saldo sebesar $" . number_format($amount, 2) . " telah berhasil ditambahkan ke akun Anda menggunakan metode " . htmlspecialchars($payment_method) . ". Selamat berbelanja skin CS2 impian!"; // Isi notifikasi
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)");
        $notif_stmt->execute(['user_id' => $user_id, 'title' => $notif_title, 'message' => $notif_msg]); // Eksekusi input notifikasi

        // Melakukan commit perubahan permanen ke database SQL
        $conn->commit();

        // Mengalihkan kembali pengguna ke menu inventori dengan parameter sukses top-up
        header("Location: inventory.php?status=success_deposit&amount=" . $amount);
        exit; // Keluar dari pemrosesan script
    } catch (Exception $e) {
        // Membatalkan semua proses modifikasi database jika terjadi kegagalan sistem ditengah jalan
        $conn->rollBack();
        $error = "Gagal memproses pengisian saldo: " . $e->getMessage(); // Tampilkan pesan kegagalan
    }
}

$page_title = "Secure Checkout - SkinsLove Payment Gateway";
require_once 'includes/header.php';
?>

    <!-- Konten Utama Simulasi Payment Gateway Terintegrasi -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <!-- Kolom Summary Tagihan & Pilihan Metode Pembayaran -->
            <div class="col-md-8">
                <!-- Banner Keamanan / Sandbox Notice -->
                <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert" style="background-color: rgba(234, 179, 8, 0.15); color: #facc15;">
                    <div class="d-flex">
                        <div class="fs-4 me-3"><i class="bi bi-shield-fill-exclamation"></i></div>
                        <div>
                            <strong class="d-block mb-1">METODE PEMBAYARAN SANDBOX (SIMULASI)</strong>
                            <p class="small mb-0 opacity-85">Halaman ini sepenuhnya merupakan simulasi transaksi pengisian saldo untuk keperluan Pemrograman Web. Tidak ada uang asli yang didebit atau dipotong dari rekening Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Sisi Kiri: Pilihan Metode Pembayaran -->
                    <div class="col-md-7">
                        <div class="cart-card p-4 shadow mb-4">
                            <h5 class="fw-bold text-white mb-3"><i class="bi bi-credit-card-2-front text-gradient me-2"></i>Pilih Metode Pembayaran</h5>
                            
                            <!-- Form Utama Checkout Transaksi -->
                            <form action="payment_gateway.php" method="POST">
                                <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                                <input type="hidden" name="process_payment" value="1">

                                <!-- Pilihan 1: QRIS QR-Code Instan -->
                                <div class="form-check p-3 rounded-3 border border-secondary mb-3 payment-option cursor-pointer active-option" style="background-color: #1a1a22;">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_qris" value="QRIS Instan" checked>
                                    <label class="form-check-label text-white fw-semibold" for="pay_qris">
                                        <i class="bi bi-qr-code-scan text-gradient me-2"></i> QRIS (Gopay/OVO/Dana)
                                        <span class="d-block text-muted small fw-normal mt-1">Konfirmasi pembayaran otomatis dalam 2 detik menggunakan QR code</span>
                                    </label>
                                    <!-- Area Gambar Barcode QRIS Simulasi -->
                                    <div id="qris_barcode_area" class="text-center mt-3 p-2 bg-white rounded-3 d-inline-block mx-auto w-100" style="max-width: 180px;">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=SkinsLoveDeposit_<?php echo time(); ?>" alt="QRIS Barcode" class="img-fluid">
                                        <div class="text-dark small fw-bold mt-1" style="font-size: 0.65rem;">SCAN QRIS SIMULASI</div>
                                    </div>
                                </div>

                                <!-- Pilihan 2: Virtual Account Bank Transfer -->
                                <div class="form-check p-3 rounded-3 border border-secondary mb-3 payment-option cursor-pointer" style="background-color: #121216;">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_va" value="Virtual Account">
                                    <label class="form-check-label text-white fw-semibold" for="pay_va">
                                        <i class="bi bi-bank text-gradient me-2"></i> Bank Virtual Account (BCA/Mandiri/BNI)
                                        <span class="d-block text-muted small fw-normal mt-1">Nomor virtual account pembayaran unik akan diberikan setelah diklik</span>
                                    </label>
                                    <!-- Info Bank VA Simulasi -->
                                    <div id="va_info_area" class="mt-3 p-2 rounded bg-dark border border-secondary d-none">
                                        <div class="small text-muted mb-1">Nomor Virtual Account BCA:</div>
                                        <div class="text-warning fw-bold font-mono fs-5">88301 0812 3456 789</div>
                                        <div class="small text-muted mt-1">Gunakan nominal persis untuk kelancaran verifikasi.</div>
                                    </div>
                                </div>

                                <!-- Pilihan 3: Kartu Kredit / Debit Online -->
                                <div class="form-check p-3 rounded-3 border border-secondary mb-4 payment-option cursor-pointer" style="background-color: #121216;">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_cc" value="Kartu Kredit">
                                    <label class="form-check-label text-white fw-semibold" for="pay_cc">
                                        <i class="bi bi-credit-card text-gradient me-2"></i> Kartu Kredit / Debit Visa & MasterCard
                                        <span class="d-block text-muted small fw-normal mt-1">Masukkan data kartu kredit simulasi Anda dengan aman</span>
                                    </label>
                                    <!-- Input CC Simulasi -->
                                    <div id="cc_input_area" class="mt-3 d-none">
                                        <div class="mb-2">
                                            <label class="small text-muted">Nomor Kartu</label>
                                            <input type="text" class="bg-dark text-info form-control py-1 font-mono small" placeholder="4111 2222 3333 4444" value="4111222233334444" disabled>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="small text-muted">Masa Berlaku</label>
                                                <input type="text" class="bg-dark text-info form-control py-1 font-mono small" placeholder="MM/YY" value="12/28" disabled>
                                            </div>
                                            <div class="col-6">
                                                <label class="small text-muted">CVV</label>
                                                <input type="password" class="bg-dark text-info form-control py-1 font-mono small" placeholder="123" value="123" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Proses Bayar Sekarang -->
                                <button type="submit" class="btn btn-gradient w-100 py-3 rounded-3 fw-bold shadow">
                                    <i class="bi bi-patch-check-fill me-2"></i> Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Detail Ringkasan Invoice & Pembayaran -->
                    <div class="col-md-5">
                        <div class="checkout-box p-4 shadow">
                            <h6 class="fw-bold text-white mb-3 border-bottom border-secondary pb-2">Ringkasan Pembayaran</h6>
                            
                            <!-- Info Identitas Transaksi -->
                            <div class="mb-3 small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Nomor Order:</span>
                                    <span class="text-white fw-bold font-mono">SLV-<?php echo strtoupper(substr(md5(time()), 0, 8)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Nama Akun:</span>
                                    <span class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Status:</span>
                                    <span class="badge bg-secondary font-mono">PENDING/SANDBOX</span>
                                </div>
                            </div>

                            <hr class="border-secondary my-3">

                            <!-- Nominal Transaksi -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Top-Up Saldo ($):</span>
                                    <span class="text-white fw-bold">$<?php echo number_format($amount, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Biaya Layanan:</span>
                                    <span class="text-success small fw-semibold">FREE ($0.00)</span>
                                </div>
                                <hr class="border-secondary my-2">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-white fw-bold">Total Pembayaran:</span>
                                    <span class="text-gradient fw-bold fs-4">$<?php echo number_format($amount, 2); ?></span>
                                </div>
                            </div>

                            <!-- Footer Keamanan Gateway -->
                            <div class="text-center text-muted small" style="font-size: 0.7rem;">
                                <i class="bi bi-shield-fill-check text-success me-1"></i> Terenkripsi dengan sistem keamanan simulasi 256-bit SSL.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Kustom Interaktif untuk Menangani Perpindahan Pilihan Metode Pembayaran -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payQris = document.getElementById('pay_qris');
            const payVa = document.getElementById('pay_va');
            const payCc = document.getElementById('pay_cc');

            const qrisArea = document.getElementById('qris_barcode_area');
            const vaArea = document.getElementById('va_info_area');
            const ccArea = document.getElementById('cc_input_area');

            // Event listener saat memilih QRIS
            payQris.addEventListener('change', function() {
                if (this.checked) {
                    qrisArea.classList.remove('d-none');
                    vaArea.classList.add('d-none');
                    ccArea.classList.add('d-none');
                }
            });

            // Event listener saat memilih Virtual Account
            payVa.addEventListener('change', function() {
                if (this.checked) {
                    qrisArea.classList.add('d-none');
                    vaArea.classList.remove('d-none');
                    ccArea.classList.add('d-none');
                }
            });

            // Event listener saat memilih Kartu Kredit
            payCc.addEventListener('change', function() {
                if (this.checked) {
                    qrisArea.classList.add('d-none');
                    vaArea.classList.add('d-none');
                    ccArea.classList.remove('d-none');
                }
            });
        });
    </script>

<?php require_once 'includes/footer.php'; ?>
