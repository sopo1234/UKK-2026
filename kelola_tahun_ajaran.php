```php
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$sql = "SELECT * FROM t_tahun_ajaran ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Tahun Ajaran</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        main {
            margin-left: 16.666667%;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h1>Kelola Tahun Ajaran</h1>

            <a href="dashboard.php">Kembali ke Dashboard</a>
            <br><br>

            <a href="tambah_tahun_ajaran.php">Tambah Tahun Ajaran</a>
            <hr>

            <table id="tabelTahunAjaran" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Tahun Ajaran</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($hasil)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['nama'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($data['tanggal_mulai'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($data['tanggal_selesai'] ?? '-'); ?></td>
                        <td>
                            <?php echo ($data['status_aktif'] ?? 0) == 1
                                ? 'Aktif' : 'Tidak Aktif'; ?>
                        </td>
                        <td><?php echo htmlspecialchars($data['created_at'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($data['updated_at'] ?? '-'); ?></td>
                        <td>
                            <a href="edit_tahun_ajaran.php?id=<?php echo (int)$data['id']; ?>">Edit</a>
                            |
                            <a href="hapus_tahun_ajaran.php?id=<?php echo (int)$data['id']; ?>"
                               onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelTahunAjaran').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data tahun ajaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: []
    });
});
</script>

</body>
</html>
```