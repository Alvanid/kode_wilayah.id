<?php
require_once 'config.php';

// Pagination
$limit = 25;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search
$search = trim($_GET['search'] ?? '');
$searchParam = "%{$search}%";

// Handle tambah/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode_kecamatan'];
    $nama = $_POST['nama_kecamatan'];

    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM kecamatan WHERE `COL 1` = ?");
        $stmt->execute([$kode]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO kecamatan (`COL 1`, `COL 3`) VALUES (?, ?)");
            $stmt->execute([$kode, $nama]);
            $success = 'tambah';
        }
    } elseif (isset($_POST['edit'])) {
        $original_kode = $_POST['original_kode'];
        $stmt = $pdo->prepare("UPDATE kecamatan SET `COL 1` = ?, `COL 3` = ? WHERE `COL 1` = ?");
        $stmt->execute([$kode, $nama, $original_kode]);
        $success = 'edit';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM kecamatan WHERE `COL 1` = ?");
    $stmt->execute([$_GET['delete']]);
    $success = 'hapus';
}

// Handle edit (isi form)
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT `COL 1`, `COL 3` FROM kecamatan WHERE `COL 1` = ?");
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil data
$where = '';
$params = [];
if (!empty($search)) {
    $where = "WHERE k.`COL 1` LIKE ? OR k.`COL 3` LIKE ? OR kab.`COL 3` LIKE ? OR prov.`COL 3` LIKE ?";
    $params = [$searchParam, $searchParam, $searchParam, $searchParam];
}

$sql = "SELECT
            k.`COL 1` AS kode_kecamatan,
            k.`COL 3` AS nama_kecamatan,
            kab.`COL 3` AS nama_kabupaten,
            prov.`COL 3` AS nama_provinsi
        FROM kecamatan k
        LEFT JOIN kabupaten kab ON LEFT(k.`COL 1`,4) = kab.`COL 1`
        LEFT JOIN provinsi prov ON LEFT(kab.`COL 1`,2) = prov.`COL 1`
        $where
        ORDER BY k.`COL 1` ASC
        LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$kecamatans = $stmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM kecamatan k
            LEFT JOIN kabupaten kab ON LEFT(k.`COL 1`,4) = kab.`COL 1`
            LEFT JOIN provinsi prov ON LEFT(kab.`COL 1`,2) = prov.`COL 1` $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Kecamatan</title>
<link rel="icon" href="img/alvan_desain.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h1 class="mb-4 fw-bold">Data Kecamatan</h1>
    <p class="mb-3 text-muted">Total data: <strong><?= number_format((int)$total) ?></strong> entri</p>

    <div class="d-flex justify-content-end mb-3">
        <form class="d-flex" method="get" role="search">
            <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Cari kode/nama kecamatan, kabupaten, provinsi" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Kode Kecamatan</th>
                    <th>Nama Kecamatan</th>
                    <th>Nama Kabupaten</th>
                    <th>Nama Provinsi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($kecamatans as $k): ?>
                <tr>
                    <td><?= htmlspecialchars($k['kode_kecamatan']) ?></td>
                    <td><?= htmlspecialchars($k['nama_kecamatan']) ?></td>
                    <td><?= htmlspecialchars($k['nama_kabupaten']) ?></td>
                    <td><?= htmlspecialchars($k['nama_provinsi']) ?></td>
                    <td class="text-center">
                            <a href="edit.php?kode=<?= urlencode($k['kode_kecamatan']) ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-warning me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="hapus.php?kode=<?= urlencode($k['kode_kecamatan']) ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus data ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= min(4, $totalPages); $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($totalPages > 5): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <li class="page-item <?= $totalPages == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $totalPages ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $totalPages ?></a></li>
            <?php endif; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    <!-- Tombol floating + -->
    <a href="tambah.php" class="btn btn-primary btn-lg rounded-circle position-fixed bottom-0 end-0 m-4 shadow" title="Tambah Kecamatan">
        <i class="bi bi-plus-lg"></i>
    </a>
    <!-- Toast -->
    <?php if (isset($success)): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="toast" class="toast align-items-center text-white bg-<?= $success=='hapus'?'danger':'success' ?> border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $success=='tambah'?'Data berhasil ditambah':($success=='edit'?'Data berhasil diupdate':'Data berhasil dihapus') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <script>
            var toast = new bootstrap.Toast(document.getElementById('toast'), {delay: 1500});
            toast.show();
        </script>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
