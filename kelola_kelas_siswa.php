
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

// Tambah penempatan siswa
if (isset($_POST["tambah"])) {
    $siswa_id = (int)$_POST["siswa_id"];
    $tahun_id = (int)$_POST["tahun_ajaran_id"];
    $kelas_id = (int)$_POST["kelas_id"];
    $mulai = $_POST["tanggal_mulai"];
    $selesai = $_POST["tanggal_selesai"] ?: null;
    $status = (int)$_POST["status_aktif"];

    if ($selesai !== null && $mulai > $selesai) {
        $pesan = "Tanggal mulai tidak boleh setelah tanggal selesai.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_kelas_siswa
                (siswa_id, tahun_ajaran_id, kelas_id,
                 tanggal_mulai, tanggal_selesai, status_aktif,
                 created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            $stmt->execute([
                $siswa_id, $tahun_id, $kelas_id,
                $mulai, $selesai, $status
            ]);

            header("Location: kelola_kelas_siswa.php?pesan=tambah");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal menambah penempatan. Periksa ID siswa, kelas, dan tahun ajaran.";
        }
    }
}

// Edit penempatan
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $siswa_id = (int)$_POST["siswa_id"];
    $tahun_id = (int)$_POST["tahun_ajaran_id"];
    $kelas_id = (int)$_POST["kelas_id"];
    $mulai = $_POST["tanggal_mulai"];
    $selesai = $_POST["tanggal_selesai"] ?: null;
    $status = (int)$_POST["status_aktif"];

    if ($selesai !== null && $mulai > $selesai) {
        $pesan = "Tanggal mulai tidak boleh setelah tanggal selesai.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE t_kelas_siswa SET
                 siswa_id = ?, tahun_ajaran_id = ?, kelas_id = ?,
                 tanggal_mulai = ?, tanggal_selesai = ?,
                 status_aktif = ?, updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([
                $siswa_id, $tahun_id, $kelas_id,
                $mulai, $selesai, $status, $id
            ]);

            header("Location: kelola_kelas_siswa.php?pesan=edit");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal mengubah penempatan siswa.";
        }
    }
}

// Hapus penempatan
if (isset($_GET["hapus"])) {
    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_kelas_siswa WHERE id = ?"
        );
        $stmt->execute([(int)$_GET["hapus"]]);

        header("Location: kelola_kelas_siswa.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Data penempatan tidak dapat dihapus.";
    }
}

// Pesan
if (isset($_GET["pesan"])) {
    $daftarPesan = [
        "tambah" => "Penempatan siswa berhasil ditambahkan.",
        "edit" => "Penempatan siswa berhasil diperbarui.",
        "hapus" => "Penempatan siswa berhasil dihapus."
    ];
    $pesan = $daftarPesan[$_GET["pesan"]] ?? "";
}

// Data edit
$dataEdit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_kelas_siswa WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit"]]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataEdit) {
        $pesan = "Data penempatan tidak ditemukan.";
    }
}

// Pilihan siswa, kelas, dan tahun ajaran
$siswa = $pdo->query(
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$kelas = $pdo->query(
    "SELECT id, nama FROM t_kelas ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$tahun = $pdo->query(
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

// Daftar penempatan
$data = $pdo->query(
    "SELECT ks.*, s.nama AS nama_siswa, k.nama AS nama_kelas,
            ta.nama AS nama_tahun
     FROM t_kelas_siswa ks
     LEFT JOIN t_siswa s ON ks.siswa_id = s.id
     LEFT JOIN t_kelas k ON ks.kelas_id = k.id
     LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
     ORDER BY ks.id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Penempatan Siswa</title>
</head>
<body>

<h2>KELOLA PENEMPATAN SISWA</h2>

<p><?php echo h($pesan); ?></p>
<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $dataEdit ? "Edit Penempatan" : "Tambah Penempatan"; ?></h3>

<form method="POST" action="kelola_kelas_siswa.php">

    <?php if ($dataEdit): ?>
        <input type="hidden" name="id" value="<?php echo h($dataEdit["id"]); ?>">
    <?php endif; ?>

    <p>Siswa:</p>
    <select name="siswa_id" required>
        <option value="">-- Pilih Siswa --</option>
        <?php foreach ($siswa as $s): ?>
            <option value="<?php echo h($s["id"]); ?>"
                <?php echo (string)($dataEdit["siswa_id"] ?? "") === (string)$s["id"] ? "selected" : ""; ?>>
                <?php echo h($s["nis"] . " - " . $s["nama"]); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>Tahun Ajaran:</p>
    <select name="tahun_ajaran_id" required>
        <option value="">-- Pilih Tahun Ajaran --</option>
        <?php foreach ($tahun as $t): ?>
            <option value="<?php echo h($t["id"]); ?>"
                <?php echo (string)($dataEdit["tahun_ajaran_id"] ?? "") === (string)$t["id"] ? "selected" : ""; ?>>
                <?php echo h($t["nama"]); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>Kelas:</p>
    <select name="kelas_id" required>
        <option value="">-- Pilih Kelas --</option>
        <?php foreach ($kelas as $k): ?>
            <option value="<?php echo h($k["id"]); ?>"
                <?php echo (string)($dataEdit["kelas_id"] ?? "") === (string)$k["id"] ? "selected" : ""; ?>>
                <?php echo h($k["nama"]); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>Tanggal Mulai:</p>
    <input type="date" name="tanggal_mulai" required
           value="<?php echo h($dataEdit["tanggal_mulai"] ?? date("Y-m-d")); ?>">

    <p>Tanggal Selesai (boleh kosong):</p>
    <input type="date" name="tanggal_selesai"
           value="<?php echo h(($dataEdit["tanggal_selesai"] ?? "") === "0000-00-00" ? "" : ($dataEdit["tanggal_selesai"] ?? "")); ?>">

    <p>Status:</p>
    <select name="status_aktif" required>
        <option value="1" <?php echo (string)($dataEdit["status_aktif"] ?? "1") === "1" ? "selected" : ""; ?>>Aktif</option>
        <option value="0" <?php echo (string)($dataEdit["status_aktif"] ?? "") === "0" ? "selected" : ""; ?>>Tidak Aktif</option>
    </select>

    <p>
        <?php if ($dataEdit): ?>
            <button type="submit" name="edit">Simpan Perubahan</button>
            <a href="kelola_kelas_siswa.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah Penempatan</button>
        <?php endif; ?>
    </p>
</form>

<hr>

<h3>Daftar Penempatan Siswa</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Siswa</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $d): ?>
    <tr>
        <td><?php echo h($d["id"]); ?></td>
        <td><?php echo h($d["nama_siswa"] ?? "-"); ?></td>
        <td><?php echo h($d["nama_tahun"] ?? "-"); ?></td>
        <td><?php echo h($d["nama_kelas"] ?? "-"); ?></td>
        <td><?php echo h($d["tanggal_mulai"]); ?></td>
        <td><?php echo h($d["tanggal_selesai"] ?? "-"); ?></td>
        <td><?php echo $d["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?></td>
        <td>
            <a href="kelola_kelas_siswa.php?edit=<?php echo h($d["id"]); ?>">Edit</a>
            |
            <a href="kelola_kelas_siswa.php?hapus=<?php echo h($d["id"]); ?>"
               onclick="return confirm('Yakin menghapus penempatan ini?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>