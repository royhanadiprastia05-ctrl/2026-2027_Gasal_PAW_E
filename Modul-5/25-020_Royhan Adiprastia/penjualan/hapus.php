<?php
include 'koneksi.php';

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $query = $koneksi->prepare(
        "DELETE FROM supplier WHERE id = ?"
    );

    $query->execute([$id]);

    header("Location: index.php");
    exit;
}
?>