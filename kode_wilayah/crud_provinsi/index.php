<?php
require_once 'config.php';

// Helper function untuk URL dengan parameter
function buildUrl($page = null, $search = null) {
    $params = [];
    if ($page !== null) $params['page'] = $page;
    if ($search !== null && !empty($search)) $params['search'] = $search;
    return '?' . http_build_query($params);
}

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchQuery = $search ? '&search=' . urlencode($search) : '';

// Pagination
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

// Ambil total data (dengan filter search)
$whereClause = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $whereClause = "WHERE `COL 1` LIKE '%$search%' OR `COL 3` LIKE '%$search%'";
}
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM provinsi $whereClause");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data per halaman (dengan filter search)
$sql = "SELECT `COL 1`, `COL 3` FROM provinsi $whereClause ORDER BY `COL 1` ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Provinsi</title>
    <link rel="icon" href="img/alvan_desain.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="p-4">
<div class="container">
    <h1 class="fw-bold mb-4">Daftar Provinsi Indonesia</h1>
    
<!-- Form Search Kecil di Kanan -->
<form method="GET" class="d-flex justify-content-end mb-3 w-auto" role="search">
    <input class="form-control form-control-sm me-2" type="search" name="search" 
           placeholder="Cari kode/nama..." 
           value="<?= htmlspecialchars($search) ?>" aria-label="Search">
    <button class="btn btn-outline-success btn-sm" type="submit">
        <i class="bi bi-search"></i>
    </button>
    <?php if (!empty($search)): ?>
        <a href="?" class="btn btn-outline-secondary btn-sm ms-2" title="Bersihkan pencarian">
            <i class="bi bi-x-circle"></i>
            <span class="visually-hidden">Bersihkan pencarian</span>
        </a>
    <?php endif; ?>
</form>

 <small class="text-muted">Jumlah data: <strong><?= number_format((int)$totalData) ?></strong> entri</small>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 60%;">Nama Provinsi</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            $kode = htmlspecialchars($row["COL 1"]);
                            $nama = htmlspecialchars($row["COL 3"], ENT_QUOTES);
                        ?>
                        <tr>
                            <td class="text-center"><?= $kode ?></td>
                            <td><?= $nama ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-kode="<?= $kode ?>"
                                    data-nama="<?= $nama ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                    <span class="visually-hidden">Edit</span>
                                </button>
                                <a href="hapus.php?kode=<?= $kode ?><?= $searchQuery ?>" class="btn btn-sm btn-danger ms-1"
                                   onclick="return confirm('Yakin ingin menghapus provinsi ini?');" title="Hapus">
                                   <i class="bi bi-trash-fill"></i>
                                   <span class="visually-hidden">Hapus</span>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center fw-bold">Data tidak ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php mysqli_close($conn); ?>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= buildUrl($page-1, $search) ?>" tabindex="-1" title="Previous">
                            <i class="bi bi-chevron-left"></i>
                            <span class="visually-hidden">Prev</span>
                    </a>
                </li>
        <?php for($p=1; $p<=$totalPages; $p++): ?>
            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildUrl($p, $search) ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= buildUrl($page+1, $search) ?>" title="Next">
                            <i class="bi bi-chevron-right"></i>
                            <span class="visually-hidden">Next</span>
                    </a>
                </li>
      </ul>
    </nav>
</div>

<!-- Tombol floating + modern -->
    <button type="button" class="btn btn-primary btn-lg rounded-circle position-fixed bottom-0 end-0 m-4 shadow"
        data-bs-toggle="modal" data-bs-target="#modalTambah" title="Tambah">
        <i class="bi bi-plus-lg"></i>
        <span class="visually-hidden">Tambah</span>
    </button>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="simpan.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Provinsi Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
              <label for="kode_tambah" class="form-label">Kode Provinsi</label>
              <input type="text" class="form-control" id="kode_tambah" name="kode" required>
          </div>
          <div class="mb-3">
              <label for="nama_tambah" class="form-label">Nama Provinsi</label>
              <input type="text" class="form-control" id="nama_tambah" name="nama" required>
          </div>
      </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" title="Simpan">
                        <i class="bi bi-check2"></i>
                        <span class="visually-hidden">Simpan</span>
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Batal">
                        <i class="bi bi-x-lg"></i>
                        <span class="visually-hidden">Batal</span>
                </button>
            </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="update.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Provinsi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="kode_lama" id="kode_lama">
          <div class="mb-3">
              <label for="kode_edit" class="form-label">Kode Provinsi</label>
              <input type="text" class="form-control" id="kode_edit" name="kode" required>
          </div>
          <div class="mb-3">
              <label for="nama_edit" class="form-label">Nama Provinsi</label>
              <input type="text" class="form-control" id="nama_edit" name="nama" required>
          </div>
      </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" title="Update">
                        <i class="bi bi-save"></i>
                        <span class="visually-hidden">Update</span>
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Batal">
                        <i class="bi bi-x-lg"></i>
                        <span class="visually-hidden">Batal</span>
                </button>
            </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Isi data form edit saat tombol Edit diklik
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const kode = this.getAttribute('data-kode');
        const nama = this.getAttribute('data-nama');
        document.getElementById('kode_lama').value = kode;
        document.getElementById('kode_edit').value = kode;
        document.getElementById('nama_edit').value = nama;
    });
});
</script>
</body>
</html>
