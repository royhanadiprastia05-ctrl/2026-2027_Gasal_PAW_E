
<?php
require_once __DIR__ . '/../assets/auth.php';
cekLogin();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark"
     style="background-color: #1e3a5f;">

    <div class="container-fluid">

        <a class="navbar-brand" href="index.php">
            Sistem Penjualan
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Home
                    </a>
                </li>

                <?php if ($_SESSION['level'] == 1): ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#" data-bs-toggle="dropdown">
                        Data Master
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="master.php?jenis=barang">Data Barang</a></li>
                        <li><a class="dropdown-item" href="master.php?jenis=supplier">Data Supplier</a></li>
                        <li><a class="dropdown-item" href="master.php?jenis=pelanggan">Data Pelanggan</a></li>
                        <li><a class="dropdown-item" href="master.php?jenis=user">Data User</a></li>
                    </ul>
                </li>

                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="transaksi.php">
                        Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="laporan.php">
                        Laporan
                    </a>
                </li>

            </ul>

            <span class="navbar-text text-white me-3">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </span>

            <a href="logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>

        </div>
    </div>
</nav>

<div class="container mt-4">
