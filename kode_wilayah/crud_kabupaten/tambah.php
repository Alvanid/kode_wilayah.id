<?php
require 'config.php';

// Ambil data provinsi untuk dropdown
$stmt = $pdo->query("SELECT `COL 1` AS kode_provinsi, `COL 3` AS nama_provinsi FROM provinsi ORDER BY `COL 3` ASC");
$provinsis = $stmt->fetchAll(PDO::FETCH_ASSOC);

session_start();
$toastMessage = $_SESSION['toast'] ?? '';
unset($_SESSION['toast']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Kabupaten</title>
<link rel="icon" href="img/alvan_desain.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h2 class="card-title mb-0"><i class="bi bi-plus-circle"></i> Tambah Kabupaten</h2>
                </div>
                <div class="card-body">
                    <form action="simpan.php" method="post">
                        <div class="mb-3">
                            <label for="kode" class="form-label fw-bold">Kode Kabupaten</label>
                            <input type="text" class="form-control" id="kode" name="kode" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold">Nama Kabupaten</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="provinsi" class="form-label fw-bold">Provinsi</label>
                            <select class="form-select" id="provinsi" name="provinsi" required>
                                <option value="">Pilih Provinsi</option>
                                <?php foreach($provinsis as $p): ?>
                                <option value="<?= htmlspecialchars($p['kode_provinsi']) ?>"><?= htmlspecialchars($p['nama_provinsi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success me-md-2" title="Simpan">
                                <i class="bi bi-save"></i>
                            </button>
                            <a href="index.php" class="btn btn-secondary" title="Kembali">
                                <i class="bi bi-house"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Success -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <?= htmlspecialchars($toastMessage) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tampilkan toast jika ada message
    const toastMessage = "<?= addslashes($toastMessage) ?>";
    if(toastMessage){
        const toastEl = document.getElementById('successToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }
</script>
</body>
</html>