<?php

include 'koneksi.php';

$mulai = $_GET['tanggal_mulai'] ?? '';
$selesai = $_GET['tanggal_selesai'] ?? '';

if (!$mulai || !$selesai || $mulai > $selesai) {
    die("Rentang tanggal tidak valid.");
}

$query = $koneksi->prepare(
    "SELECT DATE(waktu_transaksi) AS tanggal,
            SUM(total) AS total_harian
     FROM transaksi
     WHERE waktu_transaksi >= ?
     AND waktu_transaksi < DATE_ADD(?, INTERVAL 1 DAY)
     GROUP BY DATE(waktu_transaksi)
     ORDER BY tanggal ASC"
);

$query->bind_param("ss", $mulai, $selesai);
$query->execute();

$hasil = $query->get_result();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_penjualan.xls");

echo "<table border='1'>";

echo "<tr>";
echo "<th>No</th>";
echo "<th>Total Pendapatan</th>";
echo "<th>Tanggal Transaksi</th>";
echo "</tr>";

$no = 1;

while ($row = $hasil->fetch_assoc()) {

    echo "<tr>";

    echo "<td>" . $no++ . "</td>";

    echo "<td>" . $row['total_harian'] . "</td>";

    echo "<td>" . $row['tanggal'] . "</td>";

    echo "</tr>";
}

echo "</table>";

exit;

?>