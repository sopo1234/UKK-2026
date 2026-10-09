
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

// Tambah pelanggaran
if (isset($_POST["tambah"])) {
    $kategori_id = ($_POST["pelanggaran_kategori_id"] !== "")
        ? (int)$_POST["pelanggaran_kategori_id"] : null;
    $kode = trim($_POST["kode"]);
    $nama = trim($_POST["nama"]);
    $poin = (int)$_POST["poin"];
    $deskripsi = trim($_POST["deskripsi"]);
    $status = (int)$_POST["status_aktif"];

    if ($nama === "" || $poin < 0) {
        $pesan = "Nama wajib diisi dan poin tidak boleh negatif.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_pelanggaran
                (pelanggaran_kategori_id, kode, nama, poin,
                 deskripsi, status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            $stmt->execute([
                $kategori_id,
                $kode !== "" ? $kode : null,
                $nama,
                $poin,
                $deskripsi,
                $status
            ]);

            header("Location: kelola_pelanggaran.php?pesan=tambah");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal menambah pelanggaran. Periksa kode dan kategori.";
        }
    }
}

// Edit pelanggaran
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $kategori_id = ($_POST["pelanggaran_kategori_id"] !== "")
        ? (int)$_POST["pelanggaran_kategori_id"] : null;
    $kode = trim($_POST["kode"]);
    $nama = trim($_POST["nama"]);
    $poin = (int)$_POST["poin"];
    $deskripsi = trim($_POST["deskripsi"]);
    $status = (int)$_POST["status_aktif"];

    if ($nama === "" || $poin < 0) {
        $pesan = "Nama wajib diisi dan poin tidak boleh negatif.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE t_pelanggaran SET
                 pelanggaran_kategori_id = ?, kode = ?, nama = ?,
                 poin = ?, deskripsi = ?, status_aktif = ?,
                 updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([
                $kategori_id,
                $kode !== "" ? $kode : null,
                $nama,
                $poin,
                $deskripsi,
                $status,
                $id
            ]);

            header("Location: kelola_pelanggaran.php?pesan=edit");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal mengubah pelanggaran. Periksa data yang dimasukkan.";
        }
    }
}

// Hapus pelanggaran
if (isset($_GET["hapus"])) {
    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_pelanggaran WHERE id = ?"
        );
        $stmt->execute([(int)$_GET["hapus"]]);

        header("Location: kelola_pelanggaran.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Pelanggaran tidak dapat dihapus karena masih digunakan.";
    }
}

// Pesan
if (isset($_GET["pesan"])) {
    $daftarPesan = [
        "tambah" => "Pelanggaran berhasil ditambahkan.",
        "edit" => "Pelanggaran berhasil diperbarui.",
        "hapus" => "Pelanggaran berhasil dihapus."
    ];
    $pesan = $daftarPesan[$_GET["pesan"]] ?? "";
}

// Data edit
$dataEdit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_pelanggaran WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit"]]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataEdit) {
        $pesan = "Data pelanggaran tidak ditemukan.";
    }
}

// Pilihan kategori
$kategori = $pdo->query(
    "SELECT id, nama FROM t_pelanggaran_kategori ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

// Daftar pelanggaran
$data = $pdo->query(
    "SELECT p.*, k.nama AS nama_kategori
     FROM t_pelanggaran p
     LEFT JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
     ORDER BY p.id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pelanggaran</title>
</head>
<body>

<h2>KELOLA DATA PELANGGARAN</h2>

<p><?php echo h($pesan); ?></p>
<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $dataEdit ? "Edit Pelanggaran" : "Tambah Pelanggaran"; ?></h3>

<form method="POST" action="kelola_pelanggaran.php">

    <?php if ($dataEdit): ?>
        <input type="hidden" name="id"
               value="<?php echo h($dataEdit["id"]); ?>">
    <?php endif; ?>

    <p>Kategori:</p>
    <select name="pelanggaran_kategori_id">
        <option value="">-- Tanpa Kategori --</option>
        <?php foreach ($kategori as $k): ?>
            <option value="<?php echo h($k["id"]); ?>"
                <?php echo (string)($dataEdit["pelanggaran_kategori_id"] ?? "") === (string)$k["id"] ? "selected" : ""; ?>>
                <?php echo h($k["nama"]); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>Kode Pelanggaran:</p>
    <input type="text" name="kode" maxlength="30"
           value="<?php echo h($dataEdit["kode"] ?? ""); ?>">

    <p>Nama Pelanggaran:</p>
    <input type="text" name="nama" maxlength="150" required
           value="<?php echo h($dataEdit["nama"] ?? ""); ?>">

    <p>Poin:</p>
    <input type="number" name="poin" min="0" required
           value="<?php echo h($dataEdit["poin"] ?? "0"); ?>">

    <p>Deskripsi:</p>
    <textarea name="deskripsi" rows="4" cols="40"><?php
        echo h($dataEdit["deskripsi"] ?? "");
    ?></textarea>

    <p>Status:</p>
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
            <a href="kelola_pelanggaran.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah Pelanggaran</button>
        <?php endif; ?>
    </p>
</form>

<hr>

<h3>Daftar Pelanggaran</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Kategori</th>
        <th>Kode</th>
        <th>Nama Pelanggaran</th>
        <th>Poin</th>
        <th>Deskripsi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $d): ?>
    <tr>
        <td><?php echo h($d["id"]); ?></td>
        <td><?php echo h($d["nama_kategori"] ?? "-"); ?></td>
        <td><?php echo h($d["kode"] ?? "-"); ?></td>
        <td><?php echo h($d["nama"]); ?></td>
        <td><?php echo h($d["poin"]); ?></td>
        <td><?php echo nl2br(h($d["deskripsi"])); ?></td>
        <td><?php echo $d["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?></td>
        <td>
            <a href="kelola_pelanggaran.php?edit=<?php echo h($d["id"]); ?>">Edit</a>
            |
            <a href="kelola_pelanggaran.php?hapus=<?php echo h($d["id"]); ?>"
               onclick="return confirm('Yakin ingin menghapus pelanggaran ini?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>