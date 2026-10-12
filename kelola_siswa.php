<?php
session_start();
require_once __DIR__ . "/config.php";

error_reporting(E_ALL);
ini_set("display_errors", "1");

if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit;
}

function e($value): string
{
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$pesan = "";
$error = "";
$edit = null;

// Ambil nama kolom tabel agar kode lebih aman terhadap perbedaan struktur.
try {
    $columnStmt = $pdo->query("SHOW COLUMNS FROM t_siswa");
    $columnRows = $columnStmt->fetchAll(PDO::FETCH_ASSOC);
    $columns = array_column($columnRows, "Field");

    $requiredColumns = [
        "id", "nip", "nisn", "nama", "jenis_kelamin", "tanggal_lahir",
        "alamat", "status_aktif", "created_at", "updated_at"
    ];

    $missingColumns = array_values(array_diff($requiredColumns, $columns));

    if ($missingColumns) {
        throw new RuntimeException(
            "Kolom berikut tidak ditemukan di t_siswa: " .
            implode(", ", $missingColumns) .
            ". Periksa tab Struktur di phpMyAdmin."
        );
    }
} catch (Throwable $ex) {
    $error = "Gagal membaca struktur tabel t_siswa: " . $ex->getMessage();
    $columns = [];
}

// Proses tambah/edit siswa
if (
    !$error &&
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["simpan_siswa"])
) {
    $id = (int)($_POST["id"] ?? 0);
    $nip = trim($_POST["nip"] ?? "");
    $nisn = trim($_POST["nisn"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $jk = trim($_POST["jenis_kelamin"] ?? "");
    $tgl_lahir = trim($_POST["tanggal_lahir"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");
    $status_aktif = (int)($_POST["status_aktif"] ?? 1);

    if ($nip === "" || $nisn === "" || $nama === "") {
        $error = "NIP, NISN, dan nama siswa wajib diisi.";
    } elseif (!in_array($jk, ["L", "P"], true)) {
        $error = "Jenis kelamin harus L atau P.";
    } elseif (!in_array($status_aktif, [0, 1], true)) {
        $error = "Status aktif tidak valid.";
    } elseif ($tgl_lahir !== "" &&
              !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_lahir)) {
        $error = "Tanggal lahir harus menggunakan format YYYY-MM-DD.";
    } else {
        try {
            $check = $pdo->prepare(
                "SELECT id FROM t_siswa
                 WHERE (nip = ? OR nisn = ?) AND id <> ? LIMIT 1"
            );
            $check->execute([$nip, $nisn, $id]);

            if ($check->fetch(PDO::FETCH_ASSOC)) {
                $error = "NIP atau NISN sudah digunakan siswa lain.";
            } else {
                if ($id > 0) {
                    $stmt = $pdo->prepare(
                        "UPDATE t_siswa
                         SET nip = ?, nisn = ?, nama = ?, jenis_kelamin = ?,
                             tanggal_lahir = ?, alamat = ?, status_aktif = ?,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE id = ?"
                    );
                    $stmt->execute([
                        $nip, $nisn, $nama, $jk,
                        $tgl_lahir === "" ? null : $tgl_lahir,
                        $alamat === "" ? null : $alamat,
                        $status_aktif, $id
                    ]);
                } else {
                    $stmt = $pdo->prepare(
                        "INSERT INTO t_siswa
                         (nip, nisn, nama, jenis_kelamin, tanggal_lahir, alamat,
                          status_aktif, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?,
                                 CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)"
                    );
                    $stmt->execute([
                        $nip, $nisn, $nama, $jk,
                        $tgl_lahir === "" ? null : $tgl_lahir,
                        $alamat === "" ? null : $alamat,
                        $status_aktif
                    ]);
                }

                header("Location: kelola_siswa.php?pesan=simpan");
                exit;
            }
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            $error = "Gagal menyimpan siswa. Pastikan kolom tabel sesuai dan NIP/NISN tidak duplikat.";
        }
    }
}

// Proses hapus siswa. Tidak menghapus tabel/database.
if (
    !$error &&
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["hapus_id"])
) {
    try {
        $stmt = $pdo->prepare("DELETE FROM t_siswa WHERE id = ?");
        $stmt->execute([(int)$_POST["hapus_id"]]);
        header("Location: kelola_siswa.php?pesan=hapus");
        exit;
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        $error = "Siswa tidak dapat dihapus, mungkin masih dipakai data lain.";
    }
}

if (($_GET["pesan"] ?? "") === "simpan") {
    $pesan = "Data siswa berhasil disimpan.";
} elseif (($_GET["pesan"] ?? "") === "hapus") {
    $pesan = "Data siswa berhasil dihapus.";
}

// Ambil data untuk form edit
if (!$error && isset($_GET["edit"])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM t_siswa WHERE id = ?");
        $stmt->execute([(int)$_GET["edit"]]);
        $edit = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$edit) {
            $error = "Data siswa tidak ditemukan.";
        }
    } catch (PDOException $ex) {
        $error = "Gagal mengambil data siswa.";
    }
}

// Pencarian dan daftar siswa
$dataSiswa = [];
$cari = trim($_GET["cari"] ?? "");

if (!$error) {
    try {
        $sql = "SELECT id, nip, nisn, nama, jenis_kelamin, tanggal_lahir, alamat,
                       status_aktif, created_at, updated_at
                FROM t_siswa";
        $params = [];

        if ($cari !== "") {
            $sql .= " WHERE nip LIKE ? OR nisn LIKE ? OR nama LIKE ?";
            $keyword = "%" . $cari . "%";
            $params = [$keyword, $keyword, $keyword];
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $dataSiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        $error = "Gagal mengambil daftar siswa.";
        error_log($ex->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa</title>
</head>
<body>

<h2>Kelola Data Siswa</h2>
<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

<?php if ($pesan !== ""): ?>
    <p><?php echo e($pesan); ?></p>
<?php endif; ?>

<?php if ($error !== ""): ?>
    <p><strong>Terjadi kesalahan:</strong> <?php echo e($error); ?></p>
<?php endif; ?>

<?php if (!$error): ?>
    <hr>
    <h3><?php echo $edit ? "Edit Data Siswa" : "Tambah Data Siswa"; ?></h3>

    <form method="post" action="kelola_siswa.php">
        <input type="hidden" name="simpan_siswa" value="1">
        <input type="hidden" name="id" value="<?php echo (int)($edit["id"] ?? 0); ?>">

        <p>
            <label for="nip">NIP:</label><br>
            <input type="text" id="nip" name="nip" required
                   value="<?php echo e($edit["nip"] ?? ""); ?>">
        </p>

        <p>
            <label for="nisn">NISN:</label><br>
            <input type="text" id="nisn" name="nisn" required
                   value="<?php echo e($edit["nisn"] ?? ""); ?>">
        </p>

        <p>
            <label for="nama">Nama Siswa:</label><br>
            <input type="text" id="nama" name="nama" required
                   value="<?php echo e($edit["nama"] ?? ""); ?>">
        </p>

        <p>
            <label for="jenis_kelamin">Jenis Kelamin:</label><br>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="L" <?php echo (($edit["jenis_kelamin"] ?? "") === "L") ? "selected" : ""; ?>>Laki-laki</option>
                <option value="P" <?php echo (($edit["jenis_kelamin"] ?? "") === "P") ? "selected" : ""; ?>>Perempuan</option>
            </select>
        </p>

        <p>
            <label for="tanggal_lahir">Tanggal Lahir:</label><br>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                   value="<?php echo e($edit["tanggal_lahir"] ?? ""); ?>">
        </p>

        <p>
            <label for="alamat">Alamat:</label><br>
            <textarea id="alamat" name="alamat" rows="3" cols="40"><?php echo e($edit["alamat"] ?? ""); ?></textarea>
        </p>

        <p>
            <label for="status_aktif">Status Aktif:</label><br>
            <select id="status_aktif" name="status_aktif">
                <option value="1" <?php echo (int)($edit["status_aktif"] ?? 1) === 1 ? "selected" : ""; ?>>Aktif</option>
                <option value="0" <?php echo isset($edit["status_aktif"]) && (int)$edit["status_aktif"] === 0 ? "selected" : ""; ?>>Tidak Aktif</option>
            </select>
        </p>

        <button type="submit">Simpan</button>
        <?php if ($edit): ?>
            <a href="kelola_siswa.php">Batal</a>
        <?php endif; ?>
    </form>

    <hr>
    <h3>Daftar Data Siswa</h3>

    <form method="get" action="kelola_siswa.php">
        <input type="text" name="cari"
               placeholder="Cari NIP, NISN, atau nama"
               value="<?php echo e($cari); ?>">
        <button type="submit">Cari</button>
        <a href="kelola_siswa.php">Reset</a>
    </form>

    <br>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>No.</th>
            <th>NIP</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Status Aktif</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($dataSiswa): ?>
            <?php foreach ($dataSiswa as $i => $siswa): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo e($siswa["nip"]); ?></td>
                    <td><?php echo e($siswa["nisn"]); ?></td>
                    <td><?php echo e($siswa["nama"]); ?></td>
                    <td><?php echo e($siswa["jenis_kelamin"] === "L" ? "Laki-laki" : ($siswa["jenis_kelamin"] === "P" ? "Perempuan" : $siswa["jenis_kelamin"])); ?></td>
                    <td><?php echo e($siswa["tanggal_lahir"]); ?></td>
                    <td><?php echo e($siswa["alamat"]); ?></td>
                    <td><?php echo (int)$siswa["status_aktif"] === 1 ? "Aktif" : "Tidak Aktif"; ?></td>
                    <td><?php echo e($siswa["created_at"]); ?></td>
                    <td><?php echo e($siswa["updated_at"]); ?></td>
                    <td>
                        <a href="kelola_siswa.php?edit=<?php echo (int)$siswa["id"]; ?>">Edit</a>
                        <form method="post" action="kelola_siswa.php"
                              onsubmit="return confirm('Yakin ingin menghapus data siswa ini?');">
                            <input type="hidden" name="hapus_id"
                                   value="<?php echo (int)$siswa["id"]; ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">Data siswa tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
