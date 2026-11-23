<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Presensi Siswa';
$flash = pull_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - Presensi Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php if (is_logged_in()) { require __DIR__ . '/navbar.php'; } ?>
<main class="container">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
