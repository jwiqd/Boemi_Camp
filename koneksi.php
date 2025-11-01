<?php
$db_host = 'localhost';   // Alamat server database (default XAMPP)
$db_user = 'root';        // Username database (default XAMPP)
$db_pass = '';            // Password database (default XAMPP)
$db_name = 'boemi_camp_db'; // Nama database yang Anda buat

// Buat koneksi ke database
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek jika koneksi gagal
if (mysqli_connect_errno()) {
    // Hentikan program dan tampilkan pesan error
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
