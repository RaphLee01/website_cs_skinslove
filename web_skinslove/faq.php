<?php
// Memanggil file config.php untuk memulai koneksi database pdo dan status session user
require_once 'config.php';

$page_title = "Frequently Asked Questions (FAQ) - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- Konten Utama Halaman Tanya Jawab FAQ -->
    <div class="container my-5">
        <div class="text-center mb-5">
            <span class="badge bg-danger mb-2 px-3 py-1.5 rounded-pill text-uppercase">Pusat Bantuan & Tanya Jawab</span>
            <h1 class="fw-bold text-white mb-2">Frequently Asked Questions</h1>
            <p class="text-muted">Temukan jawaban atas berbagai pertanyaan umum seputar pembelian dan pengisian saldo di SkinsLove.gg</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9">
                <!-- Bootstrap 5 Accordion dengan Tema Gelap Khusus -->
                <div class="accordion accordion-flush shadow rounded-3 border border-secondary overflow-hidden" id="accordionFaq">
                    
                    <!-- FAQ 1: Apa itu SkinsLove.gg? -->
                    <div class="accordion-item" style="background-color: #121216; border-bottom: 1px solid #2d2d39;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="background-color: #121216; color: white;">
                                <i class="bi bi-question-circle text-gradient me-2"></i> Apa itu SkinsLove.gg?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small" style="line-height: 1.7;">
                                SkinsLove.gg adalah platform marketplace buy and trade digital yang berspesialisasi dalam penjualan serta pertukaran kosmetik senjata game Counter-Strike 2 (CS2). Kami menyediakan ekosistem terintegrasi yang memudahkan gamer mencari, meninjau wear float, mengumpulkan di keranjang belanja, hingga menyelesaikan pembelian dengan instan.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2: Bagaimana cara melakukan pengisian saldo (Top-up)? -->
                    <div class="accordion-item" style="background-color: #121216; border-bottom: 1px solid #2d2d39;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="background-color: #121216; color: white;">
                                <i class="bi bi-wallet2 text-gradient me-2"></i> Bagaimana cara melakukan pengisian saldo?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small" style="line-height: 1.7;">
                                Sangat mudah! Setelah Anda login ke akun SkinsLove.gg, klik tombol tanda tambah (+) yang berada tepat di samping informasi saldo pada bagian kanan navbar. Masukkan nominal yang ingin diisikan, lalu tekan tombol "Lanjutkan Pembayaran". Anda akan diarahkan ke simulasi payment gateway untuk memilih metode transfer (QRIS, Bank Virtual Account, atau Kartu Kredit).
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3: Apakah ini menggunakan transaksi uang asli? -->
                    <div class="accordion-item" style="background-color: #121216; border-bottom: 1px solid #2d2d39;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="background-color: #121216; color: white;">
                                <i class="bi bi-shield-exclamation text-gradient me-2"></i> Apakah ini menggunakan transaksi uang asli?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small" style="line-height: 1.7;">
                                <strong>Tidak.</strong> Website SkinsLove.gg ini merupakan proyek tugas akademik UAS Pemrograman Web yang dibuat menggunakan PHP Native Prosedural dengan Bootstrap 5. Semua transaksi, saldo akun, pemotongan keranjang belanja, deposit, dan riwayat di dalamnya bersifat simulasi interaktif (sandbox) dan tidak membebankan tagihan riil kepada Anda.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4: Apa yang dimaksud dengan wear rating atau float value? -->
                    <div class="accordion-item" style="background-color: #121216; border-bottom: 1px solid #2d2d39;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="background-color: #121216; color: white;">
                                <i class="bi bi-compass text-gradient me-2"></i> Apa yang dimaksud dengan wear rating atau float value?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small" style="line-height: 1.7;">
                                Wear rating atau float value dalam game CS2 adalah rentang nilai numerik dari 0.00 hingga 1.00 yang menentukan tingkat keausan atau lecet pada tampilan visual skin senjata. Nilai yang semakin mendekati 0.00 dikategorikan sebagai "Factory New" (sangat mulus tanpa goresan), sementara nilai yang mendekati 1.00 digolongkan sebagai "Battle-Scarred" (terlihat usang dan baret-baret). Anda dapat menyaring item di SkinsLove.gg berdasarkan float value ini!
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5: Bagaimana jika saya ingin mengirim saran atau keluhan? -->
                    <div class="accordion-item" style="background-color: #121216; border-bottom: none;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" style="background-color: #121216; color: white;">
                                <i class="bi bi-envelope-check text-gradient me-2"></i> Bagaimana jika saya ingin mengirim saran atau keluhan?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small" style="line-height: 1.7;">
                                Kami sangat mengapresiasi masukan dari pengguna! Anda bisa menavigasi ke halaman <a href="contact.php" class="text-gradient text-decoration-none fw-bold">Hubungi Kami</a> yang tersedia di menu footer website ini. Isi formulir yang disediakan, kirimkan pesan Anda, dan saran tersebut akan langsung terkirim dan ditampilkan pada halaman Admin Dashboard panel utama.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Gaya CSS Kustom untuk Merapikan Visual Accordion Bootstrap di Tema Gelap -->
    <style>
        .accordion-button::after {
            filter: invert(1) grayscale(1) brightness(2);
        }
        .accordion-button:not(.collapsed) {
            color: #f43f5e !important;
            background-color: #1c1c24 !important;
            box-shadow: inset 0 -1px 0 rgba(244, 63, 94, 0.2);
        }
    </style>

<?php require_once 'includes/footer.php'; ?>
