<?php
require_once 'config.php';

$prov = $_GET['provinsi'] ?? '';
header('Content-Type: application/json; charset=utf-8');
if ($prov === '') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT `COL 1` AS kode_kabupaten, `COL 3` AS nama_kabupaten FROM kabupaten WHERE `COL 2` = ? ORDER BY `COL 3` ASC");
$stmt->execute([$prov]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
