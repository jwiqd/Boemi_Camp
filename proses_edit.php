<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    die("Akses dilarang!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_product = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $gambar_lama = $_POST['image_url_lama'];
    // Ambil group_id. Jika kosong, set ke NULL
    $group_id = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : NULL;
    
    $nama_file_gambar_baru = $gambar_lama; 

    if (isset($_FILES['image_url']) && $_FILES['image_url']['name'] != "") {
        
        $target_dir = __DIR__ . "/images/"; // Simpan di folder images
        $nama_file_unik = time() . '-' . basename($_FILES["image_url"]["name"]);
        $target_file = $target_dir . $nama_file_unik;
        
        if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
            $path_gambar_lama = __DIR__ . "/images/" . $gambar_lama; 
            if (file_exists($path_gambar_lama) && $gambar_lama != "") {
                unlink($path_gambar_lama);
            }
            $nama_file_gambar_baru = $nama_file_unik;
        }
    }

    $stmt = $koneksi->prepare("UPDATE products SET name = ?, price = ?, description = ?, image_url = ?, stock = ?, category = ?, group_id = ? WHERE id = ?");
    $stmt->bind_param("sdssisii", $name, $price, $description, $nama_file_gambar_baru, $stock, $category, $group_id, $id_product); 

    if ($stmt->execute()) {
        header("Location: dashboard.php?status=Produk berhasil di-update!");
        exit;
    } else {
        echo "Gagal mengupdate database: " . $stmt->error;
    }

    $stmt->close();
}
$koneksi->close();
?>