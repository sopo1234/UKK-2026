
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

// Tambah data wali kelas
if (isset($_POST["tambah"])) {
    $tahun = (int)$_POST["tahun_ajaran_id"];
    $kelas = (int)$_POST["kelas_id"];
    $guru = (int)$_POST["guru_id"];
    $mulai = $_POST["tanggal_mulai"];
    $selesai = $_POST["tanggal_selesai"] ?: null;
    $status = (int)$_POST["status_aktif"];

    if ($selesai !== null && $mulai > $selesai) {
        $pesan = "Tanggal mulai tidak boleh setelah tanggal selesai.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_wali_kelas
                (tahun_ajaran_id, kelas_id, guru_id,
                 tanggal_mulai, tanggal_selesai, status_aktif,
                 created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            $stmt->execute([
                $tahun, $kelas, $guru,
                $mulai, $selesai, $status
            ]);

            header("Location: kelola_wali_kelas.php?pesan=tambah");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal menambah wali kelas. Periksa data dan relasi tabel.";
        }
    }
}

// Edit data wali kelas
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $tahun = (int)$_POST["tahun_ajaran_id"];
    $kelas = (int)$_POST["kelas_id"];
    $guru = (int)$_POST["guru_id"];
    $mulai = $_POST["tanggal_mulai"];
    $selesai = $_POST["tanggal_selesai"] ?: null;
    $status = (int)$_POST["status_aktif"];

    if ($selesai !== null && $mulai > $selesai) {
        $pesan = "Tanggal mulai tidak boleh setelah tanggal selesai.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE t_wali_kelas SET
                 tahun_ajaran_id = ?, kelas_id = ?, guru_id = ?,
                 tanggal_mulai = ?, tanggal_selesai = ?,
                 status_aktif = ?, updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([
                $tahun, $kelas, $guru,
                $mulai, $selesai, $status, $id
            ]);

            header("Location: kelola_wali_kelas.php?pesan=edit");
            exit;
        } catch (PDOException $e) {
            $pesan = "Gagal mengubah data wali kelas.";
        }
    }
}

// Hapus data
if (isset($_GET["hapus"])) {
    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_wali_kelas WHERE id = ?"
        );
        $stmt->execute([(int)$_GET["hapus"]]);

        header("Location: kelola_wali_kelas.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Data tidak dapat dihapus.";
    }
}

// Pesan berhasil
if (isset($_GET["pesan"])) {
    $daftarPesan = [
        "tambah" => "Data wali kelas berhasil ditambahkan.",
        "edit" => "Data wali kelas berhasil diperbarui.",
        "hapus" => "Data wali kelas berhasil dihapus."
    ];

    $pesan = $daftarPesan[$_GET["pesan"]] ?? "";
}

// Data yang akan diedit
$dataEdit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_wali_kelas WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit"]]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataEdit) {
        $pesan = "Data wali kelas tidak ditemukan.";
    }
}

// Pilihan tahun ajaran
$tahun = $pdo->query(
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

// Pilihan kelas
$kelas = $pdo->query(
    "SELECT id, nama FROM t_kelas ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

// Pilihan guru
$guru = $pdo->query(
    "SELECT id, nama FROM t_guru ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

// Daftar wali kelas
$data = $pdo->query(
    "SELECT w.*,
            ta.nama AS nama_tahun,
            k.nama AS nama_kelas,
            g.nama AS nama_guru
     FROM t_wali_kelas w
     LEFT JOIN t_tahun_ajaran ta
        ON w.tahun_ajaran_id = ta.id
     LEFT JOIN t_kelas k
        ON w.kelas_id = k.id
     LEFT JOIN t_guru g
        ON w.guru_id = g.id
     ORDER BY w.id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Wali Kelas</title>
</head>
<body>

<h2>KELOLA WALI KELAS</h2>

<p><?php echo h($pesan); ?></p>

<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $dataEdit ? "Edit Wali Kelas" : "Tambah Wali Kelas"; ?></h3>

<form method="POST" action="kelola_wali_kelas.php">

    <?php if ($dataEdit): ?>
        <input type="hidden" name="id"
               value="<?php echo h($dataEdit["id"]); ?>">
    <?php endif; ?>

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

    <p>Guru:</p>
    <select name="guru_id" required>
        <option value="">-- Pilih Guru --</option>
        <?php foreach ($guru as $g): ?>
            <option value="<?php echo h($g["id"]); ?>"
                <?php echo (string)($dataEdit["guru_id"] ?? "") === (string)$g["id"] ? "selected" : ""; ?>>
                <?php echo h($g["nama"]); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>Tanggal Mulai:</p>
    <input type="date" name="tanggal_mulai" required
        value="<?php echo h($dataEdit["tanggal_mulai"] ?? date("Y-m-d")); ?>">

    <p>Tanggal Selesai (boleh kosong):</p>
    <input type="date" name="tanggal_selesai"
        value="<?php
        $tgl = $dataEdit["tanggal_selesai"] ?? "";
        echo h($tgl === "0000-00-00" ? "" : $tgl);
        ?>">

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
            <a href="kelola_wali_kelas.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah Wali Kelas</button>
        <?php endif; ?>
    </p>
</form>

<hr>

<h3>Daftar Wali Kelas</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Guru</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $d): ?>
    <tr>
        <td><?php echo h($d["id"]); ?></td>
        <td><?php echo h($d["nama_tahun"] ?? "-"); ?></td>
        <td><?php echo h($d["nama_kelas"] ?? "-"); ?></td>
        <td><?php echo h($d["nama_guru"] ?? "-"); ?></td>
        <td><?php echo h($d["tanggal_mulai"]); ?></td>
        <td><?php
            $tgl = $d["tanggal_selesai"] ?? "";
            echo h($tgl === "0000-00-00" ? "-" : $tgl);
        ?></td>
        <td><?php echo $d["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?></td>
        <td>
            <a href="kelola_wali_kelas.php?edit=<?php echo h($d["id"]); ?>">Edit</a>
            |
            <a href="kelola_wali_kelas.php?hapus=<?php echo h($d["id"]); ?>"
               onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>