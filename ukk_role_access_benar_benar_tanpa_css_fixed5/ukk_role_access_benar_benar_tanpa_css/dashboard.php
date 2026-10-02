<?php
require 'includes/auth.php'; require 'config.php';
$title = 'Dashboard';
$role = strtolower($_SESSION['role'] ?? '');
$labels = [
 'admin'=>['title'=>'Dashboard Administrator','desc'=>'Kelola seluruh data sistem, pengguna, pelanggaran, laporan, dan export.'],
 'guru'=>['title'=>'Dashboard Guru','desc'=>'Catat pelanggaran dan pantau perkembangan siswa.'],
 'wali_kelas'=>['title'=>'Dashboard Wali Kelas','desc'=>'Pantau laporan, riwayat, dan rekap poin siswa di kelas.']
];
$view = $labels[$role] ?? $labels['guru'];
$tables = ['t_siswa'=>'Siswa','t_guru'=>'Guru','t_kelas'=>'Kelas','t_pelanggaran'=>'Jenis Pelanggaran','t_pelanggaran_siswa'=>'Catatan Pelanggaran'];
$counts=[]; foreach($tables as $t=>$label){ try{$counts[$label]=(int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();}catch(Throwable $e){$counts[$label]=0;} }
include 'includes/header.php';
?>
<h1><?= $view['title'] ?></h1>
<p><?= $view['desc'] ?></p>
<h2>Ringkasan Data</h2>
<table>
<tr>
<td><h2><?= $counts['Siswa'] ?></h2><strong>Siswa</strong></td>
<td><h2><?= $counts['Guru'] ?></h2><strong>Guru</strong></td>
<td><h2><?= $counts['Kelas'] ?></h2><strong>Kelas</strong></td>
<td><h2><?= $counts['Jenis Pelanggaran'] ?></h2><strong>Jenis Pelanggaran</strong></td>
<td><h2><?= $counts['Catatan Pelanggaran'] ?></h2><strong>Catatan</strong></td>
</tr></table>
<?php if ($role === 'admin'): ?>
<h2>Menu Administrator</h2><p>Semua menu pengelolaan tersedia pada panel kiri.</p>
<?php elseif ($role === 'guru'): ?>
<h2>Menu Guru</h2><p>Gunakan menu pelanggaran untuk mencatat dan memantau siswa.</p>
<?php elseif ($role === 'wali_kelas'): ?>
<h2>Menu Wali Kelas</h2><p>Gunakan menu monitoring untuk melihat laporan kelas.</p>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
