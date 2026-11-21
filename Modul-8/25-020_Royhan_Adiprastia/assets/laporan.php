<?php

include __DIR__ . '/../includes/header.php';
cekLogin();

$query = $koneksi->query(
    "SELECT transaksi.*,
    pelanggan.nama AS nama_pelanggan,
    barang.nama AS nama_barang
    FROM transaksi
    JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id
    JOIN barang ON transaksi.barang_id = barang.id
    ORDER BY transaksi.waktu_transaksi DESC"
);

$data = $query->fetchAll(PDO::FETCH_ASSOC);

$queryTotal = $koneksi->query(
    "SELECT
    COUNT(*) AS jumlah_transaksi,
    SUM(total) AS total_penjualan
    FROM transaksi"
);

$ringkasan = $queryTotal->fetch(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<h3>Laporan Penjualan</h3>

<div class="row mb-4">

    <div class="col-md-6">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <h6>Jumlah Transaksi</h6>
                <h3><?= $ringkasan['jumlah_transaksi'] ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <h6>Total Penjualan</h6>
                <h3>
                    Rp <?= number_format(
                        $ringkasan['total_penjualan'] ?? 0,
                        0,
                        ',',
                        '.'
                    ) ?>
                </h3>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-sm">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">
            <h5>Data Laporan Transaksi</h5>

            <button onclick="window.print()"
                    class="btn btn-primary btn-sm">
                Cetak Laporan
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($data as $d): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($d['waktu_transaksi']) ?></td>
                            <td><?= htmlspecialchars($d['nama_pelanggan']) ?></td>
                            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                            <td>
                                Rp <?= number_format($d['harga'], 0, ',', '.') ?>
                            </td>
                            <td><?= $d['qty'] ?></td>
                            <td>
                                Rp <?= number_format($d['total'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada data laporan.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/header.php'; ?>
