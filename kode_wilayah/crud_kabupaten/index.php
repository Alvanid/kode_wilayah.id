<?php
require_once 'config.php';

// --- Konfigurasi pagination ---
$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// --- Search ---
$search = trim($_GET['search'] ?? '');
$searchParam = "%$search%";
$searchQuery = $search ? '&search=' . urlencode($search) : '';

// --- Hitung total data ---
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM kabupaten k
    LEFT JOIN provinsi p ON k.`COL 2` = p.`COL 1`
    WHERE k.`COL 1` LIKE ? OR k.`COL 3` LIKE ? OR p.`COL 3` LIKE ?
");
$totalStmt->execute([$searchParam, $searchParam, $searchParam]);
$totalData = $totalStmt->fetchColumn();
$totalPages = ceil($totalData / $limit);

// --- Ambil data sesuai page dan search ---
$stmt = $pdo->prepare("
    SELECT 
        k.`COL 1` AS kode_kabupaten,
        k.`COL 3` AS nama_kabupaten,
        p.`COL 3` AS nama_provinsi
    FROM kabupaten k
    LEFT JOIN provinsi p ON k.`COL 2` = p.`COL 1`
    WHERE k.`COL 1` LIKE ? OR k.`COL 3` LIKE ? OR p.`COL 3` LIKE ?
    ORDER BY k.`COL 1` ASC
    LIMIT $limit OFFSET $offset
");
$stmt->execute([$searchParam, $searchParam, $searchParam]);
$kabupatens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Ambil pesan toast dari query string ---
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kabupaten</title>
    <link rel="icon" href="img/alvan_desain.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">

    <!-- Heading -->
    <h1 class="mb-4 fw-bold">Data Kabupaten</h1>

    <!-- Search -->
    <div class="d-flex justify-content-end mb-3">
        <form class="d-flex" method="get">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                   class="form-control me-2" placeholder="Cari kode/nama kabupaten atau provinsi">
            <button type="submit" class="btn btn-secondary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <!-- Total Data -->
    <p class="mb-3 text-muted">Total data: <strong><?= number_format((int)$totalData) ?></strong> entri</p>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Kode Kabupaten</th>
                    <th>Nama Kabupaten</th>
                    <th>Nama Provinsi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($kabupatens) > 0): ?>
                    <?php foreach($kabupatens as $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($k['kode_kabupaten']) ?></td>
                            <td><?= htmlspecialchars($k['nama_kabupaten']) ?></td>
                            <td><?= htmlspecialchars($k['nama_provinsi']) ?></td>
                            <td class="text-center">
                                          <a href="edit.php?kode=<?= urlencode($k['kode_kabupaten']) ?><?= $searchQuery ?>" 
                                              class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                          <a href="hapus.php?kode=<?= urlencode($k['kode_kabupaten']) ?><?= $searchQuery ?>" 
                                              class="btn btn-sm btn-danger" title="Hapus" 
                                              onclick="return confirm('Hapus data ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

  <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?page=<?= $page - 1 ?><?= $searchQuery ?>" tabindex="-1">Previous</a>
            </li>
            <?php
            // Tampilkan halaman awal
            for ($i = 1; $i <= min(4, $totalPages); $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?page=<?= $i ?><?= $searchQuery ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($totalPages > 5): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li class="page-item <?= $totalPages == $page ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?page=<?= $totalPages ?><?= $searchQuery ?>"><?= $totalPages ?></a>
                </li>
            <?php elseif ($totalPages > 4): ?>
                <li class="page-item <?= $totalPages == $page ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                </li>
            <?php endif; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?page=<?= $page + 1 ?><?= $searchQuery ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

    <!-- Tombol Floating + -->
    <a href="tambah.php" class="btn btn-primary btn-lg rounded-circle position-fixed bottom-0 end-0 m-4 shadow" 
       title="Tambah Kabupaten">
        <i class="bi bi-plus-lg"></i>
    </a>

    <!-- Toast Success -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <?= htmlspecialchars($message) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tampilkan toast jika ada message
    const message = <?= json_encode($message) ?>;
    if (message) {
        const toastEl = document.getElementById('successToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 1500 });
        toast.show();
    }
</script>
</body>
</html>
