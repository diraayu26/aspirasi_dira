<?php
session_start();
if (isset($_SESSION['admin'])) {
 header("Location: dashboard.php");
 exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Login Admin</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="login-body">
<div class="login-card">
 <div class="login-logo">E-Aspirasi</div>
 <h2>Login Admin</h2>
 <p>Masuk untuk mengelola pengaduan siswa.</p>
 <form action="proses_login.php" method="POST">
 <div class="form-group">
 <label>Username</label>
 <input type="text" name="username" required>
 </div>
 <div class="form-group">
 <label>Password</label>
 <input type="password" name="password" required>
 </div>
 <button type="submit" class="btn-primary full">Login</button>
 </form>
 <a href="../index.php" class="back-link">← Kembali ke halaman utama</a>
</div>
</body>
</html>