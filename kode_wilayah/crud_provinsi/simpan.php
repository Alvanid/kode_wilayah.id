<?php
include 'config.php';

$kode = $_POST['kode'] ?? '';
$nama = $_POST['nama'] ?? '';

if ($kode && $nama) {
    $kode = mysqli_real_escape_string($conn, $kode);
    $nama = mysqli_real_escape_string($conn, $nama);

    $sql = "INSERT INTO provinsi (`COL 1`, `COL 3`) VALUES ('$kode', '$nama')";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        die("Error: " . mysqli_error($conn));
    }
} else {
    die("Kode dan Nama Provinsi harus diisi.");
}
?>
