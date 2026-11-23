<?php
// Helper bersama: session, proteksi login, escape output, flash message.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['id_user'], $_SESSION['username']);
}

/** Proteksi halaman: arahkan ke login jika belum login. */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function old(array $data, string $key, string $default = ''): string
{
    return e($data[$key] ?? $default);
}
