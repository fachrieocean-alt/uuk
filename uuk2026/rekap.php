<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/classes/Presensi.php';
require_once __DIR__ . '/classes/Kelas.php';

$model = new Presensi();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    $model->delete((int) ($_POST['id'] ?? 0));
    flash('success', 'Log presensi berhasil dihapus.');
    // Pertahankan filter yang sedang aktif.
    $qs = $_POST['qs'] ?? '';
    header('Location: rekap.php' . ($qs !== '' ? '?' . $qs : ''));
    exit;
}

// Bawaan: presensi hari ini. Jika filter dikirim (walau kosong), pakai nilai filter.
$tanggal = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : date('Y-m-d');
if ($tanggal !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $tanggal = date('Y-m-d');
}
$idKelas = (isset($_GET['kelas']) && ctype_digit($_GET['kelas'])) ? (int) $_GET['kelas'] : null;
$q = trim($_GET['q'] ?? '');

$daftar = $model->filter($tanggal !== '' ? $tanggal : null, $idKelas, $q);
$daftarKelas = (new Kelas())->getAll();
$queryString = http_build_query(['tanggal' => $tanggal, 'kelas' => $idKelas ?? '', 'q' => $q]);

$pageTitle = 'Rekap Presensi';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Rekap Presensi</h1>
    <span class="badge"><?= count($daftar) ?> hadir</span>
</div>

<form method="get" class="filter-bar card">
    <div>
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" value="<?= e($tanggal) ?>">
    </div>
    <div>
        <label for="kelas">Kelas</label>
        <select id="kelas" name="kelas">
            <option value="">Semua kelas</option>
            <?php foreach ($daftarKelas as $k): ?>
                <option value="<?= (int) $k['id_kelas'] ?>" <?= $idKelas === (int) $k['id_kelas'] ? 'selected' : '' ?>><?= e($k['nama_kelas']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="grow">
        <label for="q">Cari nama / NISN</label>
        <input type="text" id="q" name="q" value="<?= e($q) ?>" placeholder="Cari...">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="rekap.php" class="btn">Hari ini</a>
    </div>
</form>

<div class="card table-wrap">
    <table>
        <thead>
            <tr><th>No</th><th>Nama Siswa</th><th>NISN</th><th>Kelas</th><th>Tanggal &amp; Waktu</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$daftar): ?>
            <tr><td colspan="6" class="empty">Belum ada presensi untuk filter ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftar as $i => $p): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($p['nama']) ?></td>
                <td><?= e($p['nisn']) ?></td>
                <td><?= e($p['nama_kelas']) ?></td>
                <td><?= e($p['tanggal_waktu']) ?></td>
                <td class="actions">
                    <form method="post" onsubmit="return confirm('Hapus log presensi ini?');">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                        <input type="hidden" name="qs" value="<?= e($queryString) ?>">
                        <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
