<?php
include 'config.php';

$kode = $_GET['kode'] ?? '';
if (!$kode) {
    die("Kode provinsi tidak ditemukan.");
}

$kode_safe = mysqli_real_escape_string($conn, $kode);
$sql = "SELECT `COL 1`, `COL 3` FROM provinsi WHERE `COL 1` = '$kode_safe'";
$result = mysqli_query($conn, $sql);
if (!$row = mysqli_fetch_assoc($result)) {
    die("Data provinsi tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Provinsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Edit Provinsi</h2>

    <form action="update.php" method="post">
        <input type="hidden" name="kode_lama" value="<?= htmlspecialchars($row['COL 1']) ?>">
        <div class="mb-3">
            <label for="kode" class="form-label">Kode Provinsi</label>
            <input type="text" name="kode" id="kode" class="form-control" value="<?= htmlspecialchars($row['COL 1']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Provinsi</label>
            <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($row['COL 3']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
