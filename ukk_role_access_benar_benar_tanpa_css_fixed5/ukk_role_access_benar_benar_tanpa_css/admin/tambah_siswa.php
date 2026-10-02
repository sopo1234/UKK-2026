<?php
require '../includes/auth.php';
require '../config.php';
requireRole(['admin']);

$title = 'Tambah Siswa';
$section = 'admin';

if (isset($_POST['simpan'])) {

    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $status_aktif = $_POST['status_aktif'];

    $stmt = $pdo->prepare("
        INSERT INTO t_siswa
        (nisn, nama, jenis_kelamin, status_aktif)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $nisn,
        $nama,
        $jenis_kelamin,
        $status_aktif
    ]);

    header('Location: kelola_siswa.php');
    exit;
}

include '../includes/header.php';
?>

<h1>Tambah Siswa</h1>

<p>
    <a href="kelola_siswa.php">Kembali ke Kelola Siswa</a>
</p>

<form method="POST">

    <table>
        <tr>
            <td>NISN</td>
            <td>:</td>
            <td>
                <input type="text" name="nisn" required>
            </td>
        </tr>

        <tr>
            <td>Nama Siswa</td>
            <td>:</td>
            <td>
                <input type="text" name="nama" required>
            </td>
        </tr>

        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>Status</td>
            <td>:</td>
            <td>
                <select name="status_aktif" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Tidak Aktif</option>
                </select>
            </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td>
                <button type="submit" name="simpan">Simpan</button>
                <a href="kelola_siswa.php">Batal</a>
            </td>
        </tr>
    </table>

</form>

<?php include '../includes/footer.php'; ?>