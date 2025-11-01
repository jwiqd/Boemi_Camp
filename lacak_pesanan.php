<?php include 'header.php'; ?>

<style>
    body { font-family: 'Poppins', sans-serif; display: grid; place-items: center; min-height: 90vh; background: #f4f4f4; padding-top: 100px; }
    .form-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
    .form-box h2 { text-align: center; margin-top: 0; margin-bottom: 20px; color: #1a3b25; }
    .form-box div { margin-bottom: 15px; }
    .form-box label { display: block; margin-bottom: 5px; font-weight: 600; }
    .form-box input { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .form-box button { width: 100%; padding: 12px; background: #01442a; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; }
    .back-link { display: block; text-align: center; margin-top: 15px; color: #555; text-decoration: none; }
    .error-msg { color: #D8000C; background: #FFD2D2; border: 1px solid #D8000C; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }
</style>

<div class="form-box">
    <h2>Lacak Pesanan Anda</h2>
    <p style="text-align: center; margin-top: -10px; margin-bottom: 20px; font-size: 0.9rem; color: #555;">
        Masukkan Order ID dan No. HP Anda untuk melihat detail struk.
    </p>

    <?php if(isset($_GET['error'])): ?>
        <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    
    <form action="proses_lacak.php" method="POST">
        <div>
            <label for="order_id">Order ID:</label>
            <input type="text" id="order_id" name="order_id" placeholder="Contoh: 7" required>
        </div>
        <div>
            <label for="no_hp">No. WhatsApp:</label>
            <input type="tel" id="no_hp" name="no_hp" placeholder="Contoh: 08123456789" required>
        </div>
        <button type="submit">Lacak Pesanan</button>
    </form>
    
    <a href="index.php" class="back-link">Kembali ke Halaman Utama</a>
</div>

<?php include 'footer.php'; ?>