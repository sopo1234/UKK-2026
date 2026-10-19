
<?php
session_start();

include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// AMBIL DATA GURU
$sql = "SELECT
            g.id,
            g.nip,
            g.nama,
            g.email,
            g.status_aktif,
            g.created_at,
            g.updated_at
        FROM t_guru g
        JOIN t_users u ON g.user_id = u.id
        WHERE u.role = 'guru'
        ORDER BY g.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data guru: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Guru</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- CSS DataTables -->
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
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
                   <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1>Kelola Guru</h1>

            <a href="dashboard.php">Kembali ke Dashboard</a>

            <br><br>

            <a href="tambah_guru.php"
               class="btn btn-primary mb-3">
                Tambah Guru
            </a>

            <hr>

            <h2>Data Guru</h2>

            <div class="table-responsive">

                <table id="tabelGuru"
                       class="table table-sm table-dark table-bordered">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    while ($guru = mysqli_fetch_assoc($hasil)) {
                    ?>

                        <tr>

                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($guru['nip'] ?? ''); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($guru['nama'] ?? ''); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($guru['email'] ?? ''); ?>
                            </td>

                            <td>
                                <?= ($guru['status_aktif'] ?? 0) == 1
                                    ? 'Aktif'
                                    : 'Tidak Aktif'; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($guru['created_at'] ?? ''); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($guru['updated_at'] ?? ''); ?>
                            </td>

                            <td>
                                <a href="edit_guru.php?id=<?= (int)$guru['id']; ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="hapus_guru.php?id=<?= (int)$guru['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus guru ini?')">
                                    Hapus
                                </a>
                            </td>

                        </tr>

                    <?php
                    }
                    ?>

                    </tbody>

                </table>

            </div>

        </main>

    </div>
</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelGuru').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data guru",
            zeroRecords: "Data guru tidak ditemukan",
            emptyTable: "Belum ada data guru",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

});
</script>

</body>
</html>