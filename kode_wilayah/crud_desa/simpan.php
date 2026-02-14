<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$kode = isset($_POST['kode_desa']) ? $conn->real_escape_string(trim($_POST['kode_desa'])) : '';
$nama = isset($_POST['nama_desa']) ? $conn->real_escape_string(trim($_POST['nama_desa'])) : '';
$kecamatan = isset($_POST['kecamatan']) ? $conn->real_escape_string(trim($_POST['kecamatan'])) : '';

if ($kode === '' || $nama === '' || $kecamatan === '') {
    // simple error, redirect back
    header('Location: tambah.php');
    exit;
}

// Ensure kode unique
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM desa WHERE `COL 1` = ?");
$stmt->bind_param('s', $kode);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if ($row['cnt'] > 0) {
    header('Location: tambah.php');
    exit;
}

$stmt = $conn->prepare("INSERT INTO desa (`COL 1`, `COL 2`, `COL 3`) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $kode, $kecamatan, $nama);
$stmt->execute();

header('Location: index.php');
exit;

?>
