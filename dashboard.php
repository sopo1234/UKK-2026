<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower((string)($_SESSION['role'] ?? ''));

if (!in_array($role, ['admin', 'guru'], true)) {
    exit("Akses ditolak!");
}
if ($role === 'guru') { header("Location: guru.php"); exit; }

$username = $_SESSION['username']
    ?? $_SESSION['nama_user']
    ?? $_SESSION['name']
    ?? 'Pengguna';

$username = htmlspecialchars(
    (string)$username,
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pelanggaran Siswa</title>

    <!-- Bootstrap 5 agar min-vh-100 berfungsi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-white">

<div class="container-fluid min-vh-100 p-0">

    <div class="row min-vh-100 g-0 align-items-stretch">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3 d-flex flex-column">

            <h4 class="text-white mb-4">
                Pelanggaran Siswa
            </h4>

            <ul class="nav nav-pills flex-column">

                <!-- DASHBOARD -->
                <li class="nav-item mb-2">
                    <a href="dashboard.php"
                       class="nav-link active">
                        Dashboard
                    </a>
                </li>

                <?php if ($role === 'admin') { ?>

                    <!-- MENU ADMIN -->
                    <li class="nav-item mb-2">
                        <a href="kelola_siswa.php"
                           class="nav-link text-white">
                            Data Siswa
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="kelola_guru.php"
                           class="nav-link text-white">
                            Data Guru
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="kelola_kelas.php"
                           class="nav-link text-white">
                            Data Kelas
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="kelola_tahun_ajaran.php"
                           class="nav-link text-white">
                            Data Tahun Ajaran
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="kelola_walikelas.php"
                           class="nav-link text-white">
                            Data Wali Kelas
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="penempatan_siswa.php"
                           class="nav-link text-white">
                            Penempatan Siswa
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="kelola_pelanggaran_kategori.php"
                           class="nav-link text-white">
                            Kategori Pelanggaran
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="kelola_pelanggaran.php"
                           class="nav-link text-white">
                            Data Pelanggaran
                        </a>
                    </li>

                <?php } else { ?>

                    <!-- MENU GURU -->
                    <li class="nav-item mb-2">
                        <a href="catatan_pelanggaran.php"
                           class="nav-link text-white">
                            Catat Pelanggaran
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="rekap_poin.php"
                           class="nav-link text-white">
                            Rekap Poin
                        </a>
                    </li>

                <?php } ?>

                <!-- MENU ABOUT ME -->
                <li class="nav-item mb-2">
                    <a href="about_me.php"
                       class="nav-link text-white">
                        About Me
                    </a>
                </li>

            </ul>

            <hr class="border-secondary">

            <!-- LOGOUT -->
            <div class="mt-auto">
                <a href="logout.php"
                   class="nav-link text-danger">
                    Logout
                </a>
            </div>

        </aside>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4 bg-white">

            <h2>Dashboard</h2>

            <p>
                Selamat datang,
                <strong><?= $username; ?></strong>.
            </p>

            <p>
                Role:
                <strong>
                    <?= htmlspecialchars(
                        ucfirst($role),
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </p>

            <hr>

            <!-- KARTU DATA -->
            <div class="row">

                <!-- KARTU SISWA -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Data Siswa
                            </h5>

                            <h2>120</h2>
                        </div>
                    </div>
                </div>

                <!-- KARTU GURU -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Data Guru
                            </h5>

                            <h2>25</h2>
                        </div>
                    </div>
                </div>

                <!-- KARTU KELAS -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Kelas
                            </h5>

                            <h2>12</h2>
                        </div>
                    </div>
                </div>

            </div>

            <hr>

            <!-- INFORMASI DASHBOARD -->
            <h4>Dashboard Pelanggaran Siswa</h4>

            <p>
                Selamat datang di sistem informasi pelanggaran siswa.
                Silakan pilih menu di sebelah kiri untuk melanjutkan.
            </p>

            <?php if ($role === 'admin') { ?>

                <a href="kelola_siswa.php"
                   class="btn btn-primary">
                    Kelola Data Siswa
                </a>

                <a href="kelola_pelanggaran.php"
                   class="btn btn-secondary">
                    Data Pelanggaran
                </a>

            <?php } else { ?>

                <a href="catatan_pelanggaran.php"
                   class="btn btn-primary">
                    Catat Pelanggaran
                </a>

                <a href="rekap_poin.php"
                   class="btn btn-secondary">
                    Rekap Poin
                </a>

            <?php } ?>

        </main>

    </div>

</div>

</body>
</html>