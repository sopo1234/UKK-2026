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

$query = "SELECT wk.*,
                 g.nama AS nama_guru,
                 k.nama AS nama_kelas,
                 k.tingkat,
                 k.jurusan,
                 ta.nama AS tahun_ajaran
          FROM t_wali_kelas wk
          LEFT JOIN t_guru g ON wk.guru_id = g.id
          LEFT JOIN t_kelas k ON wk.kelas_id = k.id
          LEFT JOIN t_tahun_ajaran ta
              ON wk.tahun_ajaran_id = ta.id
          ORDER BY wk.id DESC";

$data = mysqli_query($koneksi, $query);

if (!$data) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Wali Kelas</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

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

        .table-responsive {
            margin-top: 20px;
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

            <h2>Data Wali Kelas</h2>

            <a href="dashboard.php">Kembali ke Dashboard</a>
            |
            <a href="tambah_walikelas.php">Tambah Wali Kelas</a>

            <div class="table-responsive">
                <table id="tabelWaliKelas"
                       class="table table-bordered table-striped table-hover"
                       style="width:100%">

                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Guru</th>
                            <th>Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;

                    while ($row = mysqli_fetch_assoc($data)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['nama_guru'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['nama_kelas'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                                (<?= htmlspecialchars(
                                    $row['tingkat'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                                <?= htmlspecialchars(
                                    $row['jurusan'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>)
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['tahun_ajaran'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['tanggal_mulai'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['tanggal_selesai'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </td>

                            <td>
                                <?= ($row['status_aktif'] ?? 0) == 1
                                    ? 'Aktif'
                                    : 'Tidak Aktif'; ?>
                            </td>

                            <td>
                                <a class="btn btn-warning btn-sm"
                                   href="edit_walikelas.php?id=<?= (int)$row['id']; ?>">
                                    Edit
                                </a>

                                <a class="btn btn-danger btn-sm"
                                   href="hapus_walikelas.php?id=<?= (int)$row['id']; ?>"
                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>
            </div>

        </main>
    </div>
</div>

<!-- JavaScript DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelWaliKelas').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data wali kelas",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        order: []
    });
});
</script>

</body>
</html>