<?php // Membuka tag PHP untuk kumpulan fungsi bantuan (helper functions) global aplikasi

// Function untuk memformat angka harga menjadi format mata uang Dollar yang rapi
function formatHarga($angka) {
    // Menggunakan number_format bawaan PHP untuk menambahkan 2 angka desimal dan tanda pemisah ribuan
    $hasil = number_format($angka, 2);
    // Mengembalikan hasil format dengan tambahan simbol dollar di depan angka
    return '$' . $hasil;
} // Penutup function formatHarga

// Function untuk memeriksa apakah user yang sedang mengakses halaman sudah login atau belum
function cekLogin() {
    // Mengecek apakah variabel session user_id sudah tersimpan sebagai penanda login
    if (isset($_SESSION['user_id'])) {
        // Mengembalikan nilai true jika session user_id ditemukan (artinya sudah login)
        return true;
    } // Menutup pengecekan session
    // Mengembalikan nilai false jika session user_id tidak ditemukan (artinya belum login)
    return false;
} // Penutup function cekLogin

// Function untuk memeriksa apakah user yang login saat ini memiliki role sebagai admin
function cekAdmin() {
    // Memeriksa dua syarat sekaligus: user harus login dan role harus bernilai admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // Mengembalikan true jika kedua syarat admin terpenuhi
        return true;
    } // Menutup pengecekan kondisi role admin
    // Mengembalikan false jika salah satu syarat admin tidak terpenuhi
    return false;
} // Penutup function cekAdmin

// Function procedure untuk menghitung jumlah item yang ada di keranjang belanja milik user tertentu
// Menerima parameter koneksi PDO ($conn) dan id user yang ingin dihitung keranjangnya
function hitungJumlahKeranjang($conn, $user_id) {
    // Jika user_id tidak valid atau kosong, langsung kembalikan nilai nol tanpa query ke database
    if (empty($user_id)) {
        // Mengembalikan nol sebagai default jumlah keranjang kosong
        return 0;
    } // Menutup pengecekan user_id kosong
    // Menyiapkan query PDO untuk menghitung total baris keranjang milik user terkait
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = :user_id");
    // Mengeksekusi query dengan parameter id user yang aman dari SQL Injection
    $stmt->execute(['user_id' => $user_id]);
    // Mengambil hasil hitung kolom tunggal dari database
    $jumlah = $stmt->fetchColumn();
    // Mengembalikan jumlah item keranjang sebagai hasil akhir function
    return $jumlah;
} // Penutup function hitungJumlahKeranjang

// Function untuk membersihkan dan mengamankan input teks dari user sebelum ditampilkan ke halaman (mencegah XSS)
function bersihkanInput($teks) {
    // Menghapus spasi berlebih di awal dan akhir teks menggunakan trim
    $teks = trim($teks);
    // Mengonversi karakter spesial HTML menjadi entitas aman agar tidak dieksekusi sebagai script
    $teks = htmlspecialchars($teks);
    // Mengembalikan teks yang sudah bersih dan aman digunakan
    return $teks;
} // Penutup function bersihkanInput

// Function untuk menerjemahkan kode kategori senjata singkat menjadi label lengkap berbahasa Indonesia
function labelKategori($kategori) {
    // Membuat array asosiatif pemetaan kode kategori ke label yang lebih deskriptif
    $daftar_label = [
        'Knife' => 'Pisau (Knife)',
        'Glove' => 'Sarung Tangan (Glove)',
        'Rifle' => 'Senapan (Rifle)',
        'Pistol' => 'Pistol',
        'SMG' => 'Submachine Gun (SMG)'
    ]; // Penutup array pemetaan label kategori
    // Mengembalikan label yang cocok jika ditemukan di array, atau kategori asli jika tidak ditemukan
    return $daftar_label[$kategori] ?? $kategori;
} // Penutup function labelKategori