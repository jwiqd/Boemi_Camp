<?php 
include 'header.php';
include 'koneksi.php'; 

if (empty($_SESSION['keranjang'])) {
    header('Location: keranjang.php');
    exit;
}
?>

<style>
    .checkout-container { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 100px; min-height: 400px; }
    .admin-header h1 { font-size: 1.8rem; color: #1a3b25; margin: 0; }
    .checkout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; }
    
    .form-box div { margin-bottom: 15px; }
    .form-box label { display: block; margin-bottom: 5px; font-weight: 600; }
    .form-box input[type="text"],
    .form-box input[type="tel"],
    .form-box input[type="datetime-local"],
    .form-box textarea,
    .form-box select { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Poppins', sans-serif; }
    .form-box textarea { height: 80px; }
    
    .order-summary { background: #f8fdf9; padding: 20px; border-radius: 8px; border: 1px solid #eee; }
    .order-summary h3 { margin-top: 0; color: #1a3b25; }
    .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .summary-total { font-weight: 600; font-size: 1.2rem; border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px; }
    
    .eula-box { height: 150px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; background: #f9f9f9; margin-top: 10px; }
    .eula-box p { font-size: 0.9rem; }
    .eula-accept { margin-top: 10px; }
    
    .btn-checkout { text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: 600; transition: 0.3s; background: #01442a; color: white; display: inline-block; margin-top: 10px; border: none; cursor: pointer; width: 100%; font-size: 1rem; }
    .btn-checkout:hover { background: #017547; }
    .error-msg { color: #D8000C; background: #FFD2D2; border: 1px solid #D8000C; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }

    /* Style untuk Pilihan Pembayaran */
    .payment-option { border: 1px solid #ccc; border-radius: 5px; padding: 15px; margin-bottom: 10px; cursor: pointer; }
    .payment-option:has(input[type="radio"]:checked) { border-color: #01442a; background: #f8fdf9; }
    .payment-option input[type="radio"] { margin-right: 10px; }
    .payment-option label { font-weight: 600; cursor: pointer; }
    .payment-option p { font-size: 0.9rem; color: #555; margin: 5px 0 0 25px; }

</style>

<div class="checkout-container">
    <div class="admin-header">
        <h1>Checkout Peminjaman</h1>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="form-box">
            <form action="proses_pesanan.php" method="POST">
                
                <h3>1. Form Peminjaman</h3>
                <div>
                    <label for="nama_pelanggan">Nama Lengkap:</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>
                </div>
                <div>
                    <label for="no_hp">No. WhatsApp (Aktif):</label>
                    <input type="tel" id="no_hp" name="no_hp" required>
                </div>
                <div>
                    <label for="alamat">Alamat Lengkap:</label>
                    <textarea id="alamat" name="alamat"></textarea>
                </div>

                <h3>2. Tanggal & Waktu</h3>
                <div>
                    <label for="tanggal_ambil">Pengambilan Barang:</label>
                    <input type="datetime-local" id="tanggal_ambil" name="tanggal_ambil" required>
                </div>
                <div>
                    <label for="tanggal_kembali">Pengembalian Barang:</label>
                    <input type="datetime-local" id="tanggal_kembali" name="tanggal_kembali" required>
                </div>
                
                <h3>3. Metode Pengantaran</h3>
                <div>
                    <select id="metode_pengantaran" name="metode_pengantaran">
                        <option value="Dianter - Gratis">Dianter - Gratis (Sesuai Poin 3)</option>
                        <option value="Ambil Sendiri">Ambil Sendiri di Lokasi</option>
                    </select>
                </div>
                
                <h3>4. Metode Pembayaran</h3>
                <div class="payment-option">
                    <input type="radio" id="pay_transfer" name="metode_pembayaran" value="Transfer" required>
                    <label for="pay_transfer">Transfer Bank (Manual)</label>
                    <p>Anda harus mengupload bukti transfer setelah ini.</p>
                </div>
                <div class="payment-option">
                    <input type="radio" id="pay_cod" name="metode_pembayaran" value="COD" required>
                    <label for="pay_cod">Bayar di Tempat (COD)</label>
                    <p>Bayar tunai saat barang diantar atau diambil.</p>
                </div>
                <h3>5. EULA & Terms and Condition</h3>
                <div class="eula-box">
                    <p><strong>SYARAT DAN KETENTUAN:</strong></p>
                    <p>1. Peminjam wajib menjaga barang sewaan dengan baik.</p>
                    <p>2. Kerusakan atau kehilangan barang menjadi tanggung jawab penuh peminjam dan wajib mengganti sesuai nilai barang.</p>
                    <p>3. Keterlambatan pengembalian akan dikenakan denda harian sesuai harga sewa barang.</p>
                    <p>4. (Silakan tambahkan syarat & ketentuan lain...)</p>
                </div>
                <div class="eula-accept">
                    <input type="checkbox" id="eula_accept" name="eula_accept" required>
                    <label for="eula_accept">Saya telah membaca dan menyetujui Syarat & Ketentuan yang berlaku.</label>
                </div>

                <hr style="margin-top: 20px;">
                <button type="submit" class="btn-checkout">Konfirmasi Pesanan</button>
            </form>
        </div>

        <div class="order-summary">
            <h3>Ringkasan Pesanan Anda</h3>
            <?php
            $total_harian = 0;
            foreach ($_SESSION['keranjang'] as $id => $item):
                $subtotal_harian = $item['price'] * $item['quantity'];
                $total_harian += $subtotal_harian;
            ?>
            <div class="summary-item">
                <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                <strong>Rp <?php echo number_format($subtotal_harian); ?></strong>
            </div>
            <?php endforeach; ?>

            <div class="summary-total">
                <span>Total Biaya (per hari)</span>
                <strong>Rp <?php echo number_format($total_harian); ?></strong>
            </div>
            <p style="font-size: 0.9rem; color: #555; text-align: right;">
                *Total harga final akan dihitung berdasarkan durasi sewa Anda.
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>