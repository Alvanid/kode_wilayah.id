<?php
include 'config.php';

$kode = $_GET['kode'] ?? '';
if (!$kode) {
    die("Kode provinsi tidak ditemukan.");
}

$kode_safe = mysqli_real_escape_string($conn, $kode);
$sql = "DELETE FROM provinsi WHERE `COL 1`='$kode_safe'";
if (mysqli_query($conn, $sql)) {
    header("Location: index.php");
    exit;
} else {
    die("Error: " . mysqli_error($conn));
}
?>
