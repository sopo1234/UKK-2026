<?php
require '../includes/auth.php';
require '../config.php';
requireRole(['admin']);

$title = 'Kelola Kelas';
$section = 'admin';

include '../includes/header.php';
?>

<h1><?= htmlspecialchars($title) ?></h1>

<p>
    <a href="tambah_kelas.php">+ Tambah Kelas</a>
</p>

<table border="1">

    <tr>
        <th>No</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Status</th>
    </tr>

<?php

$rows = $pdo->query("
    SELECT
        id,
        nama,
        tingkat,
        jurusan,
        status_aktif
    FROM t_kelas
    ORDER BY id
")->fetchAll();

foreach ($rows as $i => $row):

?>

    <tr>

        <td>
            <?= $i + 1 ?>
        </td>

        <td>
            <?= htmlspecialchars($row['nama']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['tingkat']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['jurusan']) ?>
        </td>

        <td>
            <?= $row['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?>
        </td>

    </tr>

<?php endforeach; ?>

</table>

<?php include '../includes/footer.php'; ?>