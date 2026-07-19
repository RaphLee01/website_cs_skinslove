<?php // Membuka tag PHP untuk manajemen login dan inisialisasi database
require_once 'config.php'; // Memanggil file konfigurasi database dan session global
if (isset($_SESSION['user_id'])) { // Memeriksa jika user sudah login sebelumnya
    header('Location: index.php'); // Melakukan pengalihan ke halaman beranda
    exit; // Menyudahi jalannya script
} // Penutup pengecekan login sebelumnya
$error = ''; // Inisialisasi variabel error penampung pesan kesalahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Memeriksa jika request form adalah POST
    $email = trim($_POST['email'] ?? ''); // Mengambil input email dan membersihkan whitespace
    $password = $_POST['password'] ?? ''; // Mengambil input password murni
    if (empty($email) || empty($password)) { // Memeriksa jika ada input yang kosong
        $error = 'Silakan isi alamat email dan kata sandi Anda dengan lengkap!'; // Mengisi pesan kesalahan kolom kosong
    } else { // Jika input terisi semua
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email"); // Menyiapkan query mengambil data user berdasarkan email
        $stmt->execute(['email' => $email]); // Mengeksekusi query database dengan parameter aman
        $user = $stmt->fetch(); // Mengambil baris data tunggal hasil pencarian
        if ($user && password_verify($password, $user['password'])) { // Memvalidasi kecocokan password dengan hash database
            $_SESSION['user_id'] = $user['id']; // Menyimpan id user ke data session global
            $_SESSION['username'] = $user['username']; // Menyimpan nama user ke data session global
            $_SESSION['role'] = $user['role']; // Menyimpan tingkatan role user ke session global
            $_SESSION['balance'] = $user['balance']; // Menyimpan nominal saldo user ke session global
            header('Location: index.php'); // Mengalihkan halaman ke beranda karena login sukses
            exit; // Menghentikan eksekusi script selanjutnya
        } else { // Jika otentikasi login gagal
            $error = 'Alamat email atau kata sandi yang Anda masukkan salah!'; // Mengisi pesan kegagalan login
        } // Penutup kondisi hash password
    } // Penutup kondisi validasi kolom terisi
} // Penutup kondisi request POST
?> <!-- Menutup blok PHP inisialisasi login -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - SkinsLove.gg</title>
    <!-- Mengimpor framework CSS Bootstrap 5 lewat jaringan CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mengimpor Bootstrap Icons untuk ikon menyembunyikan/melihat kata sandi -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Mengimpor stylesheet kustom eksternal untuk menghindari inline style -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <!-- Elemen card login utama dengan styling terpusat di style.css -->
                <div class="card card-login p-4 shadow-lg text-start" style="background-color: #121216; border: 1px solid rgba(244, 63, 94, 0.2); border-radius: 12px;">
                    <h2 class="text-center font-bold mb-1 text-white">Skins<span class="text-gradient">Love.gg</span></h2>
                    <p class="text-muted text-center mb-4">Masuk ke akun Anda untuk bertransaksi</p>

                    <?php if (!empty($error)): // Membuka pengecekan jika ada string error login ?>
                        <div class="alert alert-danger text-center py-2" role="alert">
                            <?php echo htmlspecialchars($error); // Mencetak pesan error yang diamankan XSS ?>
                        </div>
                    <?php endif; // Mengakhiri blok percabangan alert error ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label text-muted">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label text-muted">Kata Sandi</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan sandi..." required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: #2d2d39; background-color: #1a1a22; color: #94a3b8;">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 py-2.5 rounded-3 mb-3">Masuk Sekarang</button>
                    </form>
                    
                    <div class="text-center mt-2">
                        <span class="text-muted small">Belum terdaftar?</span>
                        <a href="register.php" class="text-decoration-none small text-gradient fw-bold">Buat Akun Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- File bundle Bootstrap 5 Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- File script kustom eksternal terpisah yang berisi fungsionalitas toggle show/hide password -->
    <script src="script/main.js"></script>
</body>
</html>
