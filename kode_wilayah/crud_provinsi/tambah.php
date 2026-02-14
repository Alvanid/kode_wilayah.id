<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Provinsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Tambah Provinsi Baru</h2>

    <form action="simpan.php" method="post">
        <div class="mb-3">
            <label for="kode" class="form-label">Kode Provinsi</label>
            <input type="text" name="kode" id="kode" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Provinsi</label>
            <input type="text" name="nama" id="nama" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
