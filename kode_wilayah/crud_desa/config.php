<?php
// Konfigurasi database
$host = "localhost";
$user = "root";      // sesuaikan dengan user database
$pass = "";          // sesuaikan dengan password database
$db   = "kode_wilayah"; // sesuaikan dengan nama database

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk escape input agar aman
function e($str) {
    global $conn;
    return htmlspecialchars($conn->real_escape_string($str));
}
?>
