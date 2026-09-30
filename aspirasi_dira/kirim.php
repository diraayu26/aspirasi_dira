<?php
require_once __DIR__ . "/database.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
 header("Location: index.php");
 exit;
}
$anonim = $_POST['anonim'] ?? 'tidak';
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$judul = trim($_POST['judul'] ?? '');
$isi = trim($_POST['isi'] ?? '');
$allowedKategori = [
 'Fasilitas',
 'Pembelajaran',
 'Kebersihan',
 'Keamanan',
 'Lainnya'
];
if (!in_array($kategori, $allowedKategori)) {
 die("Kategori tidak valid.");
}
if ($judul === '' || $isi === '') {
 die("Judul dan isi pengaduan wajib diisi.");
}
if ($anonim === 'ya') {
 $nama = null;
 $email = null;
} else {
 if ($nama === '') {
 die("Nama wajib diisi jika tidak anonim.");
 }
 if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
 die("Format email tidak valid.");
 }
}
$token = strtoupper(substr(bin2hex(random_bytes(6)), 0, 10));
$query = "INSERT INTO pengaduan
 (token, nama, email, anonim, kategori, judul, isi)
 VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $koneksi->prepare($query);
$stmt->bind_param(
 "sssssss",
 $token,
 $nama,
 $email,
 $anonim,
 $kategori,
 $judul,
 $isi
);
if ($stmt->execute()) {
 header("Location: index.php?success=1&token=" . urlencode($token));
 exit;
} else {
 die("Gagal menyimpan pengaduan.");
}
?>
