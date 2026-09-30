<?php
session_start();
if (!isset($_SESSION['admin'])) {
 header("Location: login.php");
 exit;
}
require_once __DIR__ . "/../database.php";
$total = $koneksi->query(
 "SELECT COUNT(*) AS jumlah FROM pengaduan"
)->fetch_assoc()['jumlah'];
$menunggu = $koneksi->query(
 "SELECT COUNT(*) AS jumlah FROM pengaduan WHERE status='Menunggu'"
)->fetch_assoc()['jumlah'];
$diproses = $koneksi->query(
 "SELECT COUNT(*) AS jumlah FROM pengaduan WHERE status='Diproses'"
)->fetch_assoc()['jumlah'];
$selesai = $koneksi->query(
 "SELECT COUNT(*) AS jumlah FROM pengaduan WHERE status='Selesai'"
)->fetch_assoc()['jumlah'];
$query = "SELECT * FROM pengaduan ORDER BY tanggal_dibuat DESC";
$hasil = $koneksi->query($query);
?>
<!-- HTML dashboard dapat menggunakan tabel dengan kolom:
 No, Kode, Judul, Kategori, Status, Tanggal, dan Aksi. -->
<?php
$no = 1;
while ($row = $hasil->fetch_assoc()) {
?>
<tr>
 <td><?= $no++; ?></td>
 <td><?= htmlspecialchars($row['token']); ?></td>
 <td><?= htmlspecialchars($row['judul']); ?></td>
 <td><?= htmlspecialchars($row['kategori']); ?></td>
 <td><?= htmlspecialchars($row['status']); ?></td>
 <td><?= htmlspecialchars($row['tanggal_dibuat']); ?></td>
 <td>
 <a href="detail.php?id=<?= $row['id']; ?>">Detail</a>
 </td>
</tr>
<?php } ?>