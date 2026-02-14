<?php
require_once 'config.php';

$kode = $_GET['kode'] ?? '';
if ($kode === '') {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT `COL 1`, `COL 3` FROM kecamatan WHERE `COL 1` = ?");
    $stmt->execute([$kode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $del = $pdo->prepare("DELETE FROM kecamatan WHERE `COL 1` = ?");
        $del->execute([$kode]);
        session_start();
        $_SESSION['toast'] = 'Data kecamatan berhasil dihapus.';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die('Gagal hapus: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hapus Kecamatan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between">
                    <div><i class="bi bi-trash me-2"></i><span class="h6 mb-0">Hapus Kecamatan</span></div>
                    <a href="index.php" class="btn btn-sm btn-light" title="Kembali"><i class="bi bi-house"></i></a>
                </div>
                <div class="card-body">
                    <p>Apakah Anda yakin ingin menghapus:</p>
                    <p class="fw-bold"><?= htmlspecialchars($row['COL 1']) ?> — <?= htmlspecialchars($row['COL 3']) ?></p>
                    <form method="post">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-danger me-md-2" title="Hapus"><i class="bi bi-trash"></i></button>
                            <a href="index.php" class="btn btn-secondary" title="Batal"><i class="bi bi-x-circle"></i></a>
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
