<?php
require_once __DIR__ . "/database.php";
$data = null;
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 $token = strtoupper(trim($_POST['token'] ?? ''));
 if ($token === '') {
 $error = "Kode laporan harus diisi.";
 } else {
 $query = "SELECT * FROM pengaduan WHERE token = ?";
 $stmt = $koneksi->prepare($query);
 $stmt->bind_param("s", $token);
 $stmt->execute();
 $hasil = $stmt->get_result();
 if ($hasil->num_rows > 0) {
 $data = $hasil->fetch_assoc();
 } else {
 $error = "Kode laporan tidak ditemukan.";
 }
 }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Tracking Aspirasi</title>
 <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="navbar">
 <div class="logo">E-Aspirasi</div>
 <div>
 <a href="index.php">Beranda</a>
 <a href="tracking.php">Tracking</a>
 <a href="admin/login.php">Admin</a>
 </div>
</nav>
<section class="container tracking-page">
 <div class="section-title">
 <span>TRACKING</span>
 <h2>Lacak Aspirasi</h2>
 <p>Masukkan kode laporan yang Anda dapatkan setelah mengirim aspirasi.</p>
 </div>
 <form method="POST" class="tracking-form">
 <input type="text" name="token" placeholder="Contoh: A12BC34D56" required>
 <button type="submit" class="btn-primary">Lacak</button>
 </form>
 <?php if ($error): ?>
 <div class="error-box">
 <?= htmlspecialchars($error); ?>
 </div>
 <?php endif; ?>
 <?php if ($data): ?>
 <div class="result-card">
 <div class="result-header">
 <div>
 <small>KODE LAPORAN</small>
 <h3><?= htmlspecialchars($data['token']); ?></h3>
 </div>
 <span class="status"><?= htmlspecialchars($data['status']); ?></span>
 </div>
 <div class="result-content">
 <p>
 <strong>Judul:</strong><br>
 <?= htmlspecialchars($data['judul']); ?>
 </p>
 <p>
 <strong>Kategori:</strong><br>
 <?= htmlspecialchars($data['kategori']); ?>
 </p>
 <p>
 <strong>Isi:</strong><br>
 <?= nl2br(htmlspecialchars($data['isi'])); ?>
 </p>
 <?php if (!empty($data['tanggapan'])): ?>
 <div class="response-box">
 <strong>Tanggapan Admin</strong>
<p><?= nl2br(htmlspecialchars($data['tanggapan'])); ?></p>
 </div>
 <?php endif; ?>
 <small>
 Dibuat: <?= htmlspecialchars($data['tanggal_dibuat']); ?>
 </small>
 </div>
 </div>
 <?php endif; ?>
</section>
</body>
</html>
