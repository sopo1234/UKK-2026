<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login dan memiliki role guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: login.php");
    exit;
}

// Proses form saat disubmit
if (isset($_POST['simpan'])) {
    $pelanggaran_kategori_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_kategori_id']);
    $kode                    = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama                    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $poin                    = mysqli_real_escape_string($koneksi, $_POST['poin']);
    $deksripsi               = mysqli_real_escape_string($koneksi, $_POST['deksripsi']);
    $status_aktif            = mysqli_real_escape_string($koneksi, $_POST['status_aktif']);

    // Query INSERT ke tabel t_pelanggaran
    $query = "INSERT INTO t_pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deksripsi, status_aktif) 
              VALUES ('$pelanggaran_kategori_id', '$kode', '$nama', '$poin', '$deksripsi', '$status_aktif')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: riwayat.php");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pelanggaran - UKK 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
        }
        hr {
            border: 0;
            border-top: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            height: 80px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Tambah Data Pelanggaran Baru</h2>
        <hr>

        <?php if (isset($error)) : ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>ID Kategori Pelanggaran (pelanggaran_kategori_id):</label>
                <input type="number" name="pelanggaran_kategori_id" required placeholder="Contoh: 1">
            </div>

            <div class="form-group">
                <label>Kode Pelanggaran:</label>
                <input type="text" name="kode" required placeholder="Contoh: P001">
            </div>

            <div class="form-group">
                <label>Nama Pelanggaran:</label>
                <input type="text" name="nama" required placeholder="Contoh: Terlambat datang ke sekolah">
            </div>

            <div class="form-group">
                <label>Jumlah Poin:</label>
                <input type="number" name="poin" required placeholder="Contoh: 10">
            </div>

            <div class="form-group">
                <label>Deskripsi:</label>
                <textarea name="deksripsi" placeholder="Keterangan tambahan pelanggaran..."></textarea>
            </div>

            <div class="form-group">
                <label>Status Aktif:</label>
                <select name="status_aktif" required>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>

            <button type="submit" name="simpan">Simpan Pelanggaran</button>
            <a href="riwayat.php">Kembali</a>
        </form>
    </div>

</body>
</html>