<?php
require '../includes/auth.php';
require '../config.php';
requireRole(['admin']);

$title = 'Kelola Guru';
$section = 'admin';

include '../includes/header.php';
?>

<h1><?= htmlspecialchars($title) ?></h1>

<p>
    <a href="tambah_guru.php">+ Tambah Guru</a>
</p>

<table border="1">
    <tr>
        <th>No</th>
        <th>NIS</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
    </tr>

<?php
$rows = $pdo->query("
    SELECT
        id,
        nip,
        nama,
        email,
        status_aktif
    FROM t_guru
    ORDER BY id DESC
")->fetchAll();

foreach ($rows as $i => $row):
?>

    <tr>
        <td><?= $i + 1 ?></td>

        <td>
            <?= htmlspecialchars($row['nip']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['nama']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['email']) ?>
        </td>

        <td>
            <?= $row['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?>
        </td>
    </tr>

<?php endforeach; ?>

</table>

<?php include '../includes/footer.php'; ?>