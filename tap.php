<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/classes/Presensi.php';

$result = null;
$nisn = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');
    $model = new Presensi();
    $model->setNisn($nisn);
    $result = $model->tap();
    if ($result['ok']) {
        $nisn = '';
    }
}

$pageTitle = 'Simulasi Tap Kartu';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1>Simulasi Tap Kartu</h1></div>
<div class="card form-card">
    <p class="muted">Form ini mewakili mesin RFID di gerbang. Hanya NISN yang dikirim; ID dan waktu diisi otomatis oleh database.</p>
    <?php if ($result): ?>
        <div class="alert alert-<?= $result['ok'] ? 'success' : 'error' ?>"><?= e($result['message']) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <label for="nisn">NISN</label>
        <input type="text" id="nisn" name="nisn" maxlength="10" inputmode="numeric" value="<?= e($nisn) ?>" placeholder="Contoh: 0012345678" autofocus>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Tap Kartu</button>
            <a href="rekap.php" class="btn">Lihat Rekap</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
