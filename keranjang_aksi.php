<?php
session_start();
include 'koneksi.php'; // Kita butuh koneksi sekarang

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// ------------------------------------
// LOGIKA TAMBAH BARANG (Katalog & Paket)
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    
    // Cek apakah product_id dikirim (dari produk biasa ATAU paket)
    if (isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];

        if ($product_id > 0) {
            // Cek apakah produk sudah ada di keranjang
            if (isset($_SESSION['keranjang'][$product_id])) {
                // Jika sudah ada, tambah jumlahnya
                $_SESSION['keranjang'][$product_id]['quantity']++;
            } else {
                // Jika barang baru, ambil datanya dari DB
                $stmt = $koneksi->prepare("SELECT name, price, image_url, stock FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $product = $result->fetch_assoc();
                    
                    // Cek stok sebelum menambah
                    if ($product['stock'] > 0) {
                        $_SESSION['keranjang'][$product_id] = [
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image_url' => $product['image_url'],
                            'quantity' => 1 // Tambah 1
                        ];
                    }
                }
            }
        }
    }
    
    header('Location: keranjang.php');
    exit;
}

// ---------------------------------------
// LOGIKA HAPUS BARANG
// ---------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    if (isset($_SESSION['keranjang'][$product_id])) {
        unset($_SESSION['keranjang'][$product_id]);
    }
    header('Location: keranjang.php');
    exit;
}

// ------------------------------------------
// LOGIKA UPDATE JUMLAH
// ------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
    $product_id = $_POST['product_id'];
    $new_quantity = (int)$_POST['quantity'];
    
    if (isset($_SESSION['keranjang'][$product_id]) && $new_quantity > 0) {
        // Cek stok di database sebelum update
        $stmt = $koneksi->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if ($new_quantity <= $product['stock']) {
            $_SESSION['keranjang'][$product_id]['quantity'] = $new_quantity;
        } else {
            // Jika jumlah > stok, set ke stok maksimum
            $_SESSION['keranjang'][$product_id]['quantity'] = $product['stock'];
        }
    }
    
    header('Location: keranjang.php');
    exit;
}

$koneksi->close();
?>