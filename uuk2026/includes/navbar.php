<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>
<nav class="navbar">
    <div class="nav-inner">
        <a class="brand" href="rekap.php">Presensi SMK TI Airlangga</a>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-burger" aria-label="Menu">&#9776;</label>
        <ul class="nav-links">
            <li><a href="rekap.php" class="<?= $current === 'rekap.php' ? 'active' : '' ?>">Rekap Presensi</a></li>
            <li><a href="tap.php" class="<?= $current === 'tap.php' ? 'active' : '' ?>">Simulasi Tap</a></li>
            <li><a href="siswa.php" class="<?= in_array($current, ['siswa.php', 'siswa_form.php'], true) ? 'active' : '' ?>">Siswa</a></li>
            <li><a href="kelas.php" class="<?= in_array($current, ['kelas.php', 'kelas_form.php'], true) ? 'active' : '' ?>">Kelas</a></li>
            <li><a href="logout.php" class="logout">Logout (<?= e($_SESSION['username']) ?>)</a></li>
        </ul>
    </div>
</nav>
