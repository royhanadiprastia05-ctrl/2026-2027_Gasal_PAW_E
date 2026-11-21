
<?php
require_once 'auth.php';
cekLogin();

include __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-body">

        <h3>Selamat Datang di Sistem Penjualan</h3>

        <p>
            Halo,
            <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>.
        </p>

        <?php if ($_SESSION['level'] == 1): ?>

            <div class="alert alert-primary">
                Anda login sebagai Owner.
                Anda memiliki akses untuk mengelola seluruh Data Master.
            </div>

        <?php else: ?>

            <div class="alert alert-success">
                Anda login sebagai Kasir.
                Anda memiliki akses ke menu Transaksi dan Laporan.
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
