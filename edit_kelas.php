<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: data_kelas.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data kelas berdasarkan ID
$query_data = mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE id = '$id'");
if (mysqli_num_rows($query_data) === 0) {
    header("Location: data_kelas.php");
    exit;
}
$kelas = mysqli_fetch_assoc($query_data);

// Deteksi nama kolom secara aman (apakah 'nama_kelas' atau 'nama')
$nama_kolom_db = array_key_exists('nama_kelas', $kelas) ? 'nama_kelas' : 'nama';

// Proses update data ketika tombol simpan diklik
if (isset($_POST['update'])) {
    $nama_val = mysqli_real_escape_string($koneksi, $_POST['nama_kelas']);

    $query_update = mysqli_query($koneksi, "UPDATE t_kelas SET $nama_kolom_db = '$nama_val' WHERE id = '$id'");

    if ($query_update) {
        header("Location: data_kelas.php");
        exit;
    } else {
        $error = "Gagal memperbarui data kelas: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kelas - UKK 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            max-width: 500px;
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
        input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
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
        <h2>Edit Data Kelas</h2>
        <hr>

        <?php if (isset($error)) : ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Kelas:</label>
                <input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($kelas[$nama_kolom_db]); ?>" required>
            </div>

            <button type="submit" name="update">Perbarui Kelas</button>
            <a href="data_kelas.php">Kembali</a>
        </form>
    </div>

</body>
</html>