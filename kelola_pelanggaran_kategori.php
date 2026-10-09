
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

// Tambah kategori
if (isset($_POST["tambah"])) {
    $nama = trim($_POST["nama"]);
    $deskripsi = trim($_POST["deskripsi"]);
    $status = (int)$_POST["status_aktif"];

    if ($nama === "") {
        $pesan = "Nama kategori wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_pelanggaran_kategori
                (nama, deskripsi, status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())"
            );

            $stmt->execute([$nama, $deskripsi, $status]);

            header("Location: kelola_pelanggaran_kategori.php?pesan=tambah");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal menambahkan kategori pelanggaran.";
        }
    }
}

// Edit kategori
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $nama = trim($_POST["nama"]);
    $deskripsi = trim($_POST["deskripsi"]);
    $status = (int)$_POST["status_aktif"];

    if ($nama === "") {
        $pesan = "Nama kategori wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE t_pelanggaran_kategori
                 SET nama = ?, deskripsi = ?, status_aktif = ?,
                     updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([$nama, $deskripsi, $status, $id]);

            header("Location: kelola_pelanggaran_kategori.php?pesan=edit");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal mengubah kategori pelanggaran.";
        }
    }
}

// Hapus kategori
if (isset($_GET["hapus"])) {
    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
        );

        $stmt->execute([(int)$_GET["hapus"]]);

        header("Location: kelola_pelanggaran_kategori.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Kategori tidak dapat dihapus karena masih digunakan.";
    }
}

// Pesan berhasil
if (isset($_GET["pesan"])) {
    $daftarPesan = [
        "tambah" => "Kategori berhasil ditambahkan.",
        "edit" => "Kategori berhasil diperbarui.",
        "hapus" => "Kategori berhasil dihapus."
    ];

    $pesan = $daftarPesan[$_GET["pesan"]] ?? "";
}

// Data untuk edit
$dataEdit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
    );

    $stmt->execute([(int)$_GET["edit"]]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataEdit) {
        $pesan = "Kategori tidak ditemukan.";
    }
}

// Ambil semua kategori
$stmt = $pdo->query(
    "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC"
);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori Pelanggaran</title>
</head>
<body>

<h2>KELOLA KATEGORI PELANGGARAN</h2>

<p><?php echo h($pesan); ?></p>

<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $dataEdit ? "Edit Kategori" : "Tambah Kategori"; ?></h3>

<form method="POST" action="kelola_pelanggaran_kategori.php">

    <?php if ($dataEdit): ?>
        <input type="hidden" name="id"
               value="<?php echo h($dataEdit["id"]); ?>">
    <?php endif; ?>

    <p>Nama Kategori:</p>
    <input type="text" name="nama" maxlength="100" required
           value="<?php echo h($dataEdit["nama"] ?? ""); ?>">

    <p>Deskripsi:</p>
    <textarea name="deskripsi" rows="4" cols="40"><?php
        echo h($dataEdit["deskripsi"] ?? "");
    ?></textarea>

    <p>Status Aktif:</p>
    <select name="status_aktif" required>
        <option value="1"
            <?php echo (string)($dataEdit["status_aktif"] ?? "1") === "1" ? "selected" : ""; ?>>
            Aktif
        </option>
        <option value="0"
            <?php echo (string)($dataEdit["status_aktif"] ?? "") === "0" ? "selected" : ""; ?>>
            Tidak Aktif
        </option>
    </select>

    <p>
        <?php if ($dataEdit): ?>
            <button type="submit" name="edit">Simpan Perubahan</button>
            <a href="kelola_pelanggaran_kategori.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah Kategori</button>
        <?php endif; ?>
    </p>

</form>

<hr>

<h3>Daftar Kategori Pelanggaran</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Kategori</th>
        <th>Deskripsi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $d): ?>
    <tr>
        <td><?php echo h($d["id"]); ?></td>
        <td><?php echo h($d["nama"]); ?></td>
        <td><?php echo nl2br(h($d["deskripsi"])); ?></td>
        <td><?php echo $d["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?></td>
        <td>
            <a href="kelola_pelanggaran_kategori.php?edit=<?php echo h($d["id"]); ?>">Edit</a>
            |
            <a href="kelola_pelanggaran_kategori.php?hapus=<?php echo h($d["id"]); ?>"
               onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>