<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header('Location: login.php');
    exit;
}
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}
$id_product = $_GET['id'];

// Ambil data grup untuk dropdown
$result_grup = mysqli_query($koneksi, "SELECT * FROM paket_grup ORDER BY group_name ASC");

// Ambil data produk yang mau diedit
$stmt = $koneksi->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id_product);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    header('Location: dashboard.php?status=Produk tidak ditemukan.');
    exit;
}
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <style>
        body { font-family: 'Poppins', sans-serif; display: grid; place-items: center; min-height: 90vh; background: #f4f4f4; }
        .form-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        .form-box h2 { text-align: center; margin-top: 0; margin-bottom: 20px; color: #1a3b25; }
        .form-box div { margin-bottom: 15px; }
        .form-box label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-box input[type="text"], .form-box input[type="number"], .form-box textarea, .form-box select { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-box button { width: 100%; padding: 12px; background: #f0ad4e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #555; text-decoration: none; }
        .current-img { max-width: 100px; border-radius: 5px; margin-top: 5px; display: block; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Edit Produk</h2>
        <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            <input type="hidden" name="image_url_lama" value="<?php echo htmlspecialchars($data['image_url']); ?>">
            
            <div>
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
            </div>
            <div>
                <label for="price">Harga Sewa (Rp):</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $data['price']; ?>" required>
            </div>
            <div>
                <label for="stock">Stok Barang:</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($data['stock']); ?>" required>
            </div>
            <div>
                <label for="description">Deskripsi (Opsional):</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($data['description']); ?></textarea>
            </div>
            <div>
                <label for="category">Kategori (Opsional):</label>
                <input type="text" id="category" name="category" placeholder="Contoh: Tenda" value="<?php echo htmlspecialchars($data['category']); ?>">
            </div>

            <div>
                <label for="group_id">Grup Paket (Kosongkan jika bukan paket):</label>
                <select id="group_id" name="group_id">
                    <option value="">-- Produk Biasa --</option>
                    <?php while($grup = mysqli_fetch_assoc($result_grup)): ?>
                        <option value="<?php echo $grup['group_id']; ?>" 
                            <?php if ($data['group_id'] == $grup['group_id']) echo 'selected'; // Pilih otomatis grup yang tersimpan ?>
                        >
                            <?php echo htmlspecialchars($grup['group_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label>Gambar Saat Ini:</label>
                <img src="images/<?php echo htmlspecialchars($data['image_url']); ?>" class="current-img">
            </div>
            <div>
                <label for="image_url">Ganti Gambar (Kosongkan jika tidak ingin ganti):</label>
                <input type="file" id="image_url" name="image_url" accept="image/*">
            </div>
            <button type="submit">Update Produk</button>
        </form>
        <a href="dashboard.php" class="back-link">Kembali ke Dashboard</a>
    </div>
</body>
</html>