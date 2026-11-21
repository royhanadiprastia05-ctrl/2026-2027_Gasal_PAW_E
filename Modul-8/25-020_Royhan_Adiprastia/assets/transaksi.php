<?php

require_once 'auth.php';
cekLogin();

$pesan = "";

if (isset($_POST['simpan'])) {

    $pelanggan_id = (int) $_POST['pelanggan_id'];
    $barang_id = (int) $_POST['barang_id'];
    $qty = (int) $_POST['qty'];

    if ($pelanggan_id <= 0 || $barang_id <= 0 || $qty <= 0) {
        $pesan = "Data transaksi tidak valid.";
    } else {
        try {
            $koneksi->beginTransaction();

            $query = $koneksi->prepare(
                "SELECT harga, stok FROM barang WHERE id = ? FOR UPDATE"
            );
            $query->execute([$barang_id]);
            $barang = $query->fetch(PDO::FETCH_ASSOC);

            if (!$barang) {
                throw new Exception("Barang tidak ditemukan.");
            }

            if ($barang['stok'] < $qty) {
                throw new Exception("Stok barang tidak mencukupi.");
            }

            $harga = $barang['harga'];
            $total = $harga * $qty;

            $query = $koneksi->prepare(
                "INSERT INTO transaksi
                (pelanggan_id, barang_id, harga, qty, total)
                VALUES (?, ?, ?, ?, ?)"
            );

            $query->execute([
                $pelanggan_id,
                $barang_id,
                $harga,
                $qty,
                $total
            ]);

            $query = $koneksi->prepare(
                "UPDATE barang SET stok = stok - ? WHERE id = ?"
            );

            $query->execute([$qty, $barang_id]);

            $koneksi->commit();

            $pesan = "Transaksi berhasil disimpan.";

        } catch (Exception $e) {

            if ($koneksi->inTransaction()) {
                $koneksi->rollBack();
            }

            $pesan = $e->getMessage();
        }
    }
}

$pelanggan = $koneksi->query(
    "SELECT * FROM pelanggan ORDER BY nama ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$barang = $koneksi->query(
    "SELECT * FROM barang ORDER BY nama ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$query = $koneksi->query(
    "SELECT transaksi.*, pelanggan.nama AS nama_pelanggan,
    barang.nama AS nama_barang
    FROM transaksi
    JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id
    JOIN barang ON transaksi.barang_id = barang.id
    ORDER BY transaksi.id DESC"
);

$data = $query->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<h3>Transaksi Penjualan</h3>

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <h5>Tambah Transaksi</h5>

        <?php if ($pesan != ""): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Pelanggan</label>

                <select name="pelanggan_id" class="form-select" required>
                    <option value="">Pilih Pelanggan</option>

                    <?php foreach ($pelanggan as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['nama']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Barang</label>

                <select name="barang_id" class="form-select" required>
                    <option value="">Pilih Barang</option>

                    <?php foreach ($barang as $b): ?>
                        <option value="<?= $b['id'] ?>">
                            <?= htmlspecialchars($b['nama']) ?>
                            - Rp <?= number_format($b['harga'], 0, ',', '.') ?>
                            (Stok: <?= $b['stok'] ?>)
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Barang</label>

                <input type="number" name="qty"
                       class="form-control" min="1" required>
            </div>

            <button type="submit" name="simpan"
                    class="btn btn-primary">
                Simpan Transaksi
            </button>

        </form>

    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <h5>Riwayat Transaksi</h5>

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
                            <td>Rp <?= number_format($d['harga'], 0, ',', '.') ?></td>
                            <td><?= $d['qty'] ?></td>
                            <td>Rp <?= number_format($d['total'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
