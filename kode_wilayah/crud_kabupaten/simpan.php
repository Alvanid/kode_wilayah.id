<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $provinsi = trim($_POST['provinsi']);

    if (empty($kode) || empty($nama) || empty($provinsi)) {
        die("Semua field harus diisi.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO kabupaten (`COL 1`, `COL 2`, `COL 3`) VALUES (?, ?, ?)");
        $stmt->execute([$kode, $provinsi, $nama]);
        session_start();
        $_SESSION['toast'] = 'Data kabupaten berhasil disimpan.';
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal menyimpan: " . $e->getMessage());
    }
} else {
    header("Location: tambah.php");
    exit;
}
?>