<?php
session_start();
if (!isset($_SESSION['admin'])) {
 header("Location: login.php");
 exit;
}
require_once __DIR__ . "/../database.php";
$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$tanggapan = trim($_POST['tanggapan'] ?? '');
$allowedStatus = [
 'Menunggu',
 'Diproses',
 'Selesai'
];
if (!in_array($status, $allowedStatus)) {
 die("Status tidak valid.");
}
$query = "
 UPDATE pengaduan
 SET status = ?,
 tanggapan = ?
 WHERE id = ?
";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("ssi", $status, $tanggapan, $id);
if ($stmt->execute()) {
 header("Location: dashboard.php?success=1");
 exit;
}
die("Gagal memperbarui data.");
?>