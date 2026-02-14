<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_lama = trim($_POST['kode_lama']);
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $provinsi = trim($_POST['provinsi']);

    if (empty($kode_lama) || empty($kode) || empty($nama) || empty($provinsi)) {
        die("Semua field harus diisi.");
    }

    try {
        $stmt = $pdo->prepare("UPDATE kabupaten SET `COL 1` = ?, `COL 2` = ?, `COL 3` = ? WHERE `COL 1` = ?");
        $stmt->execute([$kode, $provinsi, $nama, $kode_lama]);
        session_start();
        $_SESSION['toast'] = 'Data kabupaten berhasil diperbarui.';
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal update: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>