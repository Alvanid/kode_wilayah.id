<?php
require 'config.php';

session_start();

// Ambil kode kabupaten dari query string
$kode = isset($_GET['kode']) ? trim($_GET['kode']) : '';

if ($kode === '') {
    header("Location: index.php");
    exit;
}

try {
    // Ambil data kabupaten beserta nama provinsi
    $stmt = $pdo->prepare("
        SELECT 
            k.`COL 1` AS kode_kabupaten, 
            k.`COL 3` AS nama_kabupaten, 
            k.`COL 2` AS kode_provinsi, 
            p.`COL 3` AS nama_provinsi 
        FROM kabupaten k 
        LEFT JOIN provinsi p ON k.`COL 2` = p.`COL 1` 
        WHERE k.`COL 1` = ?
    ");

    $stmt->execute([$kode]);
    $kabupaten = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kabupaten) {
        die("Data tidak ditemukan.");
    }

    // Ambil daftar provinsi untuk dropdown
    $stmtProv = $pdo->query("
        SELECT 
            `COL 1` AS kode_provinsi, 
            `COL 3` AS nama_provinsi 
        FROM provinsi 
        ORDER BY `COL 3` ASC
    ");

    $provinsis = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error database: " . $e->getMessage());
}

// Toast message
$toastMessage = isset($_SESSION['toast']) ? $_SESSION['toast'] : '';
unset($_SESSION['toast']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kabupaten</title>
    <link rel="icon" href="img/alvan_desain.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    <h1 class="card-title mb-0 fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit Kabupaten
                    </h1>
                </div>

                <div class="card-body">
                    <form action="update.php" method="post">

                        <input type="hidden" name="kode_lama"
                            value="<?= htmlspecialchars($kabupaten['kode_kabupaten'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode Kabupaten</label>
                            <input type="text"
                                name="kode"
                                class="form-control"
                                value="<?= htmlspecialchars($kabupaten['kode_kabupaten'], ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kabupaten</label>
                            <input type="text"
                                name="nama"
                                class="form-control"
                                value="<?= htmlspecialchars($kabupaten['nama_kabupaten'], ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Provinsi</label>
                            <select name="provinsi" class="form-select" required>

                                <option value="">Pilih Provinsi</option>

                                <?php foreach ($provinsis as $p): ?>

                                    <option value="<?= htmlspecialchars($p['kode_provinsi'], ENT_QUOTES, 'UTF-8') ?>"
                                        <?= ($p['kode_provinsi'] == $kabupaten['kode_provinsi']) ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($p['nama_provinsi'], ENT_QUOTES, 'UTF-8') ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">

                            <button type="submit"
                                class="btn btn-primary"
                                title="Update">

                                <i class="bi bi-save"></i>

                            </button>

                            <a href="index.php"
                                class="btn btn-secondary"
                                title="Kembali">

                                <i class="bi bi-house"></i>

                            </a>

                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast -->
    <?php if ($toastMessage): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:11">

        <div id="successToast"
            class="toast align-items-center text-white bg-success border-0">

            <div class="d-flex">

                <div class="toast-body">
                    <?= htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>

                <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast">
                </button>

            </div>

        </div>

    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($toastMessage): ?>
<script>

const toastEl = document.getElementById('successToast');

if (toastEl) {

    const toast = new bootstrap.Toast(toastEl, {
        delay: 3000
    });

    toast.show();

}

</script>
<?php endif; ?>

</body>
</html>
