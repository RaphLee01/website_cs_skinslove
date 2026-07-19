<?php
// Menyertakan file konfigurasi koneksi database PDO dan manajemen session login
require_once 'config.php';

// Memeriksa jika user belum masuk akun login, dialihkan ke login.php demi keamanan data
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mendapatkan id user yang sedang berinteraksi lewat session loginnya
$user_id = $_SESSION['user_id'];
// Mengambil jenis aksi (tambah, hapus, checkout) yang dikirim lewat metode POST
$action = $_POST['action'] ?? '';

// Penanganan aksi penambahan skin CS2 ke dalam keranjang belanja
if ($action === 'add') {
    // Mendapatkan id skin item CS2 yang dikirim dari form beranda pasar
    $item_id = intval($_POST['item_id'] ?? 0);
    
    // Memeriksa apakah data skin item ini sudah ada di dalam keranjang belanja user
    $check_stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = :user_id AND item_id = :item_id");
    $check_stmt->execute(['user_id' => $user_id, 'item_id' => $item_id]);
    
    // Jika skin belum pernah ditambahkan, maka lakukan penyimpanan data baru ke tabel database cart
    if ($check_stmt->rowCount() === 0) {
        // Menyiapkan insert query ke tabel database keranjang
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, item_id) VALUES (:user_id, :item_id)");
        $insert_stmt->execute(['user_id' => $user_id, 'item_id' => $item_id]);
    }
    // Mengalihkan user ke halaman keranjang belanja cart.php setelah skin berhasil didaftarkan
    header('Location: cart.php');
    exit;
}

// Penanganan aksi penghapusan skin dari dalam keranjang belanja
if ($action === 'remove') {
    // Mengambil id unik baris keranjang yang ingin dibatalkan pemesanannya
    $cart_id = intval($_POST['cart_id'] ?? 0);
    
    // Mempersiapkan penghapusan baris data terpilih berdasarkan id baris keranjang belanja
    $delete_stmt = $conn->prepare("DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id");
    $delete_stmt->execute(['cart_id' => $cart_id, 'user_id' => $user_id]);
    
    // Kembali ke halaman keranjang belanja setelah sukses melakukan penghapusan item
    header('Location: cart.php');
    exit;
}

// Penanganan aksi transaksi pembayaran (checkout keranjang belanja) secara mutlak
if ($action === 'checkout') {
    // Mengambil seluruh skin CS2 di dalam keranjang yang masih berstatus tersedia di pasar
    $stmt = $conn->prepare("SELECT c.id AS cart_id, i.* FROM cart c JOIN items i ON c.item_id = i.id WHERE c.user_id = :user_id AND i.is_available = 1");
    $stmt->execute(['user_id' => $user_id]);
    $items = $stmt->fetchAll();
    
    // Mengakumulasi total tagihan dari seluruh skin CS2 yang ada di dalam keranjang belanja
    $total_bill = 0.00;
    foreach ($items as $item) {
        $total_bill += floatval($item['price']);
    }
    
    // Memeriksa keselarasan saldo akun saat ini dengan jumlah nominal tagihan checkout
    if ($total_bill > 0 && $_SESSION['balance'] >= $total_bill) {
        // Memulai sistem transaksi database terisolasi untuk memastikan konsistensi pembaruan tabel (ACID)
        $conn->beginTransaction();
        try {
            // Melakukan update pemotongan nominal saldo akun pembeli di dalam database
            $update_balance_stmt = $conn->prepare("UPDATE users SET balance = balance - :bill WHERE id = :id");
            $update_balance_stmt->execute(['bill' => $total_bill, 'id' => $user_id]);
            
            // Melakukan looping transaksi pengalihan status ketersediaan barang di pasar dan pencatatan history
            foreach ($items as $skin) {
                // Mengubah status skin item game di tabel items menjadi tidak tersedia (terjual)
                $sold_stmt = $conn->prepare("UPDATE items SET is_available = 0 WHERE id = :item_id");
                $sold_stmt->execute(['item_id' => $skin['id']]);
                
                // Memasukkan riwayat pembelian ke dalam tabel transaksi log pembelian
                $trans_stmt = $conn->prepare("INSERT INTO transactions (user_id, item_id, price_paid, transaction_type, status) VALUES (:user_id, :item_id, :price, 'buy', 'success')");
                $trans_stmt->execute(['user_id' => $user_id, 'item_id' => $skin['id'], 'price' => $skin['price']]);
                
                // Menambahkan notifikasi baru bagi user secara real-time mengabarkan keberhasilan transaksi
                $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)");
                $notif_stmt->execute([
                    'user_id' => $user_id,
                    'title' => 'Pembelian Berhasil!',
                    'message' => 'Anda telah sukses membeli skin ' . $skin['name'] . ' seharga $' . number_format($skin['price'], 2) . '.'
                ]);
            }
            
            // Mengosongkan kembali seluruh daftar item di keranjang belanja user karena sudah lunas dibayar
            $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $clear_cart_stmt->execute(['user_id' => $user_id]);
            
            // Menyimpan seluruh perubahan tabel secara permanen ke dalam database
            $conn->commit();
            
            // Melakukan pembaruan nilai saldo yang disimpan di session PHP global
            $_SESSION['balance'] -= $total_bill;
            
            // Mengalihkan user ke halaman inventori miliknya untuk meninjau skin baru yang dibeli
            header('Location: inventory.php');
            exit;
        } catch (Exception $e) {
            // Membatalkan seluruh pembaruan tabel jika terjadi kegagalan sistem agar database aman
            $conn->rollBack();
            // Memberikan instruksi redirect balik ke keranjang belanja dengan informasi error
            header('Location: cart.php?error=checkout_failed');
            exit;
        }
    } else {
        // Mengarahkan kembali ke keranjang belanja jika saldo pembeli tidak mencukupi tagihan
        header('Location: cart.php?error=insufficient_balance');
        exit;
    }
}
// Pengalihan default jika tidak ada aksi yang terdeteksi
header('Location: index.php');
