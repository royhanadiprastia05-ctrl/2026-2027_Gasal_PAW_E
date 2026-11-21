<?php

require_once 'auth.php';
cekOwner();

$jenis = $_GET['jenis'] ?? '';

$daftar = [
    'barang' => [
        'judul' => 'Data Barang',
        'kolom' => ['nama', 'harga', 'stok']
    ],
    'supplier' => [
        'judul' => 'Data Supplier',
        'kolom' => ['nama', 'telp', 'alamat']
    ],
    'pelanggan' => [
        'judul' => 'Data Pelanggan',
        'kolom' => ['nama', 'telp', 'alamat']
    ],
    'user' => [
        'judul' => 'Data User',
        'kolom' => ['username', 'password', 'nama', 'level']
    ]
];

if (!isset($daftar[$jenis])) {
    die("Jenis data tidak ditemukan.");
}

$judul = $daftar[$jenis]['judul'];
$kolom = $daftar[$jenis]['kolom'];
$tabel = $jenis;

if (isset($_POST['simpan'])) {

    $nilai = [];

    foreach ($kolom as $field) {
        $nilai[$field] = $_POST[$field] ?? '';
    }

    if ($jenis == 'user') {
        $nilai['password'] = password_hash(
            $nilai['password'],
            PASSWORD_DEFAULT
        );
    }

    $fields = implode(', ', $kolom);
    $tanda = implode(', ', array_fill(0, count($kolom), '?'));

    $sql = "INSERT INTO $tabel ($fields) VALUES ($tanda)";

    $query = $koneksi->prepare($sql);
    $query->execute(array_values($nilai));

    header("Location: master.php?jenis=$jenis");
    exit;
}

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    if ($jenis == 'user' && $id == $_SESSION['id']) {
        die("Tidak dapat menghapus akun yang sedang digunakan.");
    }

    $query = $koneksi->prepare(
        "DELETE FROM $tabel WHERE id = ?"
    );

    $query->execute([$id]);

    header("Location: master.php?jenis=$jenis");
    exit;
}

$edit = null;

if (isset($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $query = $koneksi->prepare(
        "SELECT * FROM $tabel WHERE id = ?"
    );

    $query->execute([$id]);
    $edit = $query->fetch(PDO::FETCH_ASSOC);

    if (!$edit) {
        die("Data tidak ditemukan.");
    }

    if ($jenis == 'user') {
        unset($edit['password']);
    }
}

if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $nilai = [];

    foreach ($kolom as $field) {
        $nilai[$field] = $_POST[$field] ?? '';
    }

    if ($jenis == 'user') {

        if ($nilai['password'] == '') {
            unset($nilai['password']);
        } else {
            $nilai['password'] = password_hash(
                $nilai['password'],
                PASSWORD_DEFAULT
            );
        }
    }

    $set = implode(
        ', ',
        array_map(fn($field) => "$field = ?", array_keys($nilai))
    );

    $sql = "UPDATE $tabel SET $set WHERE id = ?";

    $query = $koneksi->prepare($sql);
    $query->execute([...array_values($nilai), $id]);

    header("Location: master.php?jenis=$jenis");
    exit;
}

$query = $koneksi->query("SELECT * FROM $tabel ORDER BY id DESC");
$data = $query->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<h3><?= htmlspecialchars($judul) ?></h3>

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <h5><?= $edit ? 'Edit Data' : 'Tambah Data' ?></h5>

        <form method="POST">

            <?php if ($edit): ?>
                <input type="hidden" name="id"
                       value="<?= $edit['id'] ?>">
            <?php endif; ?>

            <?php foreach ($kolom as $field): ?>

                <div class="mb-3">

                    <label class="form-label">
                        <?= ucfirst($field) ?>
                    </label>

                    <?php if ($field == 'alamat'): ?>

                        <textarea name="<?= $field ?>"
                                  class="form-control"
                                  required><?= htmlspecialchars($edit[$field] ?? '') ?></textarea>

                    <?php elseif ($field == 'level'): ?>

                        <select name="level" class="form-select" required>
                            <option value="1" <?= ($edit['level'] ?? '') == 1 ? 'selected' : '' ?>>
                                Owner
                            </option>
                            <option value="2" <?= ($edit['level'] ?? '') == 2 ? 'selected' : '' ?>>
                                Kasir
                            </option>
                        </select>

                    <?php else: ?>

                        <input
                            type="<?= $field == 'password' ? 'password' : 'text' ?>"
                            name="<?= $field ?>"
                            class="form-control"
                            value="<?= htmlspecialchars($edit[$field] ?? '') ?>"
                            <?= $field == 'password' && $edit ? '' : 'required' ?>
                            <?= $field == 'password' && $edit ? 'placeholder="Kosongkan jika tidak diubah"' : '' ?>
                        >

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

            <?php if ($edit): ?>
                <button type="submit" name="update"
                        class="btn btn-warning">Update</button>
                <a href="master.php?jenis=<?= $jenis ?>"
                   class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="simpan"
                        class="btn btn-primary">Simpan</button>
            <?php endif; ?>

        </form>

    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <h5>Daftar <?= htmlspecialchars($judul) ?></h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>No</th>

                        <?php foreach ($kolom as $field): ?>
                            <th><?= ucfirst($field) ?></th>
                        <?php endforeach; ?>

                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($data as $row): ?>

                        <tr>
                            <td><?= $no++ ?></td>

                            <?php foreach ($kolom as $field): ?>

                                <td>
                                    <?php
                                    if ($field == 'password') {
                                        echo '********';
                                    } elseif ($field == 'level') {
                                        echo $row[$field] == 1 ? 'Owner' : 'Kasir';
                                    } else {
                                        echo htmlspecialchars($row[$field]);
                                    }
                                    ?>
                                </td>

                            <?php endforeach; ?>

                            <td>
                                <a href="master.php?jenis=<?= $jenis ?>&edit=<?= $row['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <?php if ($jenis != 'user' || $row['id'] != $_SESSION['id']): ?>
                                    <a href="master.php?jenis=<?= $jenis ?>&hapus=<?= $row['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data?')">
                                        Hapus
                                    </a>
                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?= count($kolom) + 2 ?>"
                                class="text-center">
                                Belum ada data.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
