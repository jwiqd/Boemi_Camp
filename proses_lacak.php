<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $order_id = (int)$_POST['order_id'];
    $no_hp = $_POST['no_hp'];

    // 1. Cari pesanan di database
    $stmt = $koneksi->prepare("SELECT * FROM orders WHERE order_id = ? AND no_hp = ?");
    $stmt->bind_param("is", $order_id, $no_hp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // --- BERHASIL DITEMUKAN ---
        $order = $result->fetch_assoc();
        
        // 2. Simpan "izin" di session
        // Ini untuk keamanan, agar tombol "Batal" di struk.php tahu
        // bahwa ini adalah pelanggan yang sah, bukan orang lain.
        $_SESSION['user_access_order_id'] = $order['order_id'];
        
        // 3. Arahkan ke halaman struk
        header("Location: struk.php?order_id=" . $order_id);
        exit;
        
    } else {
        // --- GAGAL DITEMUKAN ---
        // Arahkan kembali ke halaman lacak dengan pesan error
        header("Location: lacak_pesanan.php?error=" . urlencode("Order ID atau No. HP salah."));
        exit;
    }
} else {
    header("Location: lacak_pesanan.php");
    exit;
}
?>