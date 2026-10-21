<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login dan memiliki role guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: login.php");
    exit;
}

// Array Menu Guru
$menuGuru = [
    'dashboard.php' => 'Dashboard',
    'catatan_pelanggaran.php' => 'Catat Pelanggaran',
    'tindakan.php' => 'Tindakan',
    'riwayat.php' => 'Riwayat',
    'rekap_poin.php' => 'Rekap Poin',
    'about_me.php' => 'About Me'
];

$halaman_aktif = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tindakan / Sanksi - UKK 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
            display: flex;
        }
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #343a40;
            height: 100vh;
            color: white;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar h3 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 18px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li a {
            display: block;
            color: #c2c7d0;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.2s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #007bff;
            color: white;
        }
        .sidebar ul li.logout {
            margin-top: 40px;
        }
        .sidebar ul li.logout a {
            color: #ff6b6b;
        }
        /* Content Styling */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        .header-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigasi -->
    <div class="sidebar">
        <h3>Panel Guru</h3>
        <ul>
            <?php foreach ($menuGuru as $file => $nama_menu) : ?>
                <?php $is_active = ($halaman_aktif == $file) ? 'active' : ''; ?>
                <li>
                    <a href="<?php echo $file; ?>" class="<?php echo $is_active; ?>">
                        <?php echo $nama_menu; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <li class="logout">
                <a href="logout.php">Logout</a>
            </li>
        </ul>
    </div>

    <!-- Konten Utama Tindakan -->
    <div class="main-content">
        <div class="header-box">
            <h2>Data Tindakan / Sanksi Pelanggaran</h2>
            <hr>
            <p>Berikut adalah daftar acuan tindakan atau sanksi berdasarkan akumulasi poin pelanggaran siswa:</p>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Batas Poin</th>
                        <th>Jenis Tindakan / Sanksi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Contoh query sementara mengambil dari t_user (silakan disesuaikan dengan tabel sanksi/tindakan di database Anda nanti)
                    $query = "SELECT * FROM t_user WHERE role = 'guru' LIMIT 5"; 
                    $result = mysqli_query($koneksi, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>10 - 25 Poin</td>";
                            echo "<td>Peringatan Lisan & Pembinaan</td>";
                            echo "<td>Ditangani oleh Guru Piket/Wali Kelas</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center;'>Belum ada data tindakan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>