<?php
class Database
{
    private PDO $pdo;

    public function __construct(
        string $host = 'localhost',
        string $dbname = 'presensi_siswa',
        string $user = 'root',
        string $pass = ''
    ) {
        $this->pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
