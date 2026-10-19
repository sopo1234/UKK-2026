
<?php
session_start();
require_once 'config/koneksi.php';

// Periksa sesi login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Ambil role
$role = strtolower((string)($_SESSION['role'] ?? ''));

if (!in_array($role, ['admin', 'guru'], true)) {
    exit("Akses ditolak. Silakan login kembali.");
}

// Nama pengguna dengan fallback agar tidak muncul warning
$namaPengguna = $_SESSION['nama_user']
    ?? $_SESSION['username']
    ?? 'Pengguna';

$namaPengguna = htmlspecialchars(
    (string)$namaPengguna,
    ENT_QUOTES,
    'UTF-8'
);

// Hitung jumlah data dengan aman
function hitungData($koneksi, $tabel) {
    $daftarTabel = ['t_siswa', 't_guru', 't_kelas'];

    if (!in_array($tabel, $daftarTabel, true)) {
        return 0;
    }

    $hasil = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM `$tabel`"
    );

    if (!$hasil) {
        return 0;
    }

    $data = mysqli_fetch_assoc($hasil);

    return (int)($data['total'] ?? 0);
}

$totalSiswa = hitungData($koneksi, 't_siswa');
$totalGuru  = hitungData($koneksi, 't_guru');
$totalKelas = hitungData($koneksi, 't_kelas');

$activeMenu = 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pelanggaran Siswa</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f5f7f9;
        }

        /* Konten dimulai tepat di sebelah kanan sidebar */
        .app-main {
            display: block;
            width: auto;
            min-width: 0;
            min-height: 100vh;
            margin-left: 284px;
            padding: 30px;
        }

        .dashboard-header {
            margin-bottom: 25px;
        }

        .dashboard-header h2 {
            margin: 0 0 10px;
            color: #263746;
        }

        .dashboard-header p {
            margin-bottom: 0;
            color: #596773;
        }

        .dashboard-card {
            min-height: 125px;
            padding: 24px;
            border: 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card-siswa {
            background: #d7edf2;
        }

        .card-guru {
            background: #d1e8ef;
        }

        .card-kelas {
            background: #bfdeea;
        }

        .dashboard-card h5 {
            margin-bottom: 8px;
            color: #263746;
        }

        .dashboard-card .angka {
            font-size: 30px;
            font-weight: bold;
            color: #164e6b;
            margin: 0;
        }

        .info-box {
            background: #ffffff;
            padding: 22px;
            margin-top: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        @media (max-width: 767.98px) {
            .app-main {
                width: 100%;
                margin-left: 0;
                padding: 18px 12px;
            }

            .dashboard-card {
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR BERSAMA -->
    <?php require_once __DIR__ . '/layout/sidebar.php'; ?>

    <!-- KONTEN DASHBOARD -->
    <main class="app-main">

        <div class="dashboard-header">
            <h2>Dashboard</h2>

            <p>
                Selamat datang,
                <strong><?= $namaPengguna; ?></strong>.
            </p>

            <p>
                Role:
                <strong><?= htmlspecialchars(
                    ucfirst($role),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?></strong>
            </p>
        </div>

        <!-- KARTU RINGKASAN -->
        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="card dashboard-card card-siswa">
                    <h5>Data Siswa</h5>
                    <p class="angka"><?= $totalSiswa; ?></p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card dashboard-card card-guru">
                    <h5>Data Guru</h5>
                    <p class="angka"><?= $totalGuru; ?></p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card dashboard-card card-kelas">
                    <h5>Data Kelas</h5>
                    <p class="angka"><?= $totalKelas; ?></p>
                </div>
            </div>

        </div>

        <!-- INFORMASI -->
        <div class="info-box">
            <h4>Dashboard Pelanggaran Siswa</h4>

            <p>
                Gunakan menu di sebelah kiri untuk mengakses
                pengelolaan data sesuai hak akses akun.
            </p>

            <?php if ($role === 'admin') { ?>
                <a href="kelola_siswa.php" class="btn btn-primary">
                    Kelola Data Siswa
                </a>

                <a href="kelola_pelanggaran.php"
                   class="btn btn-info text-white">
                    Data Pelanggaran
                </a>
            <?php } else { ?>
                <a href="catatan_pelanggaran.php" class="btn btn-primary">
                    Catat Pelanggaran
                </a>
            <?php } ?>
        </div>

    </main>

</body>
</html>
