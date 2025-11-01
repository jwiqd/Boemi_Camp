<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    die("Akses dilarang!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['action'])) {
    
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'];
    $whatsapp_url = ''; 
    $pesan_status = '';

    mysqli_begin_transaction($koneksi);

    try {
        // Ambil data pesanan
        $stmt_order = $koneksi->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt_order->bind_param("i", $order_id);
        $stmt_order->execute();
        $order = $stmt_order->get_result()->fetch_assoc();
        
        if (!$order) {
            throw new Exception("Pesanan tidak ditemukan.");
        }

        // Format Nomor HP
        $no_hp_pelanggan = $order['no_hp'];
        if (substr($no_hp_pelanggan, 0, 1) == "0") {
            $no_hp_pelanggan = "62" . substr($no_hp_pelanggan, 1);
        }
        
        $teks_wa = ''; 

        // --- LOGIKA AKSI BARU ---
        if ($action == 'setujui_transfer') {
            $status_baru = "Lunas (Disetujui)";
            $stmt = $koneksi->prepare("UPDATE orders SET status_pesanan = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status_baru, $order_id);
            $stmt->execute();

            $pesan_status = "Pesanan #" . $order_id . " (Transfer) telah disetujui.";
            
            // Buat Teks Struk untuk Transfer Lunas
            $teks_wa = "Halo " . htmlspecialchars($order['nama_pelanggan']) . ",\n\n";
            $teks_wa .= "Kabar baik! Pembayaran untuk pesanan Anda di *Boemi Camp Adventure* telah kami *SETUJUI*.\n\n";
            $teks_wa .= "Status: *Lunas (Disetujui)*\n";
            $teks_wa .= "Total Bayar: *Rp " . number_format($order['total_harga']) . "*\n\n";
            $teks_wa .= "--- *INFO SEWA* ---\n";
            $teks_wa .= "Order ID: *#" . $order['order_id'] . "*\n";
            $teks_wa .= "Tgl Ambil: " . date('d M Y, H:i', strtotime($order['tanggal_ambil'])) . "\n";
            $teks_wa .= "Tgl Kembali: " . date('d M Y, H:i', strtotime($order['tanggal_kembali'])) . "\n\n";
            $teks_wa .= "Terima kasih telah memesan. Harap tunjukkan pesan ini saat pengambilan barang.\n";
            
        } elseif ($action == 'setujui_cod') {
            $status_baru = "COD (Siap Diantar/Diambil)";
            $stmt = $koneksi->prepare("UPDATE orders SET status_pesanan = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status_baru, $order_id);
            $stmt->execute();
            
            $pesan_status = "Pesanan #" . $order_id . " (COD) telah dikonfirmasi.";
            
            // Buat Teks Struk untuk COD
            $teks_wa = "Halo " . htmlspecialchars($order['nama_pelanggan']) . ",\n\n";
            $teks_wa .= "Pesanan *Bayar di Tempat (COD)* Anda di *Boemi Camp Adventure* telah kami *KONFIRMASI*.\n\n";
            $teks_wa .= "Status: *Siap Diantar/Diambil*\n";
            $teks_wa .= "Total Bayar: *Rp " . number_format($order['total_harga']) . "* (Harap siapkan uang pas)\n\n";
            $teks_wa .= "--- *INFO SEWA* ---\n";
            $teks_wa .= "Order ID: *#" . $order['order_id'] . "*\n";
            $teks_wa .= "Tgl Ambil: " . date('d M Y, H:i', strtotime($order['tanggal_ambil'])) . "\n";
            $teks_wa .= "Tgl Kembali: " . date('d M Y, H:i', strtotime($order['tanggal_kembali'])) . "\n\n";
            $teks_wa .= "Tim kami akan menghubungi Anda untuk proses pengantaran/pengambilan.\n";
        
        // --- LOGIKA BARU UNTUK LANGKAH 2 ---
        } elseif ($action == 'tandai_diambil') {
            $status_baru = "Disewa (Diambil)";
            $stmt = $koneksi->prepare("UPDATE orders SET status_pesanan = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status_baru, $order_id);
            $stmt->execute();
            
            $pesan_status = "Pesanan #" . $order_id . " telah ditandai sebagai DI AMBIL.";
            
        } elseif ($action == 'tandai_selesai') {
            $status_baru = "Selesai (Kembali)";
            $stmt = $koneksi->prepare("UPDATE orders SET status_pesanan = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status_baru, $order_id);
            $stmt->execute();
            
            // LOGIKA PENGEMBALIAN STOK
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

            $pesan_status = "Pesanan #" . $order_id . " telah ditandai SELESAI. Stok telah dikembalikan.";

        } elseif ($action == 'batalkan') {
            $status_baru = "Dibatalkan";
            
            // Cek dulu apakah stok perlu dikembalikan
            if ($order['status_pesanan'] != 'Menunggu Pembayaran' && $order['status_pesanan'] != 'Dibatalkan') {
                // Jika pesanan sudah disetujui (stok sudah berkurang), maka kembalikan stok
                $stmt_items = $koneksi->prepare("SELECT product_id, jumlah_sewa FROM order_items WHERE order_id = ?");
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $items_to_restore = $stmt_items->get_result();
                
                $stmt_stock_update = $koneksi->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                while ($item = $items_to_restore->fetch_assoc()) {
                    $stmt_stock_update->bind_param("ii", $item['jumlah_sewa'], $item['product_id']);
                    $stmt_stock_update->execute();
                }
                 $pesan_status = "Pesanan #" . $order_id . " telah dibatalkan. Stok telah dikembalikan.";
            } else {
                 // Jika status masih "Menunggu Pembayaran" (stok belum berkurang), cukup batalkan saja
                 $pesan_status = "Pesanan #" . $order_id . " telah dibatalkan.";
            }

            // Ubah status pesanan di tabel 'orders'
            $stmt_order = $koneksi->prepare("UPDATE orders SET status_pesanan = ? WHERE order_id = ?");
            $stmt_order->bind_param("si", $status_baru, $order_id);
            $stmt_order->execute();
            
        } else {
            throw new Exception("Aksi tidak valid.");
        }

        // Commit perubahan ke DB
        mysqli_commit($koneksi);
        
        // Buat Link WhatsApp jika ada teks yang disiapkan
        if (!empty($teks_wa)) {
            $whatsapp_url = "https://wa.me/" . $no_hp_pelanggan . "?text=" . urlencode($teks_wa);
            header("Location: dashboard.php?status=" . urlencode($pesan_status) . "&whatsapp_url=" . urlencode($whatsapp_url));
        } else {
            header("Location: dashboard.php?status=" . urlencode($pesan_status));
        }
        exit;

    } catch (Exception $e) {
        // Jika ada error, batalkan semua perubahan
        mysqli_rollback($koneksi);
        header("Location: dashboard.php?status=" . urlencode("Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>