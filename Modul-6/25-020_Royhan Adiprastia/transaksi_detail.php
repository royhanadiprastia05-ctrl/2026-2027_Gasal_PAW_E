<?php
include 'koneksi.php';

$transaksi_id = isset($_GET['id']) ? (int) $_GET['id'] : 1;

if (isset($_POST['simpan'])) {
    $barang_id = (int) $_POST['barang_id'];
    $qty       = (int) $_POST['qty'];

    if ($qty > 0) {
        $cek = mysqli_query($koneksi, "
            SELECT id, harga FROM barang 
            WHERE id = $barang_id 
            AND id NOT IN (
                SELECT barang_id FROM transaksi_detail 
                WHERE transaksi_id = $transaksi_id
            )
        ");

        if (mysqli_num_rows($cek) > 0) {
            $barang = mysqli_fetch_assoc($cek);
            $harga  = (int) $barang['harga'];

            $simpan = mysqli_query($koneksi, "
                INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) 
                VALUES ($transaksi_id, $barang_id, $harga, $qty)
            ");

            if ($simpan) {
                mysqli_query($koneksi, "
                    UPDATE transaksi 
                    SET total = (
                        SELECT COALESCE(SUM(harga * qty), 0) 
                        FROM transaksi_detail 
                        WHERE transaksi_id = $transaksi_id
                    ) 
                    WHERE id = $transaksi_id
                ");

                echo "<script>
                    alert('Detail transaksi berhasil ditambahkan!');
                    window.location='transaksi_detail.php?id=$transaksi_id';
                </script>";
                exit;
            }
        } else {
            echo "<script>alert('Barang sudah ada di daftar transaksi ini atau tidak ditemukan!');</script>";
        }
    } else {
        echo "<script>alert('Jumlah (qty) harus lebih dari 0!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Detail Transaksi</title>
</head>
<body>

<h2>Master Transaksi</h2>

<form method="GET">
    <label>Pilih Transaksi:</label>
    <select name="id" onchange="this.form.submit()">
        <?php
        $data = mysqli_query($koneksi, "SELECT * FROM transaksi");
        while ($t = mysqli_fetch_assoc($data)) {
            $selected = ($t['id'] == $transaksi_id) ? 'selected' : '';
            echo "<option value='{$t['id']}' $selected>Transaksi #{$t['id']}</option>";
        }
        ?>
    </select>
</form>

<hr>

<h3>Input Detail Transaksi</h3>

<form method="POST">
    <label>Barang:</label>
    <select name="barang_id" required>
        <option value="">-- Pilih Barang --</option>
        <?php
        $barang = mysqli_query($koneksi, "
            SELECT * FROM barang 
            WHERE id NOT IN (
                SELECT barang_id 
                FROM transaksi_detail 
                WHERE transaksi_id = $transaksi_id
            )
        ");

        while ($b = mysqli_fetch_assoc($barang)) {
            echo "<option value='{$b['id']}'>
                {$b['nama_barang']} - Rp " . number_format($b['harga'], 0, ',', '.') . "
            </option>";
        }
        ?>
    </select>

    <br><br>

    <label>Jumlah (Qty):</label>
    <input type="number" name="qty" min="1" value="1" required>

    <br><br>

    <button type="submit" name="simpan">Simpan Detail</button>
</form>

<hr>

<h3>Detail Transaksi #<?= $transaksi_id ?></h3>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $detail = mysqli_query($koneksi, "
            SELECT barang.nama_barang, 
                   transaksi_detail.harga, 
                   transaksi_detail.qty 
            FROM transaksi_detail 
            JOIN barang ON barang.id = transaksi_detail.barang_id 
            WHERE transaksi_detail.transaksi_id = $transaksi_id
        ");

        while ($d = mysqli_fetch_assoc($detail)) {
            $subtotal = $d['harga'] * $d['qty'];
            echo "<tr>
                <td>{$d['nama_barang']}</td>
                <td>Rp " . number_format($d['harga'], 0, ',', '.') . "</td>
                <td>{$d['qty']}</td>
                <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
            </tr>";
        }

        $hasil = mysqli_query($koneksi, "SELECT total FROM transaksi WHERE id = $transaksi_id");
        $total = mysqli_fetch_assoc($hasil);
        $total_bayar = isset($total['total']) ? $total['total'] : 0;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" align="right">Total Transaksi:</th>
            <th>Rp <?= number_format($total_bayar, 0, ',', '.') ?></th>
        </tr>
    </tfoot>
</table>

</body>
</html>