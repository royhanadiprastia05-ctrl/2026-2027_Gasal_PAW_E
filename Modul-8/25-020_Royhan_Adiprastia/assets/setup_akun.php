<?php

require_once __DIR__ . '/../config/koneksi.php';

$akun = [
    [
        'username' => 'owner',
        'password' => 'owner123',
        'nama' => 'Owner',
        'level' => 1
    ],
    [
        'username' => 'kasir',
        'password' => 'kasir123',
        'nama' => 'Kasir',
        'level' => 2
    ]
];

$cekQuery = $koneksi->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$insertQuery = $koneksi->prepare(
    "INSERT INTO users (username, password, nama, level) VALUES (?, ?, ?, ?)"
);
$updateQuery = $koneksi->prepare(
    "UPDATE users SET password = ?, nama = ?, level = ? WHERE username = ?"
);

foreach ($akun as $data) {
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $cekQuery->execute([$data['username']]);
    $existingUser = $cekQuery->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $updateQuery->execute([
            $password,
            $data['nama'],
            $data['level'],
            $data['username']
        ]);
    } else {
        $insertQuery->execute([
            $data['username'],
            $password,
            $data['nama'],
            $data['level']
        ]);
    }
}

echo "Akun Owner dan Kasir berhasil disiapkan.";