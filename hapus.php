<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    die("Akses dilarang!");
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}
$id_product = $_GET['id']; // Ambil ID dari URL

// --- PENYESUAIAN ---
// 1. Ambil nama file gambar DULU dari tabel 'products'
$stmt_select = $koneksi->prepare("SELECT image_url FROM products WHERE id = ?");
$stmt_select->bind_param("i", $id_product);
$stmt_select->execute();
$result_select = $stmt_select->get_result();

if ($result_select->num_rows === 1) {
    $data = $result_select->fetch_assoc();
    $nama_file_gambar = $data['image_url'];

    // 2. HAPUS DATA DARI DATABASE (tabel 'products')
    $stmt_delete = $koneksi->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param("i", $id_product);
    
    if ($stmt_delete->execute()) {
        
        // 3. JIKA DATA DB BERHASIL DIHAPUS, HAPUS FILE GAMBAR DARI FOLDER
        $path_file = "images/" . $nama_file_gambar;
        if (file_exists($path_file)) {
            unlink($path_file); // Hapus file
        }
        
        header("Location: dashboard.php?status=Produk berhasil dihapus!");
        exit;
        
    } else {
        header("Location: dashboard.php?status=Gagal menghapus produk.");
        exit;
    }
} else {
    header("Location: dashboard.php?status=Produk tidak ditemukan.");
    exit;
}
?>