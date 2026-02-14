<?php
require_once 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$kode = trim($_POST['kode'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$provinsi = trim($_POST['provinsi'] ?? '');
$kabupaten = trim($_POST['kabupaten'] ?? '');

if ($kode === '' || $nama === '' || $provinsi === '' || $kabupaten === '') {
    $_SESSION['toast'] = 'Semua field harus diisi.';
    header('Location: tambah.php');
    exit;
}

try {
    // cek apakah kode sudah ada
    $check = $pdo->prepare("SELECT COUNT(*) FROM kecamatan WHERE `COL 1` = ?");
    $check->execute([$kode]);
    if ($check->fetchColumn() > 0) {
        $_SESSION['toast'] = 'Kode kecamatan sudah ada.';
        header('Location: tambah.php');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO kecamatan (`COL 1`, `COL 3`) VALUES (?, ?)");
    $stmt->execute([$kode, $nama]);

    $_SESSION['toast'] = 'Data kecamatan berhasil disimpan.';
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['toast'] = 'Gagal menyimpan: ' . $e->getMessage();
    header('Location: tambah.php');
    exit;
}
