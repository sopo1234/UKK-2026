<?php
session_start();
require_once 'config/koneksi.php';

// Pemeriksaan login dan hak akses
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    exit('Akses ditolak!');
}

// Menu aktif untuk sidebar bersama
$activeMenu = 'pelanggaran';

// Fungsi pengaman output HTML
function h($nilai)
{
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES, 'UTF-8');
}

$pesan = '';

// Pesan setelah proses tambah/edit/hapus
if (isset($_GET['pesan'])) {
    $daftarPesan = [
        'tambah' => 'Data pelanggaran berhasil ditambahkan.',
        'edit'   => 'Data pelanggaran berhasil diperbarui.',
        'hapus'  => 'Data pelanggaran berhasil dihapus.'
    ];
    $pesan = $daftarPesan[$_GET['pesan']] ?? '';
}

// Proses tambah pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kategori_id = trim((string)($_POST['pelanggaran_kategori_id'] ?? ''));
    $kategori_id = $kategori_id === '' ? null : (int)$kategori_id;
    $kode = trim((string)($_POST['kode'] ?? ''));
    $kode = $kode === '' ? null : $kode;
    $nama = trim((string)($_POST['nama'] ?? ''));
    $poinInput = trim((string)($_POST['poin'] ?? ''));
    $deskripsi = trim((string)($_POST['deskripsi'] ?? ''));
    $status = (int)($_POST['status_aktif'] ?? 1);

    if ($nama === '') {
        $pesan = 'Nama pelanggaran wajib diisi.';
    } elseif ($poinInput === '' || !is_numeric($poinInput) || (int)$poinInput < 0) {
        $pesan = 'Poin harus berupa angka nol atau lebih.';
    } else {
        $poin = (int)$poinInput;
        $status = in_array($status, [0, 1], true) ? $status : 1;

        $stmt = mysqli_prepare(
            $koneksi,
            'INSERT INTO t_pelanggaran
             (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                'issisi',
                $kategori_id,
                $kode,
                $nama,
                $poin,
                $deskripsi,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: kelola_pelanggaran.php?pesan=tambah');
                exit;
            }

            $pesan = 'Gagal menambah pelanggaran. Periksa kode atau kategori, lalu coba lagi.';
            mysqli_stmt_close($stmt);
        } else {
            $pesan = 'Gagal menyiapkan query tambah pelanggaran.';
        }
    }
}

// Proses edit pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = (int)($_POST['id'] ?? 0);
    $kategori_id = trim((string)($_POST['pelanggaran_kategori_id'] ?? ''));
    $kategori_id = $kategori_id === '' ? null : (int)$kategori_id;
    $kode = trim((string)($_POST['kode'] ?? ''));
    $kode = $kode === '' ? null : $kode;
    $nama = trim((string)($_POST['nama'] ?? ''));
    $poinInput = trim((string)($_POST['poin'] ?? ''));
    $deskripsi = trim((string)($_POST['deskripsi'] ?? ''));
    $status = (int)($_POST['status_aktif'] ?? 1);

    if ($id <= 0) {
        $pesan = 'ID pelanggaran tidak valid.';
    } elseif ($nama === '') {
        $pesan = 'Nama pelanggaran wajib diisi.';
    } elseif ($poinInput === '' || !is_numeric($poinInput) || (int)$poinInput < 0) {
        $pesan = 'Poin harus berupa angka nol atau lebih.';
    } else {
        $poin = (int)$poinInput;
        $status = in_array($status, [0, 1], true) ? $status : 1;

        $stmt = mysqli_prepare(
            $koneksi,
            'UPDATE t_pelanggaran
             SET pelanggaran_kategori_id = ?, kode = ?, nama = ?, poin = ?,
                 deskripsi = ?, status_aktif = ?, updated_at = NOW()
             WHERE id = ?'
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                'issisii',
                $kategori_id,
                $kode,
                $nama,
                $poin,
                $deskripsi,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: kelola_pelanggaran.php?pesan=edit');
                exit;
            }

            $pesan = 'Gagal mengubah pelanggaran. Periksa kode atau kategori, lalu coba lagi.';
            mysqli_stmt_close($stmt);
        } else {
            $pesan = 'Gagal menyiapkan query perubahan pelanggaran.';
        }
    }
}

// Proses hapus pelanggaran
if (isset($_GET['hapus'])) {
    $idHapus = (int)$_GET['hapus'];

    if ($idHapus > 0) {
        $stmt = mysqli_prepare($koneksi, 'DELETE FROM t_pelanggaran WHERE id = ?');

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $idHapus);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: kelola_pelanggaran.php?pesan=hapus');
                exit;
            }

            $pesan = 'Pelanggaran tidak dapat dihapus karena mungkin masih digunakan.';
            mysqli_stmt_close($stmt);
        } else {
            $pesan = 'Gagal menyiapkan query hapus pelanggaran.';
        }
    }
}

// Ambil data pelanggaran yang akan diedit
$dataEdit = null;
if (isset($_GET['edit'])) {
    $idEdit = (int)$_GET['edit'];

    if ($idEdit > 0) {
        $stmt = mysqli_prepare($koneksi, 'SELECT * FROM t_pelanggaran WHERE id = ?');

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $idEdit);
            mysqli_stmt_execute($stmt);
            $hasilEdit = mysqli_stmt_get_result($stmt);
            $dataEdit = mysqli_fetch_assoc($hasilEdit) ?: null;
            mysqli_stmt_close($stmt);
        }
    }

    if (!$dataEdit) {
        $pesan = 'Data pelanggaran tidak ditemukan.';
    }
}

// Ambil daftar kategori
$kategoriQuery = mysqli_query(
    $koneksi,
    'SELECT id, nama FROM t_pelanggaran_kategori ORDER BY nama ASC'
);

if (!$kategoriQuery) {
    die('Gagal mengambil kategori pelanggaran: ' . h(mysqli_error($koneksi)));
}

$kategori = [];
while ($itemKategori = mysqli_fetch_assoc($kategoriQuery)) {
    $kategori[] = $itemKategori;
}

// Ambil daftar pelanggaran beserta nama kategorinya
$dataQuery = mysqli_query(
    $koneksi,
    'SELECT p.*, k.nama AS nama_kategori
     FROM t_pelanggaran p
     LEFT JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
     ORDER BY p.id DESC'
);

if (!$dataQuery) {
    die('Gagal mengambil data pelanggaran: ' . h(mysqli_error($koneksi)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggaran</title>

    <!-- Bootstrap 4, mengikuti kelola_siswa.php -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- DataTables Bootstrap 4 -->
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
            width: 284px;
            max-width: 284px;
            height: 100vh;
            flex: 0 0 284px !important;
            overflow-y: auto;
            z-index: 1000;
        }

        .app-main {
            display: block;
            width: auto;
            min-width: 0;
            min-height: 100vh;
            margin-left: 284px;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 28px;
            margin-bottom: 12px;
        }

        .form-card,
        .table-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            width: 100%;
            margin-bottom: 22px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        #tabelPelanggaran {
            width: 100% !important;
            white-space: nowrap;
        }

        #tabelPelanggaran th,
        #tabelPelanggaran td {
            vertical-align: middle;
        }

        .aksi {
            display: flex;
            gap: 5px;
        }

        .deskripsi-cell {
            white-space: normal;
            min-width: 180px;
            max-width: 320px;
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

            .form-card,
            .table-card {
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<div class="layout-wrapper">

    <!-- SIDEBAR BERSAMA -->
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="app-main">

        <div class="page-header">
            <h1>Kelola Pelanggaran</h1>
            <a href="dashboard.php">Kembali ke Dashboard</a>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-info" role="alert">
                <?= h($pesan); ?>
            </div>
        <?php endif; ?>

        <!-- FORM TAMBAH / EDIT -->
        <div class="form-card shadow-sm">
            <h5 class="mb-3">
                <?= $dataEdit ? 'Edit Pelanggaran' : 'Tambah Pelanggaran'; ?>
            </h5>

            <form method="POST" action="kelola_pelanggaran.php<?= $dataEdit ? '?edit=' . (int)$dataEdit['id'] : ''; ?>">
                <?php if ($dataEdit): ?>
                    <input type="hidden" name="id" value="<?= (int)$dataEdit['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="pelanggaran_kategori_id">Kategori</label>
                        <select class="form-control" id="pelanggaran_kategori_id"
                                name="pelanggaran_kategori_id">
                            <option value="">-- Tanpa Kategori --</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= (int)$k['id']; ?>"
                                    <?= (string)($dataEdit['pelanggaran_kategori_id'] ?? '') === (string)$k['id'] ? 'selected' : ''; ?>>
                                    <?= h($k['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="kode">Kode Pelanggaran</label>
                        <input type="text" class="form-control" id="kode"
                               name="kode" maxlength="30"
                               value="<?= h($dataEdit['kode'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nama">Nama Pelanggaran</label>
                        <input type="text" class="form-control" id="nama"
                               name="nama" maxlength="150" required
                               value="<?= h($dataEdit['nama'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="poin">Poin</label>
                        <input type="number" class="form-control" id="poin"
                               name="poin" min="0" required
                               value="<?= h($dataEdit['poin'] ?? '0'); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="status_aktif">Status</label>
                        <select class="form-control" id="status_aktif" name="status_aktif" required>
                            <option value="1" <?= (string)($dataEdit['status_aktif'] ?? '1') === '1' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= (string)($dataEdit['status_aktif'] ?? '') === '0' ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= h($dataEdit['deskripsi'] ?? ''); ?></textarea>
                    </div>
                </div>

                <?php if ($dataEdit): ?>
                    <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="kelola_pelanggaran.php" class="btn btn-secondary">Batal</a>
                <?php else: ?>
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Pelanggaran</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABEL DATA -->
        <div class="table-card shadow-sm">
            <h5 class="mb-3">Daftar Data Pelanggaran</h5>

            <div class="table-responsive">
                <table id="tabelPelanggaran"
                       class="table table-striped table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Kategori</th>
                            <th>Kode</th>
                            <th>Nama Pelanggaran</th>
                            <th>Poin</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($d = mysqli_fetch_assoc($dataQuery)): ?>
                            <tr>
                                <td><?= (int)$d['id']; ?></td>
                                <td><?= h($d['nama_kategori'] ?? '-'); ?></td>
                                <td><?= h($d['kode'] ?? '-'); ?></td>
                                <td><?= h($d['nama'] ?? ''); ?></td>
                                <td><?= (int)($d['poin'] ?? 0); ?></td>
                                <td class="deskripsi-cell"><?= nl2br(h($d['deskripsi'] ?? '')); ?></td>
                                <td>
                                    <?php if ((int)($d['status_aktif'] ?? 0) === 1): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="aksi">
                                        <a class="btn btn-warning btn-sm"
                                           href="kelola_pelanggaran.php?edit=<?= (int)$d['id']; ?>">Edit</a>
                                        <a class="btn btn-danger btn-sm"
                                           href="kelola_pelanggaran.php?hapus=<?= (int)$d['id']; ?>"
                                           onclick="return confirm('Yakin ingin menghapus pelanggaran ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- JavaScript, mengikuti kelola_siswa.php -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPelanggaran').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data pelanggaran',
            zeroRecords: 'Data pelanggaran tidak ditemukan',
            emptyTable: 'Belum ada data pelanggaran',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Selanjutnya',
                previous: 'Sebelumnya'
            }
        }
    });
});
</script>

</body>
</html>
