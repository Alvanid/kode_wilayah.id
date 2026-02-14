<?php
require_once 'config.php';

// Ambil daftar provinsi untuk dropdown
$provResult = $conn->query("SELECT `COL 1` AS kode_provinsi, `COL 3` AS nama_provinsi FROM provinsi ORDER BY `COL 3` ASC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Desa</title>
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
                    <div><i class="bi bi-plus-circle me-2"></i><span class="h6 mb-0">Tambah Desa</span></div>
                    <a href="index.php" class="btn btn-sm btn-light" title="Kembali"><i class="bi bi-house"></i></a>
                </div>
                <div class="card-body">
                    <form method="post" action="simpan.php">
                        <div class="mb-3">
                            <label for="kode_desa" class="form-label fw-bold">Kode Desa</label>
                            <input type="text" class="form-control" id="kode_desa" name="kode_desa" required>
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
                            <input type="text" class="form-control" id="nama_desa" name="nama_desa" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary me-md-2" title="Simpan" aria-label="Simpan"><i class="bi bi-save"></i></button>
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
document.getElementById('provinsi').addEventListener('change', function(){
    const prov = this.value;
    const kabSelect = document.getElementById('kabupaten');
    const kecSelect = document.getElementById('kecamatan');
    kabSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
    kecSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
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

document.getElementById('kabupaten').addEventListener('change', function(){
    const kab = this.value;
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
        })
        .catch(err => console.error(err));
});
</script>

</body>
</html>
