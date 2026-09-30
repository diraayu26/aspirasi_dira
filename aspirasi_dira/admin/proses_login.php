<?php
session_start();
require_once __DIR__ . "/../database.php";
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
if ($username === '' || $password === '') {
 die("Username dan password wajib diisi.");
}
$query = "SELECT * FROM admin WHERE username = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$hasil = $stmt->get_result();
if ($hasil->num_rows === 1) {
 $admin = $hasil->fetch_assoc();
 if (password_verify($password, $admin['password'])) {
 session_regenerate_id(true);
 $_SESSION['admin'] = [
 'id' => $admin['id'],
 'username' => $admin['username']
 ];
 header("Location: dashboard.php");
 exit;
 }
}
header("Location: login.php?error=1");
exit;
?>
