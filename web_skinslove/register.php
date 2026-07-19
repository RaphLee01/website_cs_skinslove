<?php // Membuka tag PHP untuk manajemen pendaftaran pengguna baru
require_once 'config.php'; // Memanggil file konfigurasi database dan session global
$error = ''; // Inisialisasi variabel error penampung pesan kesalahan registrasi
$success = ''; // Inisialisasi variabel sukses penampung pesan sukses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Memeriksa jika pengiriman form adalah metode POST
    $username = trim($_POST['username'] ?? ''); // Mengambil data username dan membersihkan spasi berlebih
    $email = trim($_POST['email'] ?? ''); // Mengambil data email dan membersihkan karakter ilegal
    $password = $_POST['password'] ?? ''; // Mengambil password murni dari form registrasi
    $confirm_password = $_POST['confirm_password'] ?? ''; // Mengambil konfirmasi password murni
    if (empty($username) || empty($email) || empty($password)) { // Memeriksa apakah ada data wajib mendaftar yang kosong
        $error = 'Harap isi semua kolom pendaftaran yang tersedia!'; // Menyimpan pesan kesalahan kolom pendaftaran kosong
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Memeriksa keabsahan format penulisan email
        $error = 'Format penulisan alamat email tidak valid!'; // Menyimpan pesan kesalahan email tidak valid
    } elseif ($password !== $confirm_password) { // Memeriksa apakah password utama berbeda dengan konfirmasi password
        $error = 'Konfirmasi sandi tidak cocok, silakan periksa kembali sandi Anda!'; // Menyimpan pesan kesalahan sandi berbeda
    } elseif (strlen($password) < 6) { // Memeriksa batasan minimal karakter sandi untuk keamanan akun
        $error = 'Kata sandi minimal harus terdiri dari 6 karakter!'; // Menyimpan pesan kesalahan sandi terlalu pendek
    } else { // Jika validasi input dasar berhasil dilewati
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email"); // Menyiapkan query untuk memeriksa keunikan username dan email
        $stmt->execute(['username' => $username, 'email' => $email]); // Mengeksekusi query database dengan parameter aman
        if ($stmt->rowCount() > 0) { // Memeriksa jika baris data ditemukan sebagai tanda akun sudah terdaftar
            $error = 'Username atau Email sudah terdaftar dalam sistem SkinsLove!'; // Menyimpan pesan kegagalan akun ganda
        } else { // Jika username dan email dipastikan unik
            $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Mengenkripsi password dengan algoritma hash bcrypt standar industri
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role, balance) VALUES (:username, :email, :password, 'user', 0.00)"); // Menyiapkan query untuk memasukkan data akun user baru
            if ($insert_stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password])) { // Menjalankan query insert database dengan param terenkripsi
                $success = 'Pendaftaran akun berhasil! Silakan masuk menggunakan akun baru Anda.'; // Menyimpan pesan kesuksesan registrasi
            } else { // Jika query insert database gagal dijalankan
                $error = 'Terjadi kesalahan sistem, gagal mendaftarkan akun Anda!'; // Menyimpan pesan kegagalan transaksi database
            } // Penutup kondisi eksekusi insert database
        } // Penutup kondisi keunikan akun ganda
    } // Penutup kondisi validasi input dasar
} // Penutup kondisi request POST
?> <!-- Menutup blok PHP inisialisasi registrasi -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - SkinsLove.gg</title>
    <!-- Mengimpor framework CSS Bootstrap 5 melalui CDN eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mengimpor Bootstrap Icons untuk ikon menyembunyikan/melihat kata sandi -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Mengimpor file CSS eksternal terpisah untuk kerapian kode -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-register p-4 shadow-lg text-start" style="background-color: #121216; border: 1px solid rgba(244, 63, 94, 0.2); border-radius: 12px;">
                    <h2 class="text-center font-bold mb-1 text-white">Skins<span class="text-gradient">Love.gg</span></h2>
                    <p class="text-muted text-center mb-4">Buat akun baru untuk mulai transaksi skins CS2</p>

                    <?php if (!empty($error)): // Membuka pengecekan jika ada string error registrasi ?>
                        <div class="alert alert-danger text-center py-2" role="alert">
                            <?php echo htmlspecialchars($error); // Mencetak isi pesan error aman ter-escape ?>
                        </div>
                    <?php endif; // Mengakhiri blok kondisi IF error ?>

                    <?php if (!empty($success)): // Membuka pengecekan jika pendaftaran sukses ?>
                        <div class="alert alert-success text-center py-2" role="alert">
                            <?php echo htmlspecialchars($success); // Mencetak isi pesan sukses registrasi ?>
                        </div>
                    <?php endif; // Mengakhiri blok kondisi IF sukses ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label text-muted">Username Baru</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username..." required autocomplete="off" value="<?php echo htmlspecialchars($username ?? ''); // Mempertahankan input username sebelumnya ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-muted">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" required value="<?php echo htmlspecialchars($email ?? ''); // Mempertahankan input email sebelumnya ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-muted">Kata Sandi</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter..." required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: #2d2d39; background-color: #1a1a22; color: #94a3b8;">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label text-muted">Ulangi Kata Sandi</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi sandi..." required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" style="border-color: #2d2d39; background-color: #1a1a22; color: #94a3b8;">
                                    <i class="bi bi-eye" id="eyeIconConfirm"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 py-2.5 rounded-3 mb-3">Daftar Sekarang</button>
                    </form>
                    
                    <div class="text-center mt-2">
                        <span class="text-muted small">Sudah punya akun SkinsLove?</span>
                        <a href="login.php" class="text-decoration-none small text-gradient fw-bold">Masuk di Sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Menyertakan library Javascript Bootstrap 5 agar interaksi UI berjalan lancar -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Menyertakan file Javascript kustom eksternal untuk pengontrol toggler password -->
    <script src="script/main.js"></script>
</body>
</html>
