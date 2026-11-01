<?php
$koneksi = new PDO(
    "mysql:host=localhost;dbname=penjualan;charset=utf8mb4",
    "root",
    ""
);

$koneksi->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);
?>