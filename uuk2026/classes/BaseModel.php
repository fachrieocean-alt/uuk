<?php
require_once __DIR__ . '/Database.php';

abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? (new Database())->getConnection();
    }

    abstract public function getAll(): array;
}
