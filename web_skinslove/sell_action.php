<?php
// Menyertakan file konfigurasi database PDO dan manajemen session login
require_once 'config.php';

// Memeriksa jika user belum masuk akun login, dialihkan ke login.php demi keamanan data
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mendapatkan id user yang sedang berinteraksi lewat session loginnya
$user_id = $_SESSION['user_id'];
// Mengambil jenis aksi (sell) yang dikirim lewat metode POST
$action = $_POST['action'] ?? '';

if ($action === 'sell') {
    $item_id = intval($_POST['item_id'] ?? 0);

    // Memeriksa kepemilikan skin oleh user bersangkutan secara aman
    // User dianggap memiliki skin jika transaksi terbaru dari skin tersebut adalah pembelian sukses oleh user terkait
    $check_stmt = $conn->prepare("
        SELECT i.* 
        FROM items i
        JOIN transactions t ON t.item_id = i.id
        WHERE i.is_available = 0
          AND t.user_id = :user_id 
          AND t.item_id = :item_id 
          AND t.transaction_type = 'buy' 
          AND t.status = 'success' 
          AND t.id = (
              SELECT MAX(t2.id) 
              FROM transactions t2 
              WHERE t2.item_id = i.id
          )
        LIMIT 1
    ");
    $check_stmt->execute(['user_id' => $user_id, 'item_id' => $item_id]);
    $skin = $check_stmt->fetch();

    if ($skin) {
        $sell_price = floatval($skin['price']);

        // Memulai transaksi terisolasi database (ACID)
        $conn->beginTransaction();
        try {
            // 1. Menambahkan saldo akun penjual di database
            $add_balance_stmt = $conn->prepare("UPDATE users SET balance = balance + :price WHERE id = :id");
            $add_balance_stmt->execute(['price' => $sell_price, 'id' => $user_id]);

            // 2. Mengembalikan status skin item game di tabel items menjadi tersedia (is_available = 1)
            $update_item_stmt = $conn->prepare("UPDATE items SET is_available = 1 WHERE id = :id");
            $update_item_stmt->execute(['id' => $item_id]);

            // 3. Mencatat log transaksi penjualan baru ke tabel transactions
            $trans_stmt = $conn->prepare("
                INSERT INTO transactions (user_id, item_id, price_paid, transaction_type, status) 
                VALUES (:user_id, :item_id, :price, 'sell', 'success')
            ");
            $trans_stmt->execute([
                'user_id' => $user_id,
                'item_id' => $item_id,
                'price' => $sell_price
            ]);

            // 4. Menambahkan pemberitahuan/notifikasi bagi user
            $notif_stmt = $conn->prepare("
                INSERT INTO notifications (user_id, title, message) 
                VALUES (:user_id, :title, :message)
            ");
            $notif_stmt->execute([
                'user_id' => $user_id,
                'title' => 'Penjualan Berhasil!',
                'message' => 'Anda telah sukses menjual kembali skin ' . $skin['name'] . ' seharga $' . number_format($sell_price, 2) . ' dan saldo telah ditambahkan.'
            ]);

            // Menyimpan seluruh perubahan data ke database secara permanen
            $conn->commit();

            // Memperbarui nominal saldo session pembeli yang aktif saat ini
            $_SESSION['balance'] += $sell_price;

            // Mengalihkan kembali user ke halaman inventori dengan status sukses_sell
            header('Location: inventory.php?status=success_sell');
            exit;
        } catch (Exception $e) {
            // Membatalkan seluruh pembaruan tabel jika terjadi kegagalan sistem agar database aman
            $conn->rollBack();
            header('Location: inventory.php?status=error_sell');
            exit;
        }
    } else {
        // Gagal jika skin tidak ditemukan atau kepemilikan tidak valid
        header('Location: inventory.php?status=error_sell');
        exit;
    }
}

header('Location: inventory.php');
exit;
