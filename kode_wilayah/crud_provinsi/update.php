<?php
include 'config.php';

$kode_lama = $_POST['kode_lama'] ?? '';
$kode = $_POST['kode'] ?? '';
$nama = $_POST['nama'] ?? '';

if ($kode_lama && $kode && $nama) {
    $kode_lama = mysqli_real_escape_string($conn, $kode_lama);
    $kode = mysqli_real_escape_string($conn, $kode);
    $nama = mysqli_real_escape_string($conn, $nama);

    $sql = "UPDATE provinsi SET `COL 1`='$kode', `COL 3`='$nama' WHERE `COL 1`='$kode_lama'";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        die("Error: " . mysqli_error($conn));
    }
} else {
    die("Semua data harus diisi.");
}
?>
