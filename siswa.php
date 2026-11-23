<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/classes/Siswa.php';

$model = new Siswa();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    $model->delete($_POST['nisn'] ?? '');
    flash('success', 'Siswa berhasil dihapus (beserta log presensinya).');
    header('Location: siswa.php');
    exit;
}

$q = trim($_GET['q'] ?? '');
$daftar = $model->search($q);
$pageTitle = 'Kelola Siswa';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Kelola Siswa</h1>
    <a href="siswa_form.php" class="btn btn-primary">+ Tambah Siswa</a>
</div>

<form method="get" class="filter-bar card">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau NISN...">
    <button type="submit" class="btn btn-primary">Cari</button>
    <?php if ($q !== ''): ?><a href="siswa.php" class="btn">Reset</a><?php endif; ?>
</form>

<div class="card table-wrap">
    <table>
        <thead>
            <tr><th>No</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Alamat</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$daftar): ?>
            <tr><td colspan="6" class="empty">Tidak ada siswa<?= $q !== '' ? ' yang cocok dengan pencarian' : '' ?>.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftar as $i => $s): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($s['nisn']) ?></td>
                <td><?= e($s['nama']) ?></td>
                <td><?= e($s['nama_kelas']) ?></td>
                <td><?= e($s['alamat'] ?? '-') ?></td>
                <td class="actions">
                    <a href="siswa_form.php?nisn=<?= urlencode($s['nisn']) ?>" class="btn btn-small">Ubah</a>
                    <form method="post" onsubmit="return confirm('Hapus siswa ini beserta log presensinya?');">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="nisn" value="<?= e($s['nisn']) ?>">
                        <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
