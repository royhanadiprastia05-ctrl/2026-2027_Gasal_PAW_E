
<?php

$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_reporting_paw7"
);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
