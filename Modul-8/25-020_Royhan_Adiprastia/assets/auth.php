<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';

function cekLogin() {
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }
}

function cekOwner() {
    cekLogin();

    if ($_SESSION['level'] != 1) {
        header("Location: index.php");
        exit;
    }
}
