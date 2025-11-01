<?php
// Wajib ada di setiap halaman yang menggunakan session
session_start(); 

// Jika admin sudah login, jangan tampilkan halaman login lagi
// Lempar dia langsung ke dashboard
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; display: grid; place-items: center; min-height: 90vh; background: #f4f4f4; }
        .login-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        .login-box h2 { text-align: center; margin-top: 0; margin-bottom: 20px; color: #1a3b25; }
        .login-box div { margin-bottom: 15px; }
        .login-box label { display: block; margin-bottom: 5px; font-weight: 600; }
        .login-box input { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .login-box button { width: 100%; padding: 12px; background: #01442a; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .error-msg { color: #D8000C; background: #FFD2D2; border: 1px solid #D8000C; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        
        <?php 
        // Tampilkan pesan error jika login gagal (dikirim dari proses_login.php)
        if(isset($_GET['error'])): 
        ?>
            <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>