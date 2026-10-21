<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Proses ketika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $nama         = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat      = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan      = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $status_aktif = mysqli_real_escape_string($koneksi, $_POST['status_aktif']);

    // Query INSERT ke tabel t_kelas
    $query = "INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif) VALUES ('$nama', '$tingkat', '$jurusan', '$status_aktif')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: data_kelas.php");
        exit;
    } else {
        $error = "Gagal menyimpan data kelas: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kelas Baru - UKK 2026</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f9; color: #333; }
        .container { max-width: 500px; background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; }
        hr { border: 0; border-top: 1px solid #dee2e6; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        a { color: #007bff; text-decoration: none; margin-left: 10px; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Tambah Data Kelas Baru</h2>
        <hr>

        <?php if (isset($error)) : ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Kelas:</label>
                <input type="text" name="nama" required placeholder="Contoh: X RPL 1">
            </div>

            <div class="form-group">
                <label>Tingkat:</label>
                <input type="text" name="tingkat" required placeholder="Contoh: X, XI, atau XII">
            </div>

            <div class="form-group">
                <label>Jurusan:</label>
                <input type="text" name="jurusan" required placeholder="Contoh: Rekayasa Perangkat Lunak">
            </div>

            <div class="form-group">
                <label>Status Aktif:</label>
                <select name="status_aktif" required>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>

            <button type="submit" name="simpan">Simpan Kelas</button>
            <a href="data_kelas.php">Kembali</a>
        </form>
    </div>

</body>
</html>