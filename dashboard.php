<?php
session_start();

if (!isset($_SESSION['id_user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$username = htmlspecialchars(
    $_SESSION['username'] ?? 'Pengguna',
    ENT_QUOTES,
    'UTF-8'
);

$role = strtolower((string) $_SESSION['role']);
$roleTampil = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dashboard</title>
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link active">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link text-white">
                        Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="#" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Dashboard</h2>
            <p>
                Selamat datang di halaman dashboard.
            </p>
            <div class="row">
                <!-- CARD DATA SISWA -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Data Siswa
                            </h5>
                            <h2>
                                120
                            </h2>
                        </div>
                    </div>
                </div>
                <!-- CARD DATA GURU -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Data Guru
                            </h5>
                            <h2>
                                25
                            </h2>
                        </div>
                    </div>
                </div>
                <!-- CARD KELAS -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Kelas
                            </h5>
                            <h2>
                             12
                          </h2>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
    <h2>Dashboard</h2>
    <p>
        Selamat datang,
        <?php echo $_SESSION['nama_user']; ?>
    </p>
    <p>
        Anda berhasil login.
    </p>
    <a href="logout.php">Logout</a>
</body>
</html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UKK-2026</title>
</head>
<body>

<h1>Dashboard UKK-2026</h1>

<p>Selamat datang, <strong><?php echo $username; ?></strong></p>
<p>Role: <strong><?php echo $roleTampil; ?></strong></p>

<hr>

<?php if ($role === "admin") { ?>
    <h3>Menu Admin</h3>
    <ul>
        <li><a href="menu1.php">Menu 1</a></li>
        <li><a href="menu2.php">Menu 2</a></li>
        <li><a href="menu3.php">Menu 3</a></li>
        <li><a href="menu4.php">Menu 4</a></li>
        <li><a href="kelola_guru.php">Kelola Guru</a></li>
        <li><a href="kelola_siswa.php">Kelola Siswa</a></li>
        <li><a href="kelola_kelas_siswa.php">Kelola Kelas Siswa</a></li>
        <li><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
        <li><a href="kelola_pelanggaran_kategori.php">Kelola Kategori Pelanggaran</a></li>
        <li><a href="kelola_pelanggaran.php">Kelola Pelanggaran</a></li>
        <li><a href="kelola_pelanggaran_siswa.php">Kelola Pelanggaran Siswa</a></li>
        <li><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
    </ul>
<?php } else { ?>
    <h3>Menu Guru</h3>
    <ul>
        <li><a href="menu3.php">Menu 3</a></li>
        <li><a href="menu4.php">Menu 4</a></li>
    </ul>
<?php } ?>

<hr>

<p><a href="logout.php">Logout</a></p>

</body>
</html>
