
<?php
session_start();
require_once "config.php";

if (
    !isset($_SESSION["role"]) ||
    strtolower($_SESSION["role"]) !== "admin"
) {
    exit("Akses ditolak. Login sebagai Admin terlebih dahulu.");
}

function h($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, "UTF-8");
}

$pesan = "";

// Tambah siswa
if (isset($_POST["tambah"])) {
    $nis = trim($_POST["nis"]);
    $nish = trim($_POST["nish"]);
    $nama = trim($_POST["nama"]);
    $jk = $_POST["jenis_kelamin"];
    $tanggal = $_POST["tanggal_lahir"];
    $alamat = trim($_POST["alamat"]);
    $status = (int)$_POST["status_aktif"];

    try {
        $sql = "INSERT INTO t_siswa
                (nis, nish, nama, jenis_kelamin,
                 tanggal_lahir, alamat, status_aktif,
                 created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nis, $nish, $nama, $jk,
            $tanggal, $alamat, $status
        ]);

        header("Location: kelola_siswa.php?pesan=tambah");
        exit;
    } catch (PDOException $e) {
        $pesan = "Gagal menambahkan siswa. Periksa NIS dan NISH agar tidak duplikat.";
    }
}

// Edit siswa
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $nis = trim($_POST["nis"]);
    $nish = trim($_POST["nish"]);
    $nama = trim($_POST["nama"]);
    $jk = $_POST["jenis_kelamin"];
    $tanggal = $_POST["tanggal_lahir"];
    $alamat = trim($_POST["alamat"]);
    $status = (int)$_POST["status_aktif"];

    try {
        $sql = "UPDATE t_siswa SET
                nis = ?, nish = ?, nama = ?,
                jenis_kelamin = ?, tanggal_lahir = ?,
                alamat = ?, status_aktif = ?,
                updated_at = NOW()
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nis, $nish, $nama, $jk,
            $tanggal, $alamat, $status, $id
        ]);

        header("Location: kelola_siswa.php?pesan=edit");
        exit;
    } catch (PDOException $e) {
        $pesan = "Gagal mengubah data siswa.";
    }
}

// Hapus siswa
if (isset($_GET["hapus"])) {
    $id = (int)$_GET["hapus"];

    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_siswa WHERE id = ?"
        );
        $stmt->execute([$id]);

        header("Location: kelola_siswa.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Siswa tidak dapat dihapus karena masih memiliki data terkait.";
    }
}

// Pesan
if (isset($_GET["pesan"])) {
    $pesan = match ($_GET["pesan"]) {
        "tambah" => "Siswa berhasil ditambahkan.",
        "edit" => "Data siswa berhasil diubah.",
        "hapus" => "Data siswa berhasil dihapus.",
        default => ""
    };
}

// Data untuk edit
$dataEdit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_siswa WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit"]]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataEdit) {
        $pesan = "Data siswa tidak ditemukan.";
    }
}

// Tampilkan siswa
$stmt = $pdo->query(
    "SELECT * FROM t_siswa ORDER BY id DESC"
);
$siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>
</head>
<body>

<h2>KELOLA DATA SISWA</h2>

<p><?php echo h($pesan); ?></p>

<p><a href="dashboard (1).php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $dataEdit ? "Edit Siswa" : "Tambah Siswa"; ?></h3>

<form method="POST" action="kelola_siswa.php">

    <?php if ($dataEdit): ?>
        <input type="hidden" name="id"
               value="<?php echo h($dataEdit["id"]); ?>">
    <?php endif; ?>

    <p>NIS:</p>
    <input type="text" name="nis" required
           value="<?php echo h($dataEdit["nis"] ?? ""); ?>">

    <p>NISH:</p>
    <input type="text" name="nish" required
           value="<?php echo h($dataEdit["nish"] ?? ""); ?>">

    <p>Nama Siswa:</p>
    <input type="text" name="nama" required
           value="<?php echo h($dataEdit["nama"] ?? ""); ?>">

    <p>Jenis Kelamin:</p>
    <select name="jenis_kelamin" required>
        <option value="">-- Pilih --</option>
        <option value="L"
            <?php echo ($dataEdit["jenis_kelamin"] ?? "") === "L"
                ? "selected" : ""; ?>>Laki-laki</option>
        <option value="P"
            <?php echo ($dataEdit["jenis_kelamin"] ?? "") === "P"
                ? "selected" : ""; ?>>Perempuan</option>
    </select>

    <p>Tanggal Lahir:</p>
    <input type="date" name="tanggal_lahir" required
           value="<?php echo h($dataEdit["tanggal_lahir"] ?? ""); ?>">

    <p>Alamat:</p>
    <textarea name="alamat" required><?php
        echo h($dataEdit["alamat"] ?? "");
    ?></textarea>

    <p>Status Aktif:</p>
    <select name="status_aktif" required>
        <option value="1"
            <?php echo (string)($dataEdit["status_aktif"] ?? "1") === "1"
                ? "selected" : ""; ?>>Aktif</option>
        <option value="0"
            <?php echo (string)($dataEdit["status_aktif"] ?? "") === "0"
                ? "selected" : ""; ?>>Tidak Aktif</option>
    </select>

    <p>
        <?php if ($dataEdit): ?>
            <button type="submit" name="edit">Simpan Perubahan</button>
            <a href="kelola_siswa.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah Siswa</button>
        <?php endif; ?>
    </p>

</form>

<hr>

<h3>Daftar Siswa</h3>

<table border="1">
    <tr>
        <th>ID</th>
        <th>NIS</th>
        <th>NISH</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($siswa as $s): ?>
    <tr>
        <td><?php echo h($s["id"]); ?></td>
        <td><?php echo h($s["nis"]); ?></td>
        <td><?php echo h($s["nish"]); ?></td>
        <td><?php echo h($s["nama"]); ?></td>
        <td><?php echo h($s["jenis_kelamin"]); ?></td>
        <td><?php echo h($s["tanggal_lahir"]); ?></td>
        <td><?php echo h($s["alamat"]); ?></td>
        <td><?php echo $s["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?></td>
        <td>
            <a href="kelola_siswa.php?edit=<?php echo h($s["id"]); ?>">Edit</a>
            |
            <a href="kelola_siswa.php?hapus=<?php echo h($s["id"]); ?>"
               onclick="return confirm('Yakin hapus siswa ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>