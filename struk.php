<?php 
include 'header.php'; // header.php sudah punya session_start()
include 'koneksi.php'; 

if (!isset($_GET['order_id'])) {
    echo "<div class='cart-page-container cart-empty'><h2>Error: Order ID tidak ditemukan.</h2></div>";
    include 'footer.php';
    exit;
}
$order_id = (int)$_GET['order_id'];

// --- PENGECEKAN KEAMANAN (BARU) ---
// Cek apakah admin sedang login ATAU pelanggan baru saja berhasil melacak
$is_admin = isset($_SESSION['status_login']) && $_SESSION['status_login'] == true;
$is_customer = isset($_SESSION['user_access_order_id']) && $_SESSION['user_access_order_id'] == $order_id;

if (!$is_admin && !$is_customer) {
    // Jika bukan admin DAN bukan pelanggan yang sah, tendang ke halaman lacak
    header('Location: lacak_pesanan.php?error=' . urlencode('Silakan lacak pesanan Anda terlebih dahulu.'));
    exit;
}
// --- AKHIR PENGECEKAN KEAMANAN ---


// Ambil data pesanan
$stmt_order = $koneksi->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) {
    echo "<div class='cart-page-container cart-empty'><h2>Error: Pesanan tidak ditemukan.</h2></div>";
    include 'footer.php';
    exit;
}

// Ambil data barang
$stmt_items = $koneksi->prepare("
    SELECT oi.*, p.name, p.image_url 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

// Hitung durasi
$tgl_ambil = new DateTime($order['tanggal_ambil']);
$tgl_kembali = new DateTime($order['tanggal_kembali']);
$durasi_interval = $tgl_ambil->diff($tgl_kembali);
$durasi_hari = $durasi_interval->days;
if ($durasi_interval->h > 0 || $durasi_interval->i > 0 || $durasi_interval->s > 0) {
    $durasi_hari++;
}
if ($durasi_hari == 0) $durasi_hari = 1;

?>

<style>
    .cart-page-container { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 100px; min-height: 400px; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .admin-header h1 { font-size: 1.8rem; color: #1a3b25; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 25px; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
    th { background: #f5f5f5; color: #333; }
    td img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
    .struk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; }
    .struk-box { background: #f8fdf9; padding: 20px; border-radius: 8px; }
    .struk-box h3 { margin-top: 0; color: #1a3b25; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    .struk-box p { margin: 5px 0; }
    
    .payment-box.transfer { background: #fff8e1; border: 1px solid #ffe57f; }
    .payment-box.cod { background: #e6f7ff; border: 1px solid #b3e0ff; }
    .payment-box h2 { color: #d9534f; margin-top: 0; }
    .payment-box.cod h2 { color: #0275d8; }
    
    .btn-konfirmasi { text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: 600; transition: 0.3s; background: #00cc7a; color: white; display: inline-block; margin-top: 15px; border: none; cursor: pointer; width: 100%; font-size: 1rem; text-align: center; }
    .btn-konfirmasi:hover { background: #009e5b; }
    .btn-print { background: #0275d8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: 600; }
    .btn-png { background: #f0ad4e; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: 600; margin-left: 10px; }
    
    /* Tombol Batal (CANCEL) dari Pelanggan */
    .btn-batal-user { text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: 600; transition: 0.3s; background: #d9534f; color: white; display: inline-block; margin-top: 15px; border: none; cursor: pointer; width: 100%; font-size: 1rem; text-align: center; }
    .btn-batal-user:hover { background: #c9302c; }
    .success-msg { color: #008000; background: #e6ffed; padding: 15px; border: 1px solid #008000; border-radius: 5px; text-align: center; }

    @media print {
        body, header, footer, #lokasi, .admin-header, .btn-print, .btn-png, .btn-batal-user { display: none; }
        .cart-page-container, #struk-area { display: block; margin: 0; padding: 0; box-shadow: none; border: none; }
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<div class="cart-page-container">
    <div class="admin-header">
        <h1>Struk Pesanan</h1>
        <?php if ($is_admin): ?>
        <div>
            <a href="#" onclick="window.print(); return false;" class="btn-print">Cetak (PDF)</a>
            <a href="#" id="download-png" class="btn-png">Download as PNG</a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if(isset($_GET['status']) && $_GET['status'] == 'batal_sukses'): ?>
        <p class="success-msg" style="margin-top: 20px;">Pesanan Anda telah berhasil dibatalkan.</p>
    <?php endif; ?>
    
    <div id="struk-area">
        <h2 style="color: #1a3b25; margin-top: 10px;">Order ID: #<?php echo $order['order_id']; ?></h2>
        <p style="font-size: 1.1rem; color: #1a3b25; margin-top: 10px;">
            Status: <?php echo htmlspecialchars($order['status_pesanan']); ?>
        </p>

        <div class="struk-grid">
            <div class="struk-box">
                <h3>Detail Pelanggan</h3>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['nama_pelanggan']); ?></p>
                <p><strong>No. HP:</strong> <?php echo htmlspecialchars($order['no_hp']); ?></p>
                <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($order['alamat'])); ?></p>
                
                <h3 style="margin-top: 20px;">Info Sewa</h3>
                <p><strong>Tanggal Ambil:</strong> <?php echo $tgl_ambil->format('d M Y, H:i'); ?></p>
                <p><strong>Tanggal Kembali:</strong> <?php echo $tgl_kembali->format('d M Y, H:i'); ?></p>
                <p><strong>Durasi Sewa:</strong> <?php echo $durasi_hari; ?> hari</p>
                <p><strong>Pengantaran:</strong> <?php echo htmlspecialchars($order['metode_pengantaran']); ?></p>
            </div>
            
            <?php if ($order['metode_pembayaran'] == 'Transfer'): ?>
                <div class="struk-box payment-box transfer">
                    <h2>Total Pembayaran: Rp <?php echo number_format($order['total_harga']); ?></h2>
                    <p><strong>Metode:</strong> Transfer Bank (Manual)</p>
                    <hr>
                    <p>Silakan lakukan pembayaran ke rekening berikut:</p>
                    <p><strong>Bank BCA:</strong> 1234567890</p>
                    <p><strong>Atas Nama:</strong> Boemi Camp Adventure</p>
                    <hr>
                    <p>Setelah melakukan pembayaran, harap segera lakukan konfirmasi.</p>
                    
                    <?php if ($order['status_pesanan'] == 'Menunggu Pembayaran'): ?>
                    <a href="konfirmasi_pembayaran.php?order_id=<?php echo $order['order_id']; ?>" class="btn-konfirmasi">
                        Konfirmasi Pembayaran Sekarang
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: // (Jika metodenya 'COD') ?>
                <div class="struk-box payment-box cod">
                    <h2>Total Pembayaran: Rp <?php echo number_format($order['total_harga']); ?></h2>
                    <p><strong>Metode:</strong> Bayar di Tempat (COD)</p>
                    <hr>
                    <p>Silakan siapkan uang tunai pas saat barang diantar atau diambil di lokasi.</p>
                    <p>Pesanan Anda sedang menunggu konfirmasi dari admin kami.</p>
                </div>
            <?php endif; ?>
            
        </div>

        <h3 style="margin-top: 30px; color: #1a3b25;">Barang yang Disewa:</h3>
        <table>
            <thead> <tr> <th>Gambar</th> <th>Produk</th> <th>Harga Satuan / hari</th> <th>Jumlah</th> <th>Subtotal / hari</th> </tr> </thead>
            <tbody>
                <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><img src="images/<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>"></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>Rp <?php echo number_format($item['harga_saat_sewa']); ?></td>
                    <td><?php echo $item['jumlah_sewa']; ?></td>
                    <td>Rp <?php echo number_format($item['harga_saat_sewa'] * $item['jumlah_sewa']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <?php 
        // Tombol Batal hanya muncul jika:
        // 1. Ini adalah pelanggan (bukan admin)
        // 2. Statusnya masih "Menunggu Pembayaran" ATAU "COD (Menunggu Konfirmasi)"
        if ($is_customer && ($order['status_pesanan'] == 'Menunggu Pembayaran' || $order['status_pesanan'] == 'COD (Menunggu Konfirmasi)')):
        ?>
        <div style="margin-top: 20px; border-top: 2px solid #ddd; padding-top: 20px;">
            <p>Ingin membatalkan pesanan ini?</p>
            <form action="proses_batal_user.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                <button type="submit" class="btn-batal-user">Batalkan Pesanan Saya</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
// Hanya jalankan script cetak jika tombolnya ada (untuk admin)
if (document.getElementById('download-png')) {
    document.getElementById('download-png').addEventListener('click', function(e) {
        e.preventDefault();
        const strukArea = document.getElementById('struk-area');
        html2canvas(strukArea, { scale: 2 }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'struk_boemicamp_order_<?php echo $order_id; ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });
}
</script>

<?php include 'footer.php'; ?>