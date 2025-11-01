<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    die("Akses dilarang!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    // Ambil group_id. Jika kosong, set ke NULL
    $group_id = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : NULL;

    // Proses upload gambar
    $target_dir = __DIR__ . "/images/"; // Path ke folder 'images'
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $nama_file_unik = time() . '-' . basename($_FILES["image_url"]["name"]);
    $target_file = $target_dir . $nama_file_unik;

    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        die("Maaf, hanya file JPG, JPEG, & PNG yang diperbolehkan.");
    }

    if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
        
        $stmt = $koneksi->prepare("INSERT INTO products (name, price, description, image_url, stock, category, group_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssisi", $name, $price, $description, $nama_file_unik, $stock, $category, $group_id); 

        if ($stmt->execute()) {
            header("Location: dashboard.php?status=Produk baru berhasil ditambahkan!");
            exit;
        } else {
            echo "Gagal menyimpan data ke database: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Maaf, terjadi error saat mengupload file gambar.";
    }
}
$koneksi->close();
?>