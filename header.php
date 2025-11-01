<?php
// Memulai session di setiap halaman.
// Ini penting untuk menyimpan keranjang belanja.
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Boemi Camp Adventure</title>
    <link rel="stylesheet" href="styles.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" href="images/logo.png" type="image/png" />
</head>
<body>
    <header>
      <div class="logo-wrapper">
        <img
          src="images/logo.png"
          alt="Boemi Camp Adventure Logo"
          class="logo-img"
        />
        <span class="logo-text">Boemi Camp Adventure</span>
      </div>
      <div class="menu-toggle" id="menuToggle">☰</div>
      <nav id="navMenu">
        <a href="index.php#home">Home</a>
        <a href="index.php#katalog">Katalog</a>
        <a href="index.php#paket">Paket</a>
        <a href="index.php#testimoni">Testimoni</a>
        <a href="index.php#contact">Contact</a>
        
        <a href="lacak_pesanan.php" style="color: #f0ad4e; font-weight: 600;">Lacak Pesanan</a>
        <a href="keranjang.php" style="color: #00cc7a; font-weight: 600;">
            Keranjang (<?php 
                if (!empty($_SESSION['keranjang'])) {
                    echo count($_SESSION['keranjang']);
                } else {
                    echo 0;
                }
            ?>)
        </a>
      </nav>
      </nav>
    </header>