<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/classes/Kelas.php';

$model = new Kelas();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$errors = [];
$data = ['nama_kelas' => '', 'jurusan' => ''];

if ($id !== null) {
    $row = $model->find($id);
    if (!$row) {
        flash('error', 'Data kelas tidak ditemukan.');
        header('Location: kelas.php');
        exit;
    }
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = ['nama_kelas' => $_POST['nama_kelas'] ?? '', 'jurusan' => $_POST['jurusan'] ?? ''];
    $model->setIdKelas($id);
    $model->setNamaKelas($data['nama_kelas']);
    $model->setJurusan($data['jurusan']);
    $errors = $model->validate();
    if (!$errors) {
        $model->save();
        flash('success', $id === null ? 'Kelas berhasil ditambahkan.' : 'Kelas berhasil diubah.');
        header('Location: kelas.php');
        exit;
    }
}

$pageTitle = $id === null ? 'Tambah Kelas' : 'Ubah Kelas';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1><?= e($pageTitle) ?></h1></div>
<div class="card form-card">
    <form method="post" novalidate>
        <label for="nama_kelas">Nama Kelas</label>
        <input type="text" id="nama_kelas" name="nama_kelas" maxlength="20" value="<?= old($data, 'nama_kelas') ?>" class="<?= isset($errors['nama_kelas']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['nama_kelas'])): ?><div class="field-error"><?= e($errors['nama_kelas']) ?></div><?php endif; ?>

        <label for="jurusan">Jurusan</label>
        <input type="text" id="jurusan" name="jurusan" maxlength="50" value="<?= old($data, 'jurusan') ?>" class="<?= isset($errors['jurusan']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['jurusan'])): ?><div class="field-error"><?= e($errors['jurusan']) ?></div><?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="kelas.php" class="btn">Batal</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
