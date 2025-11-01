<?php
session_start();
include 'koneksi.php';

// Keamanan: Cek jika data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['order_id'])) {
    header('Location: lacak_pesanan.php');
    exit;
}

$order_id = (int)$_POST['order_id'];

// Keamanan: Cek apakah pelanggan ini berhak membatalkan pesanan ini
// (Dia harus sudah berhasil melacak pesanan ini di session)
if (!isset($_SESSION['user_access_order_id']) || $_SESSION['user_access_order_id'] != $order_id) {
    die("Akses ditolak. Silakan lacak pesanan Anda kembali.");
}

// Mulai Transaksi
mysqli_begin_transaction($koneksi);

try {
    // Ambil status pesanan saat ini
    $stmt_order = $koneksi->prepare("SELECT status_pesanan FROM orders WHERE order_id = ?");
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $order = $stmt_order->get_result()->fetch_assoc();

    if (!$order) {
        throw new Exception("Pesanan tidak ditemukan.");
    }

    // Cek apakah pesanan boleh dibatalkan
    if ($order['status_pesanan'] == 'Menunggu Pembayaran' || $order['status_pesanan'] == 'COD (Menunggu Konfirmasi)') {
        
        $status_baru = "Dibatalkan";
        
        // --- LOGIKA PENGEMBALIAN STOK ---
        // Stok hanya dikembalikan jika statusnya BUKAN "Menunggu Pembayaran"
        // (Karena saat "Menunggu Pembayaran", stok memang belum dikurangi)
        if ($order['status_pesanan'] == 'COD (Menunggu Konfirmasi)') {
            
            // Ambil semua barang dari pesanan ini
            $stmt_items = $koneksi->prepare("SELECT product_id, jumlah_sewa FROM order_items WHERE order_id = ?");
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $items_to_restore = $stmt_items->get_result();
            
            // Kembalikan stok barang
            $stmt_stock_update = $koneksi->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            while ($item = $items_to_restore->fetch_assoc()) {
                $stmt_stock_update->bind_param("ii", $item['jumlah_sewa'], $item['product_id']);
                $stmt_stock_update->execute();
            }
        }
        
        // --- BATALKAN PESANAN ---
        // Ubah status pesanan di tabel 'orders'
        $stmt_cancel = $koneksi->prepare("UPDATE orders SET status_pesanan = ? WHERE order_id = ?");
        $stmt_cancel->bind_param("si", $status_baru, $order_id);
        $stmt_cancel->execute();

        // Commit perubahan
        mysqli_commit($koneksi);
        
        // Arahkan kembali ke struk dengan pesan sukses
        header("Location: struk.php?order_id=" . $order_id . "&status=batal_sukses");
        exit;
        
    } else {
        // Jika statusnya sudah "Lunas" atau "Diambil", tidak bisa dibatalkan
        throw new Exception("Pesanan ini sudah tidak dapat dibatalkan.");
    }

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($koneksi);
    header("Location: struk.php?order_id=" . $order_id . "&error=" . urlencode($e->getMessage()));
    exit;
}
?>