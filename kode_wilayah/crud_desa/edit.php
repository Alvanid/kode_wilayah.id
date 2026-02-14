<?php
require_once 'config.php';

$kode = isset($_GET['kode']) ? $conn->real_escape_string($_GET['kode']) : '';
if ($kode === '') {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT 
    d.`COL 1` AS kode_desa,
    d.`COL 3` AS nama_desa,
    k.`COL 1` AS kode_kecamatan,
    kab.`COL 1` AS kode_kabupaten,
    prov.`COL 1` AS kode_provinsi
FROM desa d
LEFT JOIN kecamatan k ON LEFT(d.`COL 1`,7) = k.`COL 1`
LEFT JOIN kabupaten kab ON LEFT(k.`COL 1`,4) = kab.`COL 1`
LEFT JOIN provinsi prov ON LEFT(kab.`COL 1`,2) = prov.`COL 1`
WHERE d.`COL 1` = ?
LIMIT 1");
$stmt->bind_param('s', $kode);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header('Location: index.php');
    exit;
}
$row = $res->fetch_assoc();

// Provinsi list
$provResult = $conn->query("SELECT `COL 1` AS kode_provinsi, `COL 3` AS nama_provinsi FROM provinsi ORDER BY `COL 3` ASC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Desa</title>
    <link rel="icon" href="img/alvan_desain.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark d-flex align-items-center justify-content-between">
                    <div><i class="bi bi-pencil-square me-2"></i><span class="h6 mb-0">Edit Desa</span></div>
                    <a href="index.php" class="btn btn-sm btn-light" title="Kembali"><i class="bi bi-house"></i></a>
                </div>
                <div class="card-body">
                    <form method="post" action="update.php">
                        <input type="hidden" name="original_kode" value="<?= htmlspecialchars($row['kode_desa']) ?>">

                        <div class="mb-3">
                            <label for="kode_desa" class="form-label fw-bold">Kode Desa</label>
                            <input type="text" class="form-control" id="kode_desa" name="kode_desa" value="<?= htmlspecialchars($row['kode_desa']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="provinsi" class="form-label fw-bold">Provinsi</label>
                                <select id="provinsi" name="provinsi" class="form-select">
                                    <option value="">Pilih Provinsi</option>
                                    <?php while($p = $provResult->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($p['kode_provinsi']) ?>"><?= htmlspecialchars($p['nama_provinsi']) ?></option>
                                    <?php endwhile; ?>
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
                            <label for="kecamatan" class="form-label fw-bold">Kecamatan</label>
                            <select id="kecamatan" name="kecamatan" class="form-select">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nama_desa" class="form-label fw-bold">Nama Desa</label>
                            <input type="text" class="form-control" id="nama_desa" name="nama_desa" value="<?= htmlspecialchars($row['nama_desa']) ?>" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-warning me-md-2" title="Update" aria-label="Update"><i class="bi bi-save"></i></button>
                            <a href="index.php" class="btn btn-secondary" title="Batal" aria-label="Batal"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const existingProv = <?= json_encode($row['kode_provinsi']) ?>;
const existingKab  = <?= json_encode($row['kode_kabupaten']) ?>;
const existingKec  = <?= json_encode($row['kode_kecamatan']) ?>;

function loadKabupaten(prov, selected=null) {
    const kabSelect = document.getElementById('kabupaten');
    kabSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
    document.getElementById('kecamatan').innerHTML = '<option value="">Pilih Kecamatan</option>';
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
            if (selected) kabSelect.value = selected;
            if (kabSelect.value) loadKecamatan(kabSelect.value, existingKec);
        })
        .catch(err => console.error(err));
}

function loadKecamatan(kab, selected=null) {
    const kecSelect = document.getElementById('kecamatan');
    kecSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
    if(!kab) return;
    fetch('get_kecamatan.php?kabupaten=' + encodeURIComponent(kab))
        .then(r => r.json())
        .then(data => {
            data.forEach(kc => {
                const opt = document.createElement('option');
                opt.value = kc.kode_kecamatan;
                opt.textContent = kc.nama_kecamatan;
                kecSelect.appendChild(opt);
            });
            if (selected) kecSelect.value = selected;
        })
        .catch(err => console.error(err));
}

document.getElementById('provinsi').addEventListener('change', function(){
    loadKabupaten(this.value);
});

document.getElementById('kabupaten').addEventListener('change', function(){
    loadKecamatan(this.value);
});

// Initialize selects with existing values
if (existingProv) {
    document.getElementById('provinsi').value = existingProv;
    loadKabupaten(existingProv, existingKab);
}

</script>

</body>
</html>
