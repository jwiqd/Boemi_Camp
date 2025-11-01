<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_input = $_POST['password'];

    // --- PENYESUAIAN ---
    // Mengambil dari tabel 'admins' dan 'password_hash'
    $stmt = $koneksi->prepare("SELECT id, username, password_hash, full_name FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // --- PENYESUAIAN ---
        // Memverifikasi password dengan kolom 'password_hash'
        if (password_verify($password_input, $admin['password_hash'])) {
            // Jika login berhasil
            $_SESSION['status_login'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_fullname'] = $admin['full_name']; // Simpan nama lengkap di session
            
            header("Location: dashboard.php");
            exit;
        } else {
            header("Location: login.php?error=Password salah!");
            exit;
        }
    } else {
        header("Location: login.php?error=Username tidak ditemukan!");
        exit;
    }

    $stmt->close();
    $koneksi->close();
} else {
    header("Location: login.php");
    exit;
}
?>