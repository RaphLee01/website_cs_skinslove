<?php
// Memanggil file config.php untuk memulai koneksi database pdo dan status session user
require_once 'config.php';

$success_msg = ''; // Variabel untuk menyimpan pesan kesuksesan pengiriman saran
$error_msg = '';   // Variabel untuk menyimpan pesan kegagalan pengiriman saran

// Memeriksa jika formulir saran telah dikirimkan oleh pengguna menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');       // Membersihkan inputan nama pengirim dari spasi luar
    $email = trim($_POST['email'] ?? '');     // Membersihkan inputan email pengirim
    $subject = trim($_POST['subject'] ?? ''); // Mengambil subjek saran/pesan
    $message = trim($_POST['message'] ?? ''); // Mengambil isi pesan saran secara lengkap

    // Memastikan tidak ada kolom input yang dikosongkan oleh pengirim pesan
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_msg = "Harap lengkapi semua kolom formulir hubungi kami!"; // Isi pesan kesalahan
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Format penulisan alamat email Anda tidak valid!"; // Validasi email salah format
    } else {
        try {
            // Mempersiapkan query SQL INSERT untuk memasukkan data saran baru ke tabel suggestions
            $insert_stmt = $conn->prepare("INSERT INTO suggestions (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
            // Mengeksekusi penulisan baris data saran pengguna secara aman menggunakan PDO binding
            $insert_stmt->execute([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ]);

            // Jika pengirim pesan sudah login, berikan notifikasi real-time ke akun mereka
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id']; // Mengambil ID pengguna dari session
                $notif_title = "Pesan Saran Terkirim!"; // Judul notifikasi otomatis
                $notif_msg = "Terima kasih atas saran/pesan Anda dengan subjek '" . htmlspecialchars($subject) . "'. Admin kami akan meninjaunya sesegera mungkin di dashboard!"; // Isi pesan notifikasi
                $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)");
                $notif_stmt->execute(['user_id' => $user_id, 'title' => $notif_title, 'message' => $notif_msg]); // Masukkan notifikasi ke DB
            }

            $success_msg = "Saran Anda berhasil dikirim! Pesan tersebut kini telah muncul langsung di Admin Dashboard kami."; // Pesan sukses
        } catch (Exception $e) {
            $error_msg = "Gagal mengirimkan saran: " . $e->getMessage(); // Tangkap kesalahan query database
        }
    }
}

$page_title = "Hubungi Kami & Saran - SkinsLove.gg";
require_once 'includes/header.php';
?>

    <!-- Konten Utama Halaman Hubungi Kami / Saran Pengguna -->
    <div class="container my-5">
        <div class="row g-4 align-items-center">
            <!-- Informasi Kontak & Lokasi Kantor Simulasi -->
            <div class="col-md-5">
                <span class="badge bg-danger mb-2 px-3 py-1.5 rounded-pill text-uppercase">Hubungi Admin</span>
                <h2 class="fw-bold text-white mb-3">Punya Masukan atau Saran?</h2>
                <p class="text-muted small mb-4" style="line-height: 1.7; text-align: justify;">
                    Kami di SkinsLove.gg selalu berusaha keras meningkatkan kenyamanan bertransaksi dan penyaringan produk skin CS2. Jika Anda memiliki saran perbaikan, laporan bug sistem, atau ide fitur baru, silakan kirimkan melalui formulir di samping.
                </p>

                <!-- Detail Alamat & Jam Kerja -->
                <div class="d-flex mb-3 align-items-center">
                    <div class="fs-4 text-gradient me-3"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <strong class="text-white d-block small">Alamat :</strong>
                        <span class="text-muted small">Jl. Kurang Pinggir</span>
                    </div>
                </div>
                <div class="d-flex mb-3 align-items-center">
                    <div class="fs-4 text-gradient me-3"><i class="bi bi-envelope-at-fill"></i></div>
                    <div>
                        <strong class="text-white d-block small">Email Layanan:</strong>
                        <span class="text-muted small">support@skinslove.gg</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="fs-4 text-gradient me-3"><i class="bi bi-clock-fill"></i></div>
                    <div>
                        <strong class="text-white d-block small">Live Support:</strong>
                        <span class="text-muted small">24/7</span>
                    </div>
                </div>
            </div>

            <!-- Formulir Interaktif Pengiriman Feedback/Saran -->
            <div class="col-md-7">
                <div class="cart-card p-4 shadow">
                    <h5 class="fw-bold text-white mb-3"><i class="bi bi-envelope-paper-fill text-gradient me-2"></i>Contact Us</h5>
                    
                    <!-- Alert Status Sukses / Gagal -->
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success border-0 small" style="background-color: #1c3d30; color: #a3e635;" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_msg; ?>
                        </div>
                    <?php elseif (!empty($error_msg)): ?>
                        <div class="alert alert-danger border-0 small" style="background-color: #4c1d1d; color: #f87171;" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST">
                        <div class="row g-3">
                            <!-- Kolom Nama Pengirim -->
                            <div class="col-md-6">
                                <label for="name" class="form-label text-muted small">Nama Anda</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" required>
                            </div>
                            <!-- Kolom Email Pengirim -->
                            <div class="col-md-6">
                                <label for="email" class="form-label text-muted small">Alamat Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="nama@gmail.com" required>
                            </div>
                            <!-- Kolom Subjek / Topik Utama -->
                            <div class="col-12">
                                <label for="subject" class="form-label text-muted small">Subjek / Topik Pesan</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Contoh: Bug filter pencarian, Saran e-wallet..." required>
                            </div>
                            <!-- Kolom Isi Saran/Pesan Detail -->
                            <div class="col-12">
                                <label for="message" class="form-label text-muted small">Pesan / Saran Detail</label>
                                <textarea name="message" id="message" rows="5" class="form-control" placeholder="Tuliskan ide brilian atau masukan konstruktif Anda untuk SkinsLove.gg di sini..." required></textarea>
                            </div>
                            <!-- Tombol Kirim -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-gradient w-100 py-2.5 rounded-3 fw-bold">
                                    <i class="bi bi-send-fill me-2"></i> Kirim Saran & Masukan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
