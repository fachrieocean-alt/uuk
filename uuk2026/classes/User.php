<?php
require_once __DIR__ . '/Database.php';

class User
{
    private PDO $pdo;
    private ?int $id = null;
    private string $username = '';

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? (new Database())->getConnection();
    }

    public function getId(): ?int { return $this->id; }
    public function getUsername(): string { return $this->username; }

    /** Cek login. Mengembalikan true jika berhasil dan mengisi id/username. */
    public function login(string $username, string $password): bool
    {
        $st = $this->pdo->prepare('SELECT id_user, username, password FROM users WHERE username = :u');
        $st->execute([':u' => $username]);
        $row = $st->fetch();
        if ($row && password_verify($password, $row['password'])) {
            $this->id = (int) $row['id_user'];
            $this->username = $row['username'];
            return true;
        }
        return false;
    }
}
