<?php
require 'config.php';

$kode = $_GET['kode'] ?? '';
if (empty($kode)) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT k.`COL 1` AS kode_kabupaten, k.`COL 3` AS nama_kabupaten, p.`COL 3` AS nama_provinsi FROM kabupaten k LEFT JOIN provinsi p ON k.`COL 2` = p.`COL 1` WHERE k.`COL 1` = ?");
    $stmt->execute([$kode]);
    $kabupaten = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$kabupaten) {
        die("Data tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmtDelete = $pdo->prepare("DELETE FROM kabupaten WHERE `COL 1` = ?");
        $stmtDelete->execute([$kode]);
        session_start();
        $_SESSION['toast'] = 'Data kabupaten berhasil dihapus.';
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal hapus: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hapus Kabupaten</title>
<link rel="icon" href="img/alvan_desain.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h2 class="card-title mb-0"><i class="bi bi-trash"></i> Hapus Kabupaten</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted">Apakah Anda yakin ingin menghapus data kabupaten berikut?</p>
                    <div class="mb-3">
                        <strong>Kode Kabupaten:</strong> <?= htmlspecialchars($kabupaten['kode_kabupaten']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Nama Kabupaten:</strong> <?= htmlspecialchars($kabupaten['nama_kabupaten']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Provinsi:</strong> <?= htmlspecialchars($kabupaten['nama_provinsi']) ?>
                    </div>
                    <form method="post">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-danger me-md-2" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                            <a href="index.php" class="btn btn-secondary" title="Batal">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>