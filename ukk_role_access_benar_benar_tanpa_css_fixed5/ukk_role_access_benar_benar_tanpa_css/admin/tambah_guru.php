<?php
require '../includes/auth.php';
require '../config.php';
requireRole(['admin']);

$title = 'Tambah Guru';
$section = 'admin';

if (isset($_POST['simpan'])) {

    $nis = trim($_POST['nis']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $status_aktif = $_POST['status_aktif'];

    try {

        $pdo->beginTransaction();

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmtUser = $pdo->prepare("
            INSERT INTO t_user
            (name, email, password, remember_token, role)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmtUser->execute([
            $nama,
            $email,
            $password_hash,
            '',
            'guru'
        ]);

        $user_id = $pdo->lastInsertId();

        $stmtGuru = $pdo->prepare("
            INSERT INTO t_guru
            (nip, nama, email, status_aktif, user_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmtGuru->execute([
            $nis,
            $nama,
            $email,
            $status_aktif,
            $user_id
        ]);

        $pdo->commit();

        header('Location: kelola_guru.php');
        exit;

    } catch (PDOException $e) {

        $pdo->rollBack();

        echo '<p>Gagal menambahkan guru.</p>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

include '../includes/header.php';
?>

<h1>Tambah Guru</h1>

<p>
    <a href="kelola_guru.php">Kembali ke Kelola Guru</a>
</p>

<form method="POST">

<table>

    <tr>
        <td>NIS</td>
        <td>:</td>
        <td>
            <input type="text" name="nis" required>
        </td>
    </tr>

    <tr>
        <td>Nama Guru</td>
        <td>:</td>
        <td>
            <input type="text" name="nama" required>
        </td>
    </tr>

    <tr>
        <td>Email</td>
        <td>:</td>
        <td>
            <input type="email" name="email" required>
        </td>
    </tr>

    <tr>
        <td>Password</td>
        <td>:</td>
        <td>
            <input type="password" name="password" required>
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

            <a href="kelola_guru.php">
                Batal
            </a>
        </td>
    </tr>

</table>

</form>

<?php include '../includes/footer.php'; ?>