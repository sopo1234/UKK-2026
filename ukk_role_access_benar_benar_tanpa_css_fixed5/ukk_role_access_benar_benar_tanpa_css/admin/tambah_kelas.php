<?php
require '../includes/auth.php';
require '../config.php';
requireRole(['admin']);

$title = 'Tambah Kelas';
$section = 'admin';

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];
    $status_aktif = $_POST['status_aktif'];

    $stmt = $pdo->prepare("
        INSERT INTO t_kelas
        (nama, tingkat, jurusan, status_aktif)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $nama,
        $tingkat,
        $jurusan,
        $status_aktif
    ]);

    header('Location: kelola_kelas.php');
    exit;
}

include '../includes/header.php';
?>

<h1>Tambah Kelas</h1>

<p>
    <a href="kelola_kelas.php">Kembali ke Kelola Kelas</a>
</p>

<form method="POST">

<table>

    <tr>
        <td>Nama Kelas</td>
        <td>:</td>
        <td>
            <input type="text" name="nama" required>
        </td>
    </tr>

    <tr>
        <td>Tingkat</td>
        <td>:</td>
        <td>
            <select name="tingkat" required>
                <option value="">-- Pilih Tingkat --</option>
                <option value="X">X</option>
                <option value="XI">XI</option>
                <option value="XII">XII</option>
            </select>
        </td>
    </tr>

    <tr>
        <td>Jurusan</td>
        <td>:</td>
        <td>
            <input type="text" name="jurusan" required>
        </td>
    </tr>

    <tr>
        <td>Status</td>
        <td>:</td>
        <td>
            <select name="status_aktif" required>
                <option value="">-- Pilih Status --</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td>
            <button type="submit" name="simpan">
                Simpan
            </button>

            <a href="kelola_kelas.php">
                Batal
            </a>
        </td>
    </tr>

</table>

</form>

<?php include '../includes/footer.php'; ?>