<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login (sesuaikan dengan role jika perlu)
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Pastikan parameter ID ada di URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. Hapus terlebih dahulu data terkait di tabel anak (t_kelas_siswa)
    mysqli_query($koneksi, "DELETE FROM t_kelas_siswa WHERE siswa_id = '$id'");

    // 2. (Opsional) Jika ada tabel relasi lain seperti riwayat/pelanggaran siswa, hapus juga di sini:
    // mysqli_query($koneksi, "DELETE FROM t_pelanggaran_siswa WHERE siswa_id = '$id'");

    // 3. Setelah relasinya terhapus, baru hapus data utama di tabel t_siswa
    $query_hapus = mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id = '$id'");

    if ($query_hapus) {
        // Redirect kembali ke halaman data siswa setelah berhasil
        header("Location: data_siswa.php");
        exit;
    } else {
        echo "Gagal menghapus data siswa: " . mysqli_error($koneksi);
    }
} else {
    header("Location: data_siswa.php");
    exit;
}
?>