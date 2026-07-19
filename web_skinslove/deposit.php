<?php
// Memanggil file config.php untuk memulai koneksi database pdo dan status session user
require_once 'config.php';

// Memeriksa apakah user sudah login, jika belum maka diarahkan ke halaman masuk (login.php)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Tambah Saldo - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- Konten Utama Pengisian Saldo (Deposit) -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Card Utama Menu Top-up Saldo -->
                <div class="cart-card p-4 shadow">
                    <div class="text-center mb-4">
                        <div class="text-gradient d-inline-block mb-2" style="font-size: 3rem;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-1">Top-Up Saldo Akun</h4>
                        <p class="text-muted small">Pilih atau masukkan nominal saldo USD ($) yang ingin ditambahkan</p>
                    </div>

                    <!-- Tampilan Saldo Pengguna Saat Ini -->
                    <div class="bg-dark p-3 rounded-3 border border-secondary text-center mb-4">
                        <span class="text-muted d-block small mb-1">Saldo Anda Saat Ini</span>
                        <span class="fs-4 fw-bold text-success">$<?php echo number_format($_SESSION['balance'] ?? 0, 2); ?></span>
                    </div>

                    <!-- Formulir Deposit Saldo -->
                    <form action="payment_gateway.php" method="GET">
                        <!-- Pilihan Paket Nominal Cepat -->
                        <label class="form-label text-muted small mb-2">Pilih Nominal Cepat</label>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2.5 btn-nominal" data-amount="10">$10</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2.5 btn-nominal" data-amount="50">$50</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2.5 btn-nominal" data-amount="100">$100</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2.5 btn-nominal" data-amount="250">$250</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2.5 btn-nominal" data-amount="500">$500</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2.5 btn-nominal" data-amount="1000">$1000</button>
                            </div>
                        </div>

                        <!-- Kolom Input Nominal Custom -->
                        <div class="mb-4">
                            <label for="amount" class="form-label text-muted small mb-2">Atau Masukkan Nominal Custom ($ USD)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-success fw-bold">$</span>
                                <input type="number" min="1" max="10000" step="1" name="amount" id="amount" class="form-control" placeholder="Contoh: 150" required>
                            </div>
                            <div class="form-text text-muted small mt-1">Minimal pengisian adalah $1.00 dan maksimal $10,000.00</div>
                        </div>

                        <!-- Tombol Submit untuk Mengarah ke Payment Gateway Simulasi -->
                        <button type="submit" class="btn btn-gradient w-100 py-3 rounded-3 fw-bold">
                            <i class="bi bi-shield-lock-fill me-2"></i> Lanjutkan Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Kustom untuk Menangani Pengisian Nominal Otomatis -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnNominals = document.querySelectorAll('.btn-nominal');
            const inputAmount = document.getElementById('amount');

            btnNominals.forEach(button => {
                button.addEventListener('click', function() {
                    // Menghapus kelas aktif dari semua tombol nominal cepat
                    btnNominals.forEach(btn => btn.classList.replace('btn-gradient', 'btn-outline-secondary'));
                    
                    // Mengubah tombol terpilih menjadi bergradasi rose pink aktif
                    this.classList.replace('btn-outline-secondary', 'btn-gradient');
                    
                    // Mengisi nilai nominal cepat ke dalam input numerik custom
                    inputAmount.value = this.getAttribute('data-amount');
                });
            });

            // Mengosongkan status tombol aktif jika user mengetik nominal custom sendiri
            inputAmount.addEventListener('input', function() {
                btnNominals.forEach(btn => btn.classList.replace('btn-gradient', 'btn-outline-secondary'));
            });
        });
    </script>

<?php require_once 'includes/footer.php'; ?>
