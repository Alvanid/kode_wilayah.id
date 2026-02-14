<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode_kecamatan'];
    $nama = $_POST['nama_kecamatan'];
    $original_kode = $_POST['original_kode'];

    $stmt = $pdo->prepare("UPDATE kecamatan SET `COL 1` = ?, `COL 3` = ? WHERE `COL 1` = ?");
    $stmt->execute([$kode, $nama, $original_kode]);

    header("Location: edit.php?kode=" . urlencode($kode) . "&success=1");
    exit;
} else {
    header("Location: index.php");
    exit;
}
