<?php
require_once 'config.php';

$kode = isset($_GET['kode']) ? $_GET['kode'] : '';
if ($kode === '') {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("DELETE FROM desa WHERE `COL 1` = ?");
$stmt->bind_param('s', $kode);
$stmt->execute();

header('Location: index.php');
exit;

?>
