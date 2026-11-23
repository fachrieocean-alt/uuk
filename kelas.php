<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/classes/Kelas.php';

$model = new Kelas();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    $id = (int) ($_POST['id_kelas'] ?? 0);
    try {
        $pesan = $model->delete($id);
        if ($pesan === null) {
            flash('success', 'Kelas berhasil dihapus.');
        } else {
            flash('error', $pesan);
        }
    } catch (PDOException $ex) {
        flash('error', 'Kelas tidak dapat dihapus saat ini. Silakan coba lagi.');
    }
    header('Location: kelas.php');
    exit;
}

$daftar = $model->getAll();
$pageTitle = 'Kelola Kelas';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Kelola Kelas</h1>
    <a href="kelas_form.php" class="btn btn-primary">+ Tambah Kelas</a>
</div>
<div class="card table-wrap">
    <table>
        <thead>
            <tr><th>No</th><th>Nama Kelas</th><th>Jurusan</th><th>Jumlah Siswa</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$daftar): ?>
            <tr><td colspan="5" class="empty">Belum ada kelas.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftar as $i => $k): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($k['nama_kelas']) ?></td>
                <td><?= e($k['jurusan']) ?></td>
                <td><?= (int) $k['jumlah_siswa'] ?></td>
                <td class="actions">
                    <a href="kelas_form.php?id=<?= (int) $k['id_kelas'] ?>" class="btn btn-small">Ubah</a>
                    <form method="post" onsubmit="return confirm('Hapus kelas ini?');">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id_kelas" value="<?= (int) $k['id_kelas'] ?>">
                        <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
