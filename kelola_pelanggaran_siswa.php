```php
<?php
session_start();
require_once "config.php";

if (
    !isset($_SESSION["role"]) ||
    strtolower($_SESSION["role"]) !== "admin"
) {
    exit("Akses ditolak. Login sebagai Admin terlebih dahulu.");
}

function h($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

$pesan = "";

// Tambah data pelanggaran siswa
if (isset($_POST["tambah"])) {
    $tahun_id = (int)$_POST["tahun_ajaran_id"];
    $siswa_id = (int)$_POST["siswa_id"];
    $kelas_id = (int)$_POST["kelas_id"];
    $pelanggaran_id = (int)$_POST["pelanggaran_id"];
    $kategori_id = (int)$_POST["pelanggaran_kategori_id"];
    $guru_id = (int)$_POST["guru_id"];
    $tanggal = $_POST["tanggal"];
    $keterangan = trim($_POST["keterangan"]);
    $poin = (int)$_POST["poin"];
    $tindakan = trim($_POST["tindakan"]);
    $status = trim($_POST["status"]);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO t_pelanggaran_siswa
            (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id,
             nama_kelas, pelanggaran_id, nama_pelanggaran,
             pelanggaran_kategori_id, guru_id, nama_guru,
             tanggal, keterangan, poin, tindakan, status,
             created_at, updated_at)
            SELECT ?, s.id, s.nama, k.id, k.nama,
                   p.id, p.nama, ?, g.id, g.nama,
                   ?, ?, p.poin, ?, ?, NOW(), NOW()
            FROM t_siswa s
            JOIN t_kelas k ON k.id = ?
            JOIN t_pelanggaran p ON p.id = ?
            JOIN t_guru g ON g.id = ?
            WHERE s.id = ?"
        );

        $stmt->execute([
            $tahun_id, $kategori_id, $tanggal, $keterangan,
            $tindakan, $status, $kelas_id, $pelanggaran_id,
            $guru_id, $siswa_id
        ]);

        if ($stmt->rowCount() === 0) {
            $pesan = "Gagal: periksa ID siswa, kelas, pelanggaran, dan guru.";
        } else {
            header("Location: kelola_pelanggaran_siswa.php?pesan=tambah");
            exit;
        }
    } catch (PDOException $e) {
        $pesan = "Gagal menyimpan data. Periksa hubungan tabel dan ID yang dipilih.";
    }
}

// Hapus data
if (isset($_GET["hapus"])) {
    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_pelanggaran_siswa WHERE id = ?"
        );
        $stmt->execute([(int)$_GET["hapus"]]);

        header("Location: kelola_pelanggaran_siswa.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Data gagal dihapus.";
    }
}

// Edit data
if (isset($_POST["edit"])) {
    $id = (int)$_POST["id"];
    $tanggal = $_POST["tanggal"];
    $keterangan = trim($_POST["keterangan"]);
    $poin = (int)$_POST["poin"];
    $tindakan = trim($_POST["tindakan"]);
    $status = trim($_POST["status"]);

    try {
        $stmt = $pdo->prepare(
            "UPDATE t_pelanggaran_siswa
             SET tanggal = ?, keterangan = ?, poin = ?,
                 tindakan = ?, status = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->execute([
            $tanggal, $keterangan, $poin,
            $tindakan, $status, $id
        ]);

        header("Location: kelola_pelanggaran_siswa.php?pesan=edit");
        exit;
    } catch (PDOException $e) {
        $pesan = "Gagal memperbarui data.";
    }
}

// Notifikasi
if (isset($_GET["pesan"])) {
    if ($_GET["pesan"] === "tambah") {
        $pesan = "Pelanggaran siswa berhasil ditambahkan.";
    } elseif ($_GET["pesan"] === "hapus") {
        $pesan = "Data berhasil dihapus.";
    } elseif ($_GET["pesan"] === "edit") {
        $pesan = "Data berhasil diperbarui.";
    }
}

// Ambil data pilihan dari database
$tahun = $pdo->query(
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$siswa = $pdo->query(
    "SELECT id, nama FROM t_siswa ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$kelas = $pdo->query(
    "SELECT id, nama FROM t_kelas ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$jenis = $pdo->query(
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$kategori = $pdo->query(
    "SELECT id, nama FROM t_pelanggaran_kategori ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$guru = $pdo->query(
    "SELECT id, nama FROM t_guru ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

// Data yang akan diedit
$data_edit = null;

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM t_pelanggaran_siswa WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit"]]);
    $data_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Daftar pelanggaran siswa
$data = $pdo->query(
    "SELECT * FROM t_pelanggaran_siswa ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pelanggaran Siswa</title>
</head>
<body>

<h2>KELOLA PELANGGARAN SISWA</h2>

<p><?php echo h($pesan); ?></p>

<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

<hr>

<h3><?php echo $data_edit ? "Edit Pelanggaran" : "Tambah Pelanggaran"; ?></h3>

<form method="post">
    <?php if ($data_edit): ?>
        <input type="hidden" name="id"
            value="<?php echo h($data_edit["id"]); ?>">
    <?php else: ?>
        <p>Tahun Ajaran:</p>
        <select name="tahun_ajaran_id" required>
            <option value="">Pilih Tahun Ajaran</option>
            <?php foreach ($tahun as $t): ?>
                <option value="<?php echo h($t["id"]); ?>">
                    <?php echo h($t["nama"]); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>Siswa:</p>
        <select name="siswa_id" required>
            <option value="">Pilih Siswa</option>
            <?php foreach ($siswa as $s): ?>
                <option value="<?php echo h($s["id"]); ?>">
                    <?php echo h($s["nama"]); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>Kelas:</p>
        <select name="kelas_id" required>
            <option value="">Pilih Kelas</option>
            <?php foreach ($kelas as $k): ?>
                <option value="<?php echo h($k["id"]); ?>">
                    <?php echo h($k["nama"]); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>Jenis Pelanggaran:</p>
        <select name="pelanggaran_id" required>
            <option value="">Pilih Pelanggaran</option>
            <?php foreach ($jenis as $j): ?>
                <option value="<?php echo h($j["id"]); ?>">
                    <?php echo h($j["nama"]); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>Kategori Pelanggaran:</p>
        <select name="pelanggaran_kategori_id" required>
            <option value="">Pilih Kategori</option>
            <?php foreach ($kategori as $kt): ?>
                <option value="<?php echo h($kt["id"]); ?>">
                    <?php echo h($kt["nama"]); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>Guru:</p>
        <select name="guru_id" required>
            <option value="">Pilih Guru</option>
            <?php foreach ($guru as $g): ?>
                <option value="<?php echo h($g["id"]); ?>">
                    <?php echo h($g["nama"]); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <p>Tanggal:</p>
    <input type="date" name="tanggal" required
        value="<?php echo h($data_edit["tanggal"] ?? date("Y-m-d")); ?>">

    <p>Keterangan:</p>
    <textarea name="keterangan" required><?php
        echo h($data_edit["keterangan"] ?? "");
    ?></textarea>

    <p>Poin:</p>
    <input type="number" name="poin" min="0" required
        value="<?php echo h($data_edit["poin"] ?? "0"); ?>">

    <p>Tindakan:</p>
    <textarea name="tindakan" required><?php
        echo h($data_edit["tindakan"] ?? "");
    ?></textarea>

    <p>Status:</p>
    <select name="status" required>
        <?php
        $status_sekarang = $data_edit["status"] ?? "Belum Ditindaklanjuti";
        foreach (["Belum Ditindaklanjuti", "Diproses", "Selesai"] as $st):
        ?>
            <option value="<?php echo h($st); ?>"
                <?php echo $status_sekarang === $st ? "selected" : ""; ?>>
                <?php echo h($st); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>
        <?php if ($data_edit): ?>
            <button type="submit" name="edit">Simpan Perubahan</button>
            <a href="kelola_pelanggaran_siswa.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Simpan Pelanggaran</button>
        <?php endif; ?>
    </p>
</form>

<hr>

<h3>Daftar Pelanggaran Siswa</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Pelanggaran</th>
        <th>Guru</th>
        <th>Tanggal</th>
        <th>Poin</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($data as $d): ?>
        <tr>
            <td><?php echo h($d["id"]); ?></td>
            <td><?php echo h($d["nama_siswa"]); ?></td>
            <td><?php echo h($d["nama_kelas"]); ?></td>
            <td><?php echo h($d["nama_pelanggaran"]); ?></td>
            <td><?php echo h($d["nama_guru"]); ?></td>
            <td><?php echo h($d["tanggal"]); ?></td>
            <td><?php echo h($d["poin"]); ?></td>
            <td><?php echo h($d["status"]); ?></td>
            <td>
                <a href="?edit=<?php echo h($d["id"]); ?>">Edit</a> |
                <a href="?hapus=<?php echo h($d["id"]); ?>"
                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                    Hapus
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
```
