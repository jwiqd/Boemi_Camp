<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header('Location: login.php');
    exit;
}
include 'koneksi.php'; // Butuh koneksi untuk ambil data grup
$result_grup = mysqli_query($koneksi, "SELECT * FROM paket_grup ORDER BY group_name ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <style>
        body { font-family: 'Poppins', sans-serif; display: grid; place-items: center; min-height: 90vh; background: #f4f4f4; }
        .form-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        .form-box h2 { text-align: center; margin-top: 0; margin-bottom: 20px; color: #1a3b25; }
        .form-box div { margin-bottom: 15px; }
        .form-box label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-box input[type="text"], .form-box input[type="number"], .form-box textarea, .form-box select { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-box button { width: 100%; padding: 12px; background: #00cc7a; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #555; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Tambah Produk Baru</h2>
        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="price">Harga Sewa (Rp):</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div>
                <label for="stock">Stok Barang:</label>
                <input type="number" id="stock" name="stock" min="0" value="0" required>
            </div>
            <div>
                <label for="description">Deskripsi (Opsional):</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            <div>
                <label for="category">Kategori (Opsional):</label>
                <input type="text" id="category" name="category" placeholder="Contoh: Tenda">
            </div>
            
            <div>
                <label for="group_id">Grup Paket (Kosongkan jika bukan paket):</label>
                <select id="group_id" name="group_id">
                    <option value="">-- Produk Biasa --</option>
                    <?php while($grup = mysqli_fetch_assoc($result_grup)): ?>
                        <option value="<?php echo $grup['group_id']; ?>">
                            <?php echo htmlspecialchars($grup['group_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="image_url">Gambar Produk:</label>
                <input type="file" id="image_url" name="image_url" accept="image/*" required>
            </div>
            <button type="submit">Tambah Produk</button>
        </form>
        <a href="dashboard.php" class="back-link">Kembali ke Dashboard</a>
    </div>
</body>
</html>