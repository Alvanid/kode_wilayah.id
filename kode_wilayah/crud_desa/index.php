<?php
require_once "config.php";

/*
|--------------------------------------------------------------------------
| Konfigurasi
|--------------------------------------------------------------------------
*/

$limit = 25;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : "";


/*
|--------------------------------------------------------------------------
| WHERE search
|--------------------------------------------------------------------------
*/

$where = "";

if ($search != "") {

    $search_safe = $conn->real_escape_string($search);

    $where = "
    WHERE
        d.`COL 1` LIKE '%$search_safe%' OR
        d.`COL 3` LIKE '%$search_safe%' OR
        k.`COL 3` LIKE '%$search_safe%' OR
        kab.`COL 3` LIKE '%$search_safe%' OR
        prov.`COL 3` LIKE '%$search_safe%'
    ";
}


/*
|--------------------------------------------------------------------------
| Hitung total data
|--------------------------------------------------------------------------
*/

$count_sql = "
SELECT COUNT(*) as total
FROM desa d
LEFT JOIN kecamatan k ON LEFT(d.`COL 1`,7) = k.`COL 1`
LEFT JOIN kabupaten kab ON LEFT(k.`COL 1`,4) = kab.`COL 1`
LEFT JOIN provinsi prov ON LEFT(kab.`COL 1`,2) = prov.`COL 1`
$where
";

$count_result = $conn->query($count_sql);
$total_data = $count_result->fetch_assoc()['total'];

$total_page = ceil($total_data / $limit);


/*
|--------------------------------------------------------------------------
| Ambil data
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    d.`COL 1` AS kode_desa,
    d.`COL 3` AS nama_desa,
    k.`COL 3` AS nama_kecamatan,
    kab.`COL 3` AS nama_kabupaten,
    prov.`COL 3` AS nama_provinsi

FROM desa d

LEFT JOIN kecamatan k
ON LEFT(d.`COL 1`,7) = k.`COL 1`

LEFT JOIN kabupaten kab
ON LEFT(k.`COL 1`,4) = kab.`COL 1`

LEFT JOIN provinsi prov
ON LEFT(kab.`COL 1`,2) = prov.`COL 1`

$where

ORDER BY d.`COL 1` ASC

LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Data Desa Indonesia</title>

<link rel="icon" href="img/alvan_desain.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">


<div class="container mt-4">

<div class="card shadow-sm">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

<div>

<h1 class="fw-bold mb-0">
Data Desa
</h1>

<small class="text-muted">
Total Data: <?= number_format($total_data) ?>
</small>

</div>


<form method="GET" class="d-flex">

<input
type="text"
name="search"
class="form-control me-2"
placeholder="Cari kode atau nama wilayah"
value="<?= htmlspecialchars($search) ?>"
>

<button class="btn btn-primary me-2">
<i class="bi bi-search"></i>
</button>

<?php if($search != ""): ?>

<a href="index.php" class="btn btn-secondary">
<i class="bi bi-x-circle"></i>
</a>

<?php endif; ?>

</form>

</div>

</div>



<div class="table-responsive">

<table class="table table-bordered table-hover mb-0">

<thead class="table-dark">

<tr>

<th>Kode Desa</th>
<th>Nama Desa</th>
<th>Kecamatan</th>
<th>Kabupaten</th>
<th>Provinsi</th>
<th width="100" class="text-center">Aksi</th>

</tr>

</thead>

<tbody>

<?php if($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($row['kode_desa']) ?></td>

<td><?= htmlspecialchars($row['nama_desa']) ?></td>

<td><?= htmlspecialchars($row['nama_kecamatan']) ?></td>

<td><?= htmlspecialchars($row['nama_kabupaten']) ?></td>

<td><?= htmlspecialchars($row['nama_provinsi']) ?></td>

<td class="text-center">

<a href="edit.php?kode=<?= urlencode($row['kode_desa']) ?>"
class="text-warning me-2"
title="Edit">

<i class="bi bi-pencil-square fs-5"></i>

</a>

<a href="hapus.php?kode=<?= urlencode($row['kode_desa']) ?>"
class="text-danger"
title="Hapus"
onclick="return confirm('Yakin hapus data ini?')">

<i class="bi bi-trash fs-5"></i>

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="6" class="text-center">
Data tidak ditemukan
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>



<nav class="mt-4">

<ul class="pagination justify-content-center">

<?php if($page > 1): ?>

<li class="page-item">
<a class="page-link"
href="?page=1&search=<?= urlencode($search) ?>">
First
</a>
</li>

<li class="page-item">
<a class="page-link"
href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">
Prev
</a>
</li>

<?php endif; ?>


<?php

$start = max(1, $page - 3);
$end   = min($total_page, $page + 2);

for($i = $start; $i <= $end; $i++):

?>

<li class="page-item <?= ($i==$page)?'active':'' ?>">

<a class="page-link"
href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
<?= $i ?>
</a>

</li>

<?php endfor; ?>


<?php if($page < $total_page): ?>

<li class="page-item">
<a class="page-link"
href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">
Next
</a>
</li>

<li class="page-item">
<a class="page-link"
href="?page=<?= $total_page ?>&search=<?= urlencode($search) ?>">
Last
</a>
</li>

<?php endif; ?>

</ul>

</nav>

</div>



<a href="tambah.php"
class="btn btn-primary btn-lg position-fixed bottom-0 end-0 m-4 rounded-circle d-flex align-items-center justify-content-center"
title="Tambah Desa"
style="width:70px;height:70px;">

<i class="bi bi-plus-lg fs-3"></i>

</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
