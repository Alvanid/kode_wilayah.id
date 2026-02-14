<?php
require_once 'config.php';

// Ambil daftar provinsi untuk dropdown
$provStmt = $pdo->query("SELECT `COL 1` AS kode_provinsi, `COL 3` AS nama_provinsi FROM provinsi ORDER BY `COL 3` ASC");
$provinsis = $provStmt->fetchAll(PDO::FETCH_ASSOC);

session_start();
$toast = $_SESSION['toast'] ?? '';
unset($_SESSION['toast']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Kecamatan</title>
<link rel="icon" href="img/alvan_desain.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                    <div><i class="bi bi-plus-circle me-2"></i><span class="h6 mb-0">Tambah Kecamatan</span></div>
                    <a href="index.php" class="btn btn-sm btn-light" title="Kembali"><i class="bi bi-house"></i></a>
                </div>
                <div class="card-body">
                    <form method="post" action="simpan.php">
                        <div class="mb-3">
                            <label for="kode" class="form-label fw-bold">Kode Kecamatan</label>
                            <input type="text" class="form-control" id="kode" name="kode" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="provinsi" class="form-label fw-bold">Provinsi</label>
                                <select id="provinsi" name="provinsi" class="form-select">
                                    <option value="">Pilih Provinsi</option>
                                    <?php foreach($provinsis as $p): ?>
                                        <option value="<?= htmlspecialchars($p['kode_provinsi']) ?>"><?= htmlspecialchars($p['nama_provinsi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kabupaten" class="form-label fw-bold">Kabupaten</label>
                                <select id="kabupaten" name="kabupaten" class="form-select">
                                    <option value="">Pilih Kabupaten</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold">Nama Kecamatan</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success me-md-2" title="Simpan"><i class="bi bi-save"></i></button>
                            <a href="index.php" class="btn btn-secondary" title="Batal"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastTambah" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"><?= htmlspecialchars($toast) ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toastMsg = "<?= addslashes($toast) ?>";
if (toastMsg) { new bootstrap.Toast(document.getElementById('toastTambah'), { delay: 1500 }).show(); }
</script>
<script>
// Populate kabupaten when provinsi changes
document.getElementById('provinsi').addEventListener('change', function(){
    const prov = this.value;
    const kabSelect = document.getElementById('kabupaten');
    kabSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
    if(!prov) return;
    fetch('get_kabupaten.php?provinsi=' + encodeURIComponent(prov))
        .then(r => r.json())
        .then(data => {
            data.forEach(kb => {
                const opt = document.createElement('option');
                opt.value = kb.kode_kabupaten;
                opt.textContent = kb.nama_kabupaten;
                kabSelect.appendChild(opt);
            });
        })
        .catch(err => console.error(err));
});
</script>
</body>
</html>
