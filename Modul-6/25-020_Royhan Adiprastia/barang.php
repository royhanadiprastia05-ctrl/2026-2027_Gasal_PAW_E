
<?php
include 'koneksi.php';

$data = mysqli_query($koneksi, "SELECT * FROM barang");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
</head>
<body>

<h2>Data Barang</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
    </tr>

    <?php while ($b = mysqli_fetch_assoc($data)) { ?>
    <tr>
        <td><?= $b['id'] ?></td>
        <td><?= $b['kode_barang'] ?></td>
        <td><?= $b['nama_barang'] ?></td>
        <td><?= number_format($b['harga']) ?></td>
        <td><?= $b['stok'] ?></td>
        <td>
            <a href="hapus_barang.php?id=<?= $b['id'] ?>"
               onclick="return confirm('Yakin ingin menghapus barang ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
