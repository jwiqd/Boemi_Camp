<?php include 'header.php'; ?>

<style>
    body { font-family: 'Poppins', sans-serif; display: grid; place-items: center; min-height: 90vh; background: #f4f4f4; padding-top: 100px; }
    .form-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
    .form-box h2 { text-align: center; margin-top: 0; margin-bottom: 20px; color: #1a3b25; }
    .form-box div { margin-bottom: 15px; }
    .form-box label { display: block; margin-bottom: 5px; font-weight: 600; }
    .form-box input { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .form-box button { width: 100%; padding: 12px; background: #00cc7a; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; }
    .back-link { display: block; text-align: center; margin-top: 15px; color: #555; text-decoration: none; }
    .success-msg { color: #008000; background: #e6ffed; padding: 15px; border: 1px solid #008000; border-radius: 5px; text-align: center; }
</style>

<div class="form-box">
    <h2>Konfirmasi Pembayaran</h2>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <p class="success-msg">
            Terima kasih! Bukti pembayaran Anda telah terkirim. Admin akan segera memverifikasi pesanan Anda.
        </p>
    <?php else: ?>
    
        <form action="proses_konfirmasi.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="order_id">Order ID:</label>
                <input type="text" id="order_id" name="order_id" value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : ''; ?>" required>
            </div>
            <div>
                <label for="bukti_pembayaran">Upload Bukti Transfer (JPG/PNG):</label>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg, image/png" required>
            </div>
            <button type="submit">Kirim Konfirmasi</button>
        </form>
        
    <?php endif; ?>
    
    <a href="index.php" class="back-link">Kembali ke Halaman Utama</a>
</div>

<?php include 'footer.php'; ?>