<?php
$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_modul6_royhan"
);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
