
<?php
include 'koneksi.php';

$mulai = $_GET['tanggal_mulai'] ?? '';
$selesai = $_GET['tanggal_selesai'] ?? '';

$rekap = [];
$total_pendapatan = 0;
$total_pelanggan = 0;

if (!empty($mulai) && !empty($selesai)) {

    if ($mulai > $selesai) {
        die("Tanggal mulai tidak boleh melebihi tanggal selesai.");
    }

    $query = "SELECT DATE(waktu_transaksi) AS tanggal,
              SUM(total) AS total_pendapatan
              FROM transaksi
              WHERE waktu_transaksi BETWEEN ? AND ?
              GROUP BY DATE(waktu_transaksi)
              ORDER BY tanggal ASC";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ss", $mulai, $selesai);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($hasil)) {
        $rekap[] = $row;
        $total_pendapatan += $row['total_pendapatan'];
    }

    $query2 = "SELECT COUNT(DISTINCT pelanggan_id) AS jumlah
               FROM transaksi
               WHERE waktu_transaksi BETWEEN ? AND ?";

    $stmt2 = mysqli_prepare($koneksi, $query2);
    mysqli_stmt_bind_param($stmt2, "ss", $mulai, $selesai);
    mysqli_stmt_execute($stmt2);

    $hasil2 = mysqli_stmt_get_result($stmt2);
    $data = mysqli_fetch_assoc($hasil2);

    $total_pelanggan = $data['jumlah'];
}

$tanggal = [];
$pendapatan = [];

foreach ($rekap as $row) {
    $tanggal[] = $row['tanggal'];
    $pendapatan[] = (int) $row['total_pendapatan'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reporting Laporan Penjualan</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f9;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1, h2 {
            color: #333;
        }

        input, button, a {
            padding: 10px;
            margin: 5px;
        }

        button, .tombol {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .excel {
            background: #16a34a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #2563eb;
            color: white;
        }

        .ringkasan {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #e0edff;
            padding: 20px;
            border-radius: 8px;
            flex: 1;
        }

        .grafik {
            max-width: 750px;
            margin: 25px auto;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                margin: 10px;
            }

            .container {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Reporting Laporan Penjualan</h1>

    <div class="no-print">

        <form method="GET">

            <label>Tanggal Mulai:</label>

            <input type="date"
                   name="tanggal_mulai"
                   value="<?= htmlspecialchars($mulai) ?>"
                   required>

            <label>Tanggal Selesai:</label>

            <input type="date"
                   name="tanggal_selesai"
                   value="<?= htmlspecialchars($selesai) ?>"
                   required>

            <button type="submit">Tampilkan</button>

        </form>

        <?php if (!empty($mulai) && !empty($selesai)): ?>

            <button onclick="window.print()">Cetak</button>

            <a class="tombol excel"
               href="export_excel.php?tanggal_mulai=<?= urlencode($mulai) ?>&tanggal_selesai=<?= urlencode($selesai) ?>">
                Excel
            </a>

        <?php endif; ?>

    </div>

    <?php if (!empty($mulai) && !empty($selesai)): ?>

        <h2>Grafik Penjualan Harian</h2>

        <?php if (!empty($rekap)): ?>

            <div class="grafik">
                <canvas id="grafikPenjualan"></canvas>
            </div>

            <script>
                new Chart(document.getElementById('grafikPenjualan'), {
                    type: 'bar',

                    data: {
                        labels: <?= json_encode($tanggal) ?>,

                        datasets: [{
                            label: 'Total Pendapatan',

                            data: <?= json_encode($pendapatan) ?>,

                            backgroundColor: 'rgba(37, 99, 235, 0.7)',

                            borderColor: 'rgb(37, 99, 235)',

                            borderWidth: 1
                        }]
                    },

                    options: {
                        responsive: true,

                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal Transaksi'
                                }
                            },

                            y: {
                                beginAtZero: true,

                                title: {
                                    display: true,
                                    text: 'Total Pendapatan (Rp)'
                                }
                            }
                        }
                    }
                });
            </script>

        <?php else: ?>

            <p>Tidak ada data penjualan pada rentang tanggal tersebut.</p>

        <?php endif; ?>

        <h2>Rekapitulasi Penjualan</h2>

        <table>

            <tr>
                <th>No</th>
                <th>Total Pendapatan</th>
                <th>Tanggal Transaksi</th>
            </tr>

            <?php $no = 1; ?>

            <?php foreach ($rekap as $row): ?>

                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        Rp <?= number_format(
                            $row['total_pendapatan'], 0, ',', '.'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['tanggal']) ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        </table>

        <div class="ringkasan">

            <div class="card">
                <h3>Jumlah Pelanggan Unik</h3>
                <h2><?= $total_pelanggan ?> Orang</h2>
            </div>

            <div class="card">
                <h3>Total Pendapatan Keseluruhan</h3>

                <h2>
                    Rp <?= number_format(
                        $total_pendapatan, 0, ',', '.'
                    ) ?>
                </h2>
            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
