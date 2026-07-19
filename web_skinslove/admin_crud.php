<?php
// Mengimpor file konfigurasi database PDO dan status session web
require_once 'config.php';

// Menolak akses jika sesi login user bukan merupakan level 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Mengambil instruksi aksi (create, edit, delete) yang dikirim administrator via POST
$action = $_POST['action'] ?? '';

// 1. PENANGANAN UNTUK AKSI CREATE (MENAMBAH SKIN BARU)
if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $category = $_POST['category'] ?? 'Rifle';
    $wear = floatval($_POST['wear'] ?? 0.05);
    $price = floatval($_POST['price'] ?? 0.00);
    $collection = trim($_POST['collection'] ?? '');
    // Nilai default jika administrator tidak mengunggah gambar apa pun
    $image_path = 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=300';

    // Memproses unggahan file gambar dari input bertipe file multipart/form-data
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        // Mengatur lokasi folder tujuan pemindahan gambar di server web, misal: uploads/
        $upload_dir = 'uploads/';
        // Membuat folder uploads jika folder tersebut belum dibuat di direktori root server
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        // Mendapatkan ekstensi file dari gambar yang diunggah
        $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        // Membuat nama file baru yang unik menggunakan fungsi time() untuk menghindari duplikasi
        $new_filename = 'skin_' . time() . '.' . $ext;
        // Menyusun lokasi lengkap penempatan file gambar di folder server
        $target_file = $upload_dir . $new_filename;
        
        // Memindahkan file gambar dari direktori sementara server ke direktori tujuan permanen
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
            // Mengubah nilai path gambar agar merujuk ke file lokal yang baru saja berhasil diunggah
            $image_path = $target_file;
        }
    // Jika tidak ada file lokal yang diunggah, periksa apakah administrator menginputkan link URL gambar
    } elseif (!empty($_POST['image_url'])) {
        // Mengubah path gambar merujuk ke alamat link eksternal yang diinputkan
        $image_path = trim($_POST['image_url']);
    }

    // Mempersiapkan query penyisipan data skin baru ke dalam tabel items
    $stmt = $conn->prepare("INSERT INTO items (name, category, wear, price, collection, image, is_available) VALUES (:name, :category, :wear, :price, :collection, :image, 1)");
    // Mengeksekusi penyimpanan data skin baru ke database secara aman
    $stmt->execute([
        'name' => $name,
        'category' => $category,
        'wear' => $wear,
        'price' => $price,
        'collection' => $collection,
        'image' => $image_path
    ]);

    // Mengalihkan kembali administrator ke dashboard utama CRUD dengan sukses status
    header('Location: admin_dashboard.php?status=success_add');
    exit;
}

// 2. PENANGANAN UNTUK AKSI EDIT (MEMPERBAHARUI SKIN YANG SUDAH ADA)
if ($action === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = $_POST['category'] ?? 'Rifle';
    $wear = floatval($_POST['wear'] ?? 0.05);
    $price = floatval($_POST['price'] ?? 0.00);
    $collection = trim($_POST['collection'] ?? '');

    // Mengambil informasi gambar lama dari database sebagai data cadangan jika gambar tidak diganti
    $old_stmt = $conn->prepare("SELECT image FROM items WHERE id = :id");
    $old_stmt->execute(['id' => $id]);
    $old_skin = $old_stmt->fetch();
    // Menjadikan gambar lama sebagai default
    $image_path = $old_skin ? $old_skin['image'] : '';

    // Memproses unggahan file gambar baru jika administrator mengubah gambar skin secara lokal
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $new_filename = 'skin_' . time() . '.' . $ext;
        $target_file = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    // Jika tidak mengunggah file baru, periksa apakah link URL gambar baru diisikan
    } elseif (!empty($_POST['image_url'])) {
        $image_path = trim($_POST['image_url']);
    }

    // Mempersiapkan query SQL pembaruan kolom data skin terpilih berdasarkan id
    $stmt = $conn->prepare("UPDATE items SET name = :name, category = :category, wear = :wear, price = :price, collection = :collection, image = :image WHERE id = :id");
    // Mengeksekusi proses update data skin di database
    $stmt->execute([
        'name' => $name,
        'category' => $category,
        'wear' => $wear,
        'price' => $price,
        'collection' => $collection,
        'image' => $image_path,
        'id' => $id
    ]);

    // Mengalihkan halaman kembali ke dashboard administrator setelah sukses mengedit data
    header('Location: admin_dashboard.php?status=success_edit');
    exit;
}

// 3. PENANGANAN UNTUK AKSI DELETE (MENGHAPUS SKIN DARI DATABASE)
if ($action === 'delete') {
    // Mengambil id skin yang ingin dihapus dari parameter input form
    $id = intval($_POST['id'] ?? 0);

    // Memeriksa apakah item tersebut sudah terjual / dibeli (is_available = 0 atau ada di transaksi)
    $check_stmt = $conn->prepare("SELECT is_available FROM items WHERE id = :id");
    $check_stmt->execute(['id' => $id]);
    $item = $check_stmt->fetch();

    if ($item && intval($item['is_available']) === 0) {
        // Jika skin sudah terjual, batalkan penghapusan demi menjaga integritas data inventori user!
        header('Location: admin_dashboard.php?status=error_delete_sold');
        exit;
    }

    // Mempersiapkan perintah query SQL penghapusan baris data dari tabel items
    $stmt = $conn->prepare("DELETE FROM items WHERE id = :id");
    // Mengeksekusi penghapusan baris data di database
    $stmt->execute(['id' => $id]);

    // Mengalihkan kembali administrator ke menu utama dashboard CRUD setelah sukses menghapus
    header('Location: admin_dashboard.php?status=success_delete');
    exit;
}

// Mengalihkan kembali jika tidak ada aksi yang cocok untuk keamanan file
header('Location: admin_dashboard.php');
