<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    exit("Akses ditolak!");
}

// Menu aktif
$activeMenu = 'siswa';

// Ambil data siswa
$sql = "SELECT * FROM t_siswa ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die(
        "Gagal mengambil data siswa: " .
        htmlspecialchars(mysqli_error($koneksi), ENT_QUOTES, 'UTF-8')
    );
}

// Mengamankan output
function h($nilai) {
    return htmlspecialchars(
        (string)($nilai ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f5f7f9;
        }

        .sidebar,
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 253px;
            max-width: 253px;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .app-main {
            display: block;
            width: auto;
            min-width: 0;
            min-height: 100vh;
            margin-left: 253px;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 28px;
            margin-bottom: 12px;
        }

        .table-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            width: 100%;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        #tabelSiswa {
            width: 100% !important;
            white-space: nowrap;
        }

        #tabelSiswa th,
        #tabelSiswa td {
            vertical-align: middle;
        }

        .aksi {
            display: flex;
            gap: 5px;
        }

        @media (max-width: 767.98px) {
            .sidebar,
            .app-sidebar {
                position: relative;
                width: 100%;
                max-width: 100%;
                height: auto;
                min-height: 0;
            }

            .app-main {
                width: 100%;
                margin-left: 0;
                padding: 18px 12px;
            }

            .table-card {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR BERSAMA -->
<?php require_once __DIR__ . '/layout/sidebar.php'; ?>

<!-- KONTEN UTAMA -->
<main class="app-main">

    <div class="page-header">
        <h1>Kelola Siswa</h1>

        <a href="dashboard.php">Kembali ke Dashboard</a>

        <div class="mt-3">
            <a href="tambah_siswa.php" class="btn btn-primary">
                Tambah Siswa
            </a>
        </div>
    </div>

    <div class="table-card shadow-sm">

        <h5 class="mb-3">Daftar Data Siswa</h5>

        <div class="table-responsive">
            <table id="tabelSiswa"
                   class="table table-striped table-bordered table-hover table-sm">

                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $no = 1;

                while ($siswa = mysqli_fetch_assoc($hasil)) {
                ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td><?= h($siswa['nis'] ?? '-'); ?></td>

                        <td><?= h($siswa['nisn'] ?? '-'); ?></td>

                        <td><?= h($siswa['nama'] ?? '-'); ?></td>

                        <td>
                            <?php
                            $jk = $siswa['jenis_kelamin'] ?? '';

                            if ($jk === 'L') {
                                echo 'Laki-laki';
                            } elseif ($jk === 'P') {
                                echo 'Perempuan';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>

                        <td><?= h($siswa['tanggal_lahir'] ?? '-'); ?></td>

                        <td><?= h($siswa['alamat'] ?? '-'); ?></td>

                        <td>
                            <?php if ((int)($siswa['status_aktif'] ?? 0) === 1) { ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php } else { ?>
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            <?php } ?>
                        </td>

                        <td><?= h($siswa['created_at'] ?? '-'); ?></td>

                        <!-- Diperbaiki: tidak memunculkan warning jika kolom tidak tersedia -->
                        <td><?= h($siswa['updated_at'] ?? '-'); ?></td>

                        <td>
                            <div class="aksi">
                                <a href="edit_siswa.php?id=<?= (int)$siswa['id']; ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="hapus_siswa.php?id=<?= (int)$siswa['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</main>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelSiswa').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data siswa",
            zeroRecords: "Data siswa tidak ditemukan",
            emptyTable: "Belum ada data siswa",
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
