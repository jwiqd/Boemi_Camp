<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header('Location: login.php');
    exit;
}
include 'koneksi.php';

$query_produk = "SELECT * FROM products ORDER BY id DESC";
$result_produk = mysqli_query($koneksi, $query_produk);
$query_orders = "SELECT * FROM orders ORDER BY order_created_at DESC";
$result_orders = mysqli_query($koneksi, $query_orders);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
        body { background: #f8fdf9; font-family: 'Poppins', sans-serif; }
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .admin-header h1 { font-size: 1.8rem; color: #1a3b25; margin: 0; }
        .admin-header a { text-decoration: none; padding: 10px 18px; border-radius: 5px; font-weight: 600; transition: 0.3s; }
        .btn-tambah { background: #00cc7a; color: white; }
        .btn-tambah:hover { background: #009e5b; }
        .btn-logout { background: #d9534f; color: white; margin-left: 10px; }
        .btn-logout:hover { background: #c9302c; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
        th { background: #f5f5f5; color: #333; }
        td img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; }
        
        .action-links a, .action-links button { margin: 5px; text-decoration: none; font-weight: 600; font-size: 0.9rem; padding: 5px 8px; border-radius: 4px; cursor: pointer; border: none; display: inline-block; }
        .link-edit { color: #f0ad4e; }
        .link-hapus { color: #d9534f; }
        .link-setuju { color: white; background-color: #00cc7a; } /* Hijau */
        .link-batal { color: white; background-color: #d9534f; } /* Merah */
        .link-lihat { color: white; background-color: #0275d8; } /* Biru */
        .link-diambil { color: white; background-color: #5bc0de; } /* Biru Muda */
        .link-selesai { color: white; background-color: #333; } /* Hitam */
        
        .status-msg { color: #008000; background: #e6ffed; padding: 15px; border: 1px solid #008000; border-radius: 5px; margin-top: 20px; }
        
        .order-section { margin-top: 40px; border-top: 3px solid #1a3b25; padding-top: 20px; }
        .bukti-link { font-weight: 600; color: #0275d8; }
        .status-menunggu { font-weight: bold; color: #f0ad4e; } /* Kuning */
        .status-disewa { font-weight: bold; color: #5bc0de; } /* Biru Muda */
        .status-sukses { font-weight: bold; color: green; } /* Hijau */
        .status-batal { font-weight: bold; color: #d9534f; } /* Merah */
        .status-cod { font-weight: bold; color: #0275d8; } /* Biru Tua */
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Halo, <?php echo htmlspecialchars($_SESSION['admin_fullname']); ?>!</h1>
            <div>
                <a href="tambah.php" class="btn-tambah">+ Tambah Produk</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <p class="status-msg"><?php echo htmlspecialchars($_GET['status']); ?></p>
        <?php endif; ?>

        <h2>Daftar Produk</h2>
        <table>
            <thead> <tr> <th>Gambar</th> <th>Nama Produk</th> <th>Harga Sewa (/hari)</th> <th>Stok</th> <th>Aksi</th> </tr> </thead>
            <tbody>
                <?php while ($produk = mysqli_fetch_assoc($result_produk)): ?>
                <tr>
                    <td><img src="images/<?php echo htmlspecialchars($produk['image_url']); ?>" alt="<?php echo htmlspecialchars($produk['name']); ?>"></td>
                    <td><?php echo htmlspecialchars($produk['name']); ?></td>
                    <td>Rp <?php echo number_format($produk['price']); ?></td>
                    <td><?php echo htmlspecialchars($produk['stock']); ?></td> 
                    <td class="action-links">
                        <a href="edit.php?id=<?php echo $produk['id']; ?>" class="link-edit">Edit</a>
                        <a href="hapus.php?id=<?php echo $produk['id']; ?>" class="link-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="order-section">
            <h2>Daftar Pesanan (Orders)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($result_orders)): ?>
                    <tr>
                        <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($order['nama_pelanggan']); ?><br><small><?php echo htmlspecialchars($order['no_hp']); ?></small></td>
                        <td>Rp <?php echo number_format($order['total_harga']); ?></td>
                        <td><strong><?php echo htmlspecialchars($order['metode_pembayaran']); ?></strong></td>
                        <td>
                            <?php 
                                $status = $order['status_pesanan'];
                                $class = '';
                                if ($status == 'Menunggu Pembayaran' || $status == 'Menunggu Konfirmasi') {
                                    $class = 'status-menunggu';
                                } elseif ($status == 'Lunas (Disetujui)' || $status == 'Selesai (Kembali)') {
                                    $class = 'status-sukses';
                                } elseif ($status == 'Dibatalkan' || $status == 'Ditolak') {
                                    $class = 'status-batal';
                                } elseif ($status == 'COD (Siap Diantar/Diambil)') {
                                    $class = 'status-cod';
                                } elseif ($status == 'Disewa (Diambil)') {
                                    $class = 'status-disewa';
                                }
                                echo "<span class='$class'>$status</span>";
                            ?>
                        </td>
                        <td class="action-links">
                            <a href="struk.php?order_id=<?php echo $order['order_id']; ?>" target="_blank" class="link-lihat">Detail</a>
                            <?php if (!empty($order['bukti_pembayaran'])): ?>
                                <a href="images/uploads/bukti_bayar/<?php echo htmlspecialchars($order['bukti_pembayaran']); ?>" target="_blank" class="bukti-link">Lihat Bukti</a>
                            <?php endif; ?>

                            <?php if ($order['status_pesanan'] == 'Menunggu Konfirmasi'): // Ini untuk Transfer ?>
                                <form action="proses_order_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="action" value="setujui_transfer" class="link-setuju">Setujui Bayar</button>
                                </form>
                            <?php elseif ($order['status_pesanan'] == 'COD (Menunggu Konfirmasi)'): // Ini untuk COD ?>
                                <form action="proses_order_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="action" value="setujui_cod" class="link-setuju">Siapkan Barang</button>
                                </form>
                            
                            <?php elseif ($order['status_pesanan'] == 'Lunas (Disetujui)' || $order['status_pesanan'] == 'COD (Siap Diantar/Diambil)'): ?>
                                <form action="proses_order_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="action" value="tandai_diambil" class="link-diambil">Tandai Diambil</button>
                                </form>
                            
                            <?php elseif ($order['status_pesanan'] == 'Disewa (Diambil)'): ?>
                                <form action="proses_order_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="action" value="tandai_selesai" class="link-selesai">Tandai Selesai (Kembali)</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($order['status_pesanan'] != 'Selesai (Kembali)' && $order['status_pesanan'] != 'Dibatalkan'): ?>
                                <form action="proses_order_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="action" value="batalkan" class="link-batal" onclick="return confirm('Yakin batalkan pesanan ini? Stok akan dikembalikan (jika perlu).');">Batalkan</button>
                                </form>
                            <?php endif; ?>
                            
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const wa_url = urlParams.get('whatsapp_url');
            if (wa_url) {
                window.open(decodeURIComponent(wa_url), '_blank');
                window.history.replaceState(null, '', window.location.pathname + '?status=' + urlParams.get('status'));
            }
        })();
    </script>
</body>
</html>