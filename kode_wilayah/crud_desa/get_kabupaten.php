<?php
require_once 'config.php';

$prov = isset($_GET['provinsi']) ? $_GET['provinsi'] : '';
header('Content-Type: application/json; charset=utf-8');

if ($prov === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT `COL 1` AS kode_kabupaten, `COL 3` AS nama_kabupaten FROM kabupaten WHERE `COL 2` = ? ORDER BY `COL 3` ASC");
$stmt->bind_param('s', $prov);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

echo json_encode($rows);

?>
