<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$query = $koneksi->prepare(
    "SELECT * FROM supplier WHERE id = ?"
);
$query->execute([$id]);

$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data supplier tidak ditemukan.");
}

if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $query = $koneksi->prepare(
        "UPDATE supplier
         SET nama = ?, telp = ?, alamat = ?
         WHERE id = ?"
    );

    $query->execute([$nama, $telp, $alamat, $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h3 class="mb-4">Edit Data Master Supplier</h3>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama"
                   class="form-control"
                   value="<?= htmlspecialchars($data['nama']); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Telp</label>
            <input type="text" name="telp"
                   class="form-control"
                   value="<?= htmlspecialchars($data['telp']); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat"
                   class="form-control"
                   value="<?= htmlspecialchars($data['alamat']); ?>"
                   required>
        </div>

        <button type="submit" name="update" class="btn btn-success">
            Update
        </button>

        <a href="index.php" class="btn btn-danger">Batal</a>

    </form>

</div>

</body>
</html>