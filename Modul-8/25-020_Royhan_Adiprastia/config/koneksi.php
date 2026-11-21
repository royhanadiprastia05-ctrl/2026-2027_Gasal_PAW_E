<?php

$host = "localhost";
$db = "db_paw_modul8";
$user = "root";
$pass = "";

try {
    $koneksi = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $koneksi->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}