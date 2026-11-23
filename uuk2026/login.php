<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/classes/User.php';

if (is_logged_in()) {
    header('Location: rekap.php');
    exit;
}

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = new User();
    // Pesan sengaja tidak menyebutkan bagian mana yang salah.
    if ($username !== '' && $password !== '' && $user->login($username, $password)) {
        session_regenerate_id(true);
        $_SESSION['id_user'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        header('Location: rekap.php');
        exit;
    }
    $error = 'Username atau password salah.';
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="login-box card">
    <h1>Login Administrator</h1>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" required autofocus>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
