<?php
require_once 'config.php';

$kab = isset($_GET['kabupaten']) ? $_GET['kabupaten'] : '';
header('Content-Type: application/json; charset=utf-8');

if ($kab === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT `COL 1` AS kode_kecamatan, `COL 3` AS nama_kecamatan FROM kecamatan WHERE `COL 2` = ? ORDER BY `COL 3` ASC");
$stmt->bind_param('s', $kab);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

echo json_encode($rows);

?>
