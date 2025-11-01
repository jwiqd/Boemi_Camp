<?php
session_start();
include 'koneksi.php';

// 1. Keamanan dasar
if ($_SERVER["REQUEST_METHOD"] != "POST" || empty($_SESSION['keranjang'])) {
    header('Location: keranjang.php');
    exit;
}

// 2. Keamanan form EULA & Metode Bayar
if (!isset($_POST['eula_accept']) || !isset($_POST['metode_pembayaran'])) {
    $error = !isset($_POST['eula_accept']) ? 'Anda harus menyetujui Syarat & Ketentuan.' : 'Anda harus memilih metode pembayaran.';
    header('Location: checkout.php?error=' . urlencode($error));
    exit;
}

// 3. Ambil semua data dari form
$nama_pelanggan = $_POST['nama_pelanggan'];
$no_hp = $_POST['no_hp'];
$alamat = $_POST['alamat'];
$tanggal_ambil_str = $_POST['tanggal_ambil'];
$tanggal_kembali_str = $_POST['tanggal_kembali'];
$metode_pengantaran = $_POST['metode_pengantaran'];
$metode_pembayaran = $_POST['metode_pembayaran']; // <-- DATA BARU

// 4. Hitung Durasi Sewa dan Total Harga
try {
    $tgl_ambil = new DateTime($tanggal_ambil_str);
    $tgl_kembali = new DateTime($tanggal_kembali_str);

    if ($tgl_kembali <= $tgl_ambil) {
        throw new Exception("Tanggal kembali harus setelah tanggal pengambilan.");
    }
    
    $durasi_interval = $tgl_ambil->diff($tgl_kembali);
    $durasi_hari = $durasi_interval->days;
    if ($durasi_interval->h > 0 || $durasi_interval->i > 0 || $durasi_interval->s > 0) {
        $durasi_hari++;
    }
    if ($durasi_hari == 0) $durasi_hari = 1;

    $total_harian = 0;
    foreach ($_SESSION['keranjang'] as $item) {
        $total_harian += $item['price'] * $item['quantity'];
    }
    $total_harga_final = $total_harian * $durasi_hari;

} catch (Exception $e) {
    header('Location: checkout.php?error=' . urlencode($e->getMessage()));
    exit;
}

// --- PERUBAHAN LOGIKA STATUS PESANAN ---
$status_awal = '';
if ($metode_pembayaran == 'Transfer') {
    $status_awal = 'Menunggu Pembayaran';
} elseif ($metode_pembayaran == 'COD') {
    $status_awal = 'COD (Menunggu Konfirmasi)';
}
// --- AKHIR PERUBAHAN ---


// 5. PROSES DATABASE (TRANSAKSI)
mysqli_begin_transaction($koneksi);

try {
    // 5a. Cek Stok Terakhir
    foreach ($_SESSION['keranjang'] as $id => $item) {
        $stmt_stock = $koneksi->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");
        $stmt_stock->bind_param('i', $id);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result()->fetch_assoc();
        
        if ($item['quantity'] > $result_stock['stock']) {
            throw new Exception("Stok untuk " . htmlspecialchars($result_stock['name']) . " tidak mencukupi! Sisa stok: " . $result_stock['stock']);
        }
    }

    // 5b. Masukkan ke tabel 'orders'
    // --- PERUBAHAN QUERY: Tambahkan metode_pembayaran dan status_pesanan ---
    $stmt_order = $koneksi->prepare("INSERT INTO orders (nama_pelanggan, no_hp, alamat, tanggal_ambil, tanggal_kembali, metode_pengantaran, metode_pembayaran, total_harga, status_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_order->bind_param("sssssssds", $nama_pelanggan, $no_hp, $alamat, $tanggal_ambil_str, $tanggal_kembali_str, $metode_pengantaran, $metode_pembayaran, $total_harga_final, $status_awal);
    $stmt_order->execute();
    
    $order_id = mysqli_insert_id($koneksi); // Ambil ID pesanan baru

    // 5c. Masukkan 'order_items' dan kurangi stok di 'products'
    $stmt_item = $koneksi->prepare("INSERT INTO order_items (order_id, product_id, jumlah_sewa, harga_saat_sewa) VALUES (?, ?, ?, ?)");
    $stmt_stock_update = $koneksi->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($_SESSION['keranjang'] as $id => $item) {
        $stmt_item->bind_param("iiid", $order_id, $id, $item['quantity'], $item['price']);
        $stmt_item->execute();
        
        $stmt_stock_update->bind_param("ii", $item['quantity'], $id);
        $stmt_stock_update->execute();
    }

    // 5d. Commit
    mysqli_commit($koneksi);

    // 6. Kosongkan keranjang
    unset($_SESSION['keranjang']);

    // 7. Arahkan ke halaman struk
    header('Location: struk.php?order_id=' . $order_id);
    exit;

} catch (Exception $e) {
    // 5e. Rollback
    mysqli_rollback($koneksi);
    header('Location: checkout.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>