<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/classes/Siswa.php';
require_once __DIR__ . '/classes/Kelas.php';

$model = new Siswa();
$daftarKelas = (new Kelas())->getAll();
$nisnLama = $_GET['nisn'] ?? null;
$errors = [];
$data = ['nisn' => '', 'nama' => '', 'alamat' => '', 'id_kelas' => ''];

if ($nisnLama !== null) {
    $row = $model->find($nisnLama);
    if (!$row) {
        flash('error', 'Data siswa tidak ditemukan.');
        header('Location: siswa.php');
        exit;
    }
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisnLama = ($_POST['nisn_lama'] ?? '') !== '' ? $_POST['nisn_lama'] : null;
    $data = [
        'nisn' => $_POST['nisn'] ?? '',
        'nama' => $_POST['nama'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'id_kelas' => $_POST['id_kelas'] ?? '',
    ];
    $model->setNisn($data['nisn']);
    $model->setNama($data['nama']);
    $model->setAlamat($data['alamat']);
    $model->setIdKelas(ctype_digit((string) $data['id_kelas']) ? (int) $data['id_kelas'] : null);

    $errors = $model->validate($nisnLama);
    if (!$errors) {
        if ($nisnLama === null) {
            $model->insert();
            flash('success', 'Siswa berhasil ditambahkan.');
        } else {
            $model->update($nisnLama);
            flash('success', 'Data siswa berhasil diubah.');
        }
        header('Location: siswa.php');
        exit;
    }
}

$pageTitle = $nisnLama === null ? 'Tambah Siswa' : 'Ubah Siswa';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1><?= e($pageTitle) ?></h1></div>
<div class="card form-card">
    <form method="post" novalidate>
        <input type="hidden" name="nisn_lama" value="<?= e($nisnLama ?? '') ?>">

        <label for="nisn">NISN (10 digit angka)</label>
        <input type="text" id="nisn" name="nisn" maxlength="10" inputmode="numeric" value="<?= old($data, 'nisn') ?>" class="<?= isset($errors['nisn']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['nisn'])): ?><div class="field-error"><?= e($errors['nisn']) ?></div><?php endif; ?>

        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" maxlength="100" value="<?= old($data, 'nama') ?>" class="<?= isset($errors['nama']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['nama'])): ?><div class="field-error"><?= e($errors['nama']) ?></div><?php endif; ?>

        <label for="alamat">Alamat (boleh kosong)</label>
        <input type="text" id="alamat" name="alamat" maxlength="255" value="<?= old($data, 'alamat') ?>" class="<?= isset($errors['alamat']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['alamat'])): ?><div class="field-error"><?= e($errors['alamat']) ?></div><?php endif; ?>

        <label for="id_kelas">Kelas</label>
        <select id="id_kelas" name="id_kelas" class="<?= isset($errors['id_kelas']) ? 'invalid' : '' ?>">
            <option value="">-- Pilih kelas --</option>
            <?php foreach ($daftarKelas as $k): ?>
                <option value="<?= (int) $k['id_kelas'] ?>" <?= (string) $data['id_kelas'] === (string) $k['id_kelas'] ? 'selected' : '' ?>>
                    <?= e($k['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['id_kelas'])): ?><div class="field-error"><?= e($errors['id_kelas']) ?></div><?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="siswa.php" class="btn">Batal</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
