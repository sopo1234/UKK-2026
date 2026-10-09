
<?php
session_start();
require_once "config.php";

if (
    !isset($_SESSION["role"]) ||
    strtolower($_SESSION["role"]) !== "admin"
) {
    exit("Akses ditolak. Login sebagai Admin terlebih dahulu.");
}

function h($nilai) {
    return htmlspecialchars((string)$nilai, ENT_QUOTES, "UTF-8");
}

$pesan = "";

// Tambah tahun ajaran
if (isset($_POST["tambah"])) {
    $nama = trim($_POST["nama"]);
    $mulai = $_POST["tanggal_mulai"];
    $selesai = $_POST["tanggal_selesai"];
    $status = (int)$_POST["status_aktif"];

    if ($mulai > $selesai) {
        $pesan = "Tanggal mulai tidak boleh setelah tanggal selesai.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_tahun_ajaran
                (nama, tanggal_mulai, tanggal_selesai,
                 status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())"
            );

            $stmt->execute([
                $nama, $mulai, $selesai, $status
            ]);

            header("Location: kelola_tahun_ajaran.php?pesan=tambah");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal menambahkan tahun ajaran.";
        }
    }
}

// Edit tahun ajaran
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $nama = trim($_POST["nama"]);
    $mulai = $_POST["tanggal_mulai"];
    $selesai = $_POST["tanggal_selesai"];
    $status = (int)$_POST["status_aktif"];

    if ($mulai > $selesai) {
        $pesan = "Tanggal mulai tidak boleh setelah tanggal selesai.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE t_tahun_ajaran
                 SET nama = ?, tanggal_mulai = ?,
                     tanggal_selesai = ?, status_aktif = ?,
                     updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([
                $nama, $mulai, $selesai, $status, $id
            ]);

            header("Location: kelola_tahun_ajaran.php?pesan=edit");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal mengubah tahun ajaran.";
        }
    }
}

// Hapus tahun ajaran
if (isset($_GET["hapus"])) {
    $id = (int)$_GET["hapus"];

    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_tahun_ajaran WHERE id = ?"
        );
        $stmt->execute([$id]);

        header("Location: kelola_tahun_ajaran.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Tahun ajaran tidak dapat dihapus karena masih digunakan.";
    }
}

// Pesan
if (isset($_GET["pesan"])) {
    if ($_GET["pesan"] === "tambah") {
        $pesan = "Tahun ajaran berhasil ditambahkan.";
    } elseif ($_GET["pesan"] === "edit") {
        $pesan = "Tahun ajaran berhasil diubah.";
    } elseif ($_GET["pesan"] === "hapus") {
        $pesan = "Tahun ajaran berhasil dihapus.";
    }
}

// Data untuk form edit
$dataEdit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_tahun_ajaran WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit"]]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataEdit) {
        $pesan = "Data tahun ajaran tidak ditemukan.";
    }
}

// Daftar tahun ajaran
$stmt = $pdo->query(
    "SELECT * FROM t_tahun_ajaran ORDER BY id DESC"
);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Tahun Ajaran</title>
</head>
<body>

<h2>KELOLA TAHUN AJARAN</h2>

<p><?php echo h($pesan); ?></p>

<p><a href="dashboard (1).php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $dataEdit ? "Edit Tahun Ajaran" : "Tambah Tahun Ajaran"; ?></h3>

<form method="POST" action="kelola_tahun_ajaran.php">

    <?php if ($dataEdit): ?>
        <input type="hidden" name="id"
               value="<?php echo h($dataEdit["id"]); ?>">
    <?php endif; ?>

    <p>Nama Tahun Ajaran:</p>
    <input type="text" name="nama" required
           placeholder="Contoh: 2026/2027"
           value="<?php echo h($dataEdit["nama"] ?? ""); ?>">

    <p>Tanggal Mulai:</p>
    <input type="date" name="tanggal_mulai" required
           value="<?php echo h($dataEdit["tanggal_mulai"] ?? ""); ?>">

    <p>Tanggal Selesai:</p>
    <input type="date" name="tanggal_selesai" required
           value="<?php echo h($dataEdit["tanggal_selesai"] ?? ""); ?>">

    <p>Status:</p>
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
            <a href="kelola_tahun_ajaran.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah Tahun Ajaran</button>
        <?php endif; ?>
    </p>
</form>

<hr>

<h3>Daftar Tahun Ajaran</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $row): ?>
    <tr>
        <td><?php echo h($row["id"]); ?></td>
        <td><?php echo h($row["nama"]); ?></td>
        <td><?php echo h($row["tanggal_mulai"]); ?></td>
        <td><?php echo h($row["tanggal_selesai"]); ?></td>
        <td><?php echo $row["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?></td>
        <td>
            <a href="kelola_tahun_ajaran.php?edit=<?php echo h($row["id"]); ?>">Edit</a>
            |
            <a href="kelola_tahun_ajaran.php?hapus=<?php echo h($row["id"]); ?>"
               onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>