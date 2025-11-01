<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $order_id = (int)$_POST['order_id'];

    // 1. Proses upload file
    // Pastikan folder 'uploads/bukti_bayar/' sudah ada
    //$target_dir = "uploads/bukti_bayar/"; 
    // MENJADI BARIS INI:
    $target_dir = __DIR__ . '/images/uploads/bukti_bayar/';
    
    // Buat nama file unik
    $nama_file_unik = $order_id . '-' . time() . '-' . basename($_FILES["bukti_pembayaran"]["name"]);
    $target_file = $target_dir . $nama_file_unik;
    
    // Cek tipe file
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        die("Maaf, hanya file JPG, JPEG, & PNG yang diperbolehkan.");
    }

    // Pindahkan file yang diupload
    if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
        
        // 2. Jika upload berhasil, update database
        $status_baru = "Menunggu Konfirmasi"; // Status berubah, admin perlu cek
        
        $stmt = $koneksi->prepare("UPDATE orders SET bukti_pembayaran = ?, status_pesanan = ? WHERE order_id = ?");
        $stmt->bind_param("ssi", $nama_file_unik, $status_baru, $order_id);

        if ($stmt->execute()) {
            // Jika berhasil, kembali ke halaman konfirmasi dengan pesan sukses
            header("Location: konfirmasi_pembayaran.php?status=sukses");
            exit;
        } else {
            echo "Gagal menyimpan data ke database: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Maaf, terjadi error saat mengupload file bukti pembayaran.";
    }
}
$koneksi->close();
?>