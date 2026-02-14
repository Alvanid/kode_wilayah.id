<?php
require_once 'config.php';

if (!isset($_GET['kode'])) {
    header("Location: index.php");
    exit;
}

$kode = $_GET['kode'];
$stmt = $pdo->prepare("SELECT `COL 1`, `COL 3` FROM kecamatan WHERE `COL 1` = ?");
$stmt->execute([$kode]);
$kecamatan = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$kecamatan) {
    header("Location: index.php");
    exit;
}

// Tentukan kode kabupaten dari kode kecamatan (prefix 4 char)
$kode_kabupaten = substr($kecamatan['COL 1'], 0, 4);

// Ambil kode provinsi dari tabel kabupaten
$stmt2 = $pdo->prepare("SELECT `COL 2` AS kode_provinsi FROM kabupaten WHERE `COL 1` = ?");
$stmt2->execute([$kode_kabupaten]);
$kabRow = $stmt2->fetch(PDO::FETCH_ASSOC);
$selectedProv = $kabRow['kode_provinsi'] ?? '';

// Ambil semua provinsi untuk dropdown
$provStmt = $pdo->query("SELECT `COL 1` AS kode_provinsi, `COL 3` AS nama_provinsi FROM provinsi ORDER BY `COL 3` ASC");
$provinsis = $provStmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil kabupaten untuk provinsi terpilih
$kabupatens = [];
if ($selectedProv) {
    $kabStmt = $pdo->prepare("SELECT `COL 1` AS kode_kabupaten, `COL 3` AS nama_kabupaten FROM kabupaten WHERE `COL 2` = ? ORDER BY `COL 3` ASC");
    $kabStmt->execute([$selectedProv]);
    $kabupatens = $kabStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Kecamatan</title>
<link rel="icon" href="img/alvan_desain.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-pencil-square me-2"></i>
                        <span class="h6 mb-0">Edit Kecamatan</span>
                    </div>
                    <a href="index.php" class="btn btn-sm btn-light" title="Kembali"><i class="bi bi-house"></i></a>
                </div>
                <div class="card-body">
                    <form method="post" action="update.php">
                        <input type="hidden" name="original_kode" value="<?= htmlspecialchars($kecamatan['COL 1']) ?>">
                        <div class="mb-3">
                            <label for="kode" class="form-label fw-bold">Kode Kecamatan</label>
                            <input type="text" class="form-control" id="kode" name="kode_kecamatan" value="<?= htmlspecialchars($kecamatan['COL 1']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="provinsi" class="form-label fw-bold">Provinsi</label>
                                <select id="provinsi" name="provinsi" class="form-select">
                                    <option value="">Pilih Provinsi</option>
                                    <?php foreach($provinsis as $p): ?>
                                        <option value="<?= htmlspecialchars($p['kode_provinsi']) ?>" <?= $p['kode_provinsi'] == $selectedProv ? 'selected' : '' ?>><?= htmlspecialchars($p['nama_provinsi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kabupaten" class="form-label fw-bold">Kabupaten</label>
                                <select id="kabupaten" name="kabupaten" class="form-select">
                                    <option value="">Pilih Kabupaten</option>
                                    <?php foreach($kabupatens as $kb): ?>
                                        <option value="<?= htmlspecialchars($kb['kode_kabupaten']) ?>" <?= $kb['kode_kabupaten'] == $kode_kabupaten ? 'selected' : '' ?>><?= htmlspecialchars($kb['nama_kabupaten']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold">Nama Kecamatan</label>
                            <input type="text" class="form-control" id="nama" name="nama_kecamatan" value="<?= htmlspecialchars($kecamatan['COL 3']) ?>" required>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary me-md-2" title="Simpan"><i class="bi bi-save"></i></button>
                            <a href="index.php" class="btn btn-secondary" title="Batal"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="toastEdit" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Data berhasil diupdate!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($_GET['success'])): ?>
<script>
    var toast = new bootstrap.Toast(document.getElementById('toastEdit'), {delay: 1500});
    toast.show();
</script>
<?php endif; ?>
<script>
// Load kabupaten saat provinsi berubah
document.getElementById('provinsi').addEventListener('change', function(){
    const prov = this.value;
    const kabSelect = document.getElementById('kabupaten');
    // kosongkan
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
