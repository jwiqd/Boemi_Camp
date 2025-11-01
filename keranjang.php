<?php include 'header.php'; ?>

<style>
    .admin-container { max-width: 1200px; 
        margin: 30px auto; 
        padding: 20px; 
        background: #fff; 
        border-radius: 8px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
        margin-top: 100px; /* Beri jarak dari header fixed */
        min-height: 400px;
     }
    .admin-header h1 { font-size: 1.8rem; color: #1a3b25; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 25px; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
    th { background: #f5f5f5; color: #333; }
    td img { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; }
    .link-hapus { color: #d9534f; text-decoration: none; font-weight: 600; }
    .quantity-input { width: 50px; padding: 5px; }
    .cart-summary { margin-top: 20px; padding-top: 20px; border-top: 2px solid #eee; text-align: right; }
    .cart-summary h2 { font-size: 1.5rem; color: #1a3b25; }
    .btn-checkout { text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: 600; transition: 0.3s; background: #01442a; color: white; display: inline-block; margin-top: 10px; }
    .btn-checkout:hover { background: #017547; }
    .cart-empty { text-align: center; 
    padding: 50px; 
    min-height: 400px; /* <-- TAMBAHKAN BARIS INI */
    display: grid; /* <-- TAMBAHKAN BARIS INI */
    place-content: center;
    }
</style>

<div class="admin-container">
    <div class="admin-header">
        <h1>Keranjang Sewa Anda</h1>
    </div>

    <?php if (!empty($_SESSION['keranjang'])): ?>
        <form action="keranjang_aksi.php" method="POST">
            <input type="hidden" name="action" value="update_all"> <table>
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Produk</th>
                        <th>Harga / hari</th>
                        <th>Jumlah</th>
                        <th>Subtotal / hari</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $total_harian = 0;
                        foreach ($_SESSION['keranjang'] as $id => $item): 
                            $subtotal_harian = $item['price'] * $item['quantity'];
                            $total_harian += $subtotal_harian;
                    ?>
                    <tr>
                        <td><img src="images/<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>Rp <?php echo number_format($item['price']); ?></td>
                        <td>
                            <form action="keranjang_aksi.php" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                <button type="submit" class="btn-small" style="background: #f0ad4e; padding: 5px 10px;">Update</button>
                            </form>
                        </td>
                        <td>Rp <?php echo number_format($subtotal_harian); ?></td>
                        <td>
                            <a href="keranjang_aksi.php?action=remove&id=<?php echo $id; ?>" class="link-hapus" onclick="return confirm('Hapus item ini dari keranjang?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <div class="cart-summary">
            <h2>Total Biaya Sewa (per hari): Rp <?php echo number_format($total_harian); ?></h2>
            <p>Total biaya akhir akan dihitung berdasarkan durasi sewa.</p>
            <a href="checkout.php" class="btn-checkout">Lanjut ke Checkout</a>
        </div>

    <?php else: ?>
        <div class="cart-empty">
            <h2>Keranjang Anda kosong.</h2>
            <a href="index.php#katalog" class="btn-hero" style="margin-top: 20px;">Mulai Sewa Sekarang</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>