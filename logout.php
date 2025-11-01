<?php
session_start(); // Wajib ada untuk mengakses session

// Hancurkan semua data session
session_unset();
session_destroy();

// Arahkan kembali ke halaman login
header("Location: login.php");
exit;
?>