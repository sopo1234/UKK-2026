<?php
session_start();
require_once 'config/koneksi.php';

// Periksa login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Periksa hak akses admin
if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    exit("Akses ditolak!");
}

// Amankan output
function h($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

// Ambil siswa aktif
$siswa = mysqli_query($koneksi, "
    SELECT id, nisn, nama
    FROM t_siswa
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// Ambil tahun ajaran aktif
$tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC
");

// Ambil kelas aktif
$kelas = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// Ambil data penempatan siswa
$sql = "
    SELECT
        ks.id,
        s.nisn,
        s.nama AS nama_siswa,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        ks.tanggal_mulai,
        ks.tanggal_selesai,
        ks.status_aktif
    FROM t_kelas_siswa ks
    LEFT JOIN t_siswa s
        ON ks.siswa_id = s.id
    LEFT JOIN t_tahun_ajaran ta
        ON ks.tahun_ajaran_id = ta.id
    LEFT JOIN t_kelas k
        ON ks.kelas_id = k.id
    ORDER BY ks.id DESC
";

$hasil = mysqli_query($koneksi, $sql);

// Periksa query
if (!$siswa || !$tahun || !$kelas || !$hasil) {
    die("Query database gagal: " . h(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Penempatan Siswa</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background: #f4f7f9;
        }

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
            width: 83.333333%;
        }

        .konten {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            main {
                margin-left: 0;
                width: 100%;
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
    <div class="konten">

        <h2>Penempatan Siswa</h2>
        <p>Kelola penempatan siswa berdasarkan kelas dan tahun ajaran.</p>

        <a href="dashboard.php" class="btn btn-secondary mb-3">
            Kembali ke Dashboard
        </a>

        <!-- PESAN SUKSES -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success">
                <?= h($_SESSION['pesan_sukses']); ?>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        <!-- PESAN ERROR -->
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger">
                <?= h($_SESSION['pesan_error']); ?>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <hr>

        <h4>Form Penempatan Siswa</h4>

        <form action="proses_simpan_penempatan.php" method="POST">

            <div class="form-group">
                <label for="siswa_id">Siswa</label>
                <select name="siswa_id" id="siswa_id"
                        class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>

                    <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                        <option value="<?= h($s['id']); ?>">
                            <?= h($s['nis'] . ' - ' . $s['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tahun_ajaran_id">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id"
                        class="form-control" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>

                    <?php while ($ta = mysqli_fetch_assoc($tahun)): ?>
                        <option value="<?= h($ta['id']); ?>">
                            <?= h($ta['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id"
                        class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>

                    <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                        <option value="<?= h($k['id']); ?>">
                            <?= h(
                                $k['nama'] . ' - Tingkat ' .
                                $k['tingkat'] . ' - ' . $k['jurusan']
                            ); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai"
                       id="tanggal_mulai" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai"
                       id="tanggal_selesai" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="status_aktif">Status</label>
                <select name="status_aktif" id="status_aktif"
                        class="form-control" required>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Simpan Penempatan
            </button>
        </form>

        <hr>

        <h4>Data Penempatan Siswa</h4>

        <div class="table-responsive">
            <table id="tabelPenempatan"
                   class="table table-bordered table-striped table-hover">

                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Tahun Ajaran</th>
                        <th>Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php $no = 1; ?>

                <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= h($row['nisn']); ?></td>
                        <td><?= h($row['nama_siswa']); ?></td>
                        <td><?= h($row['tahun_ajaran']); ?></td>
                        <td><?= h($row['nama_kelas']); ?></td>
                        <td><?= h($row['tingkat']); ?></td>
                        <td><?= h($row['jurusan']); ?></td>
                        <td><?= h($row['tanggal_mulai']); ?></td>
                        <td><?= h($row['tanggal_selesai']); ?></td>
                        <td>
                            <?= (int)$row['status_aktif'] === 1
                                ? 'Aktif'
                                : 'Tidak Aktif'; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>
        </div>

    </div>
    </main>

</div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPenempatan').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data penempatan siswa",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
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
