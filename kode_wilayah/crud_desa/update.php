<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$original = isset($_POST['original_kode']) ? $conn->real_escape_string(trim($_POST['original_kode'])) : '';
$kode = isset($_POST['kode_desa']) ? $conn->real_escape_string(trim($_POST['kode_desa'])) : '';
$nama = isset($_POST['nama_desa']) ? $conn->real_escape_string(trim($_POST['nama_desa'])) : '';
$kecamatan = isset($_POST['kecamatan']) ? $conn->real_escape_string(trim($_POST['kecamatan'])) : '';

if ($original === '' || $kode === '' || $nama === '' || $kecamatan === '') {
    header('Location: index.php');
    exit;
}

// Update record
$stmt = $conn->prepare("UPDATE desa SET `COL 1` = ?, `COL 2` = ?, `COL 3` = ? WHERE `COL 1` = ?");
$stmt->bind_param('ssss', $kode, $kecamatan, $nama, $original);
$stmt->execute();

header('Location: index.php');
exit;

?>
