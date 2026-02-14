<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kode_wilayah";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Koneksi gagal: " . $e->getMessage());
}
?>
