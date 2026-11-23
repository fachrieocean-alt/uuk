<?php
// Seeder: data awal kelas, akun admin, dan contoh siswa.
// Jalankan sekali setelah import schema.sql:  php seed.php  (atau buka lewat browser)
require __DIR__ . '/classes/Database.php';

$pdo = (new Database())->getConnection();

$kelas = [
    ['X-PPLG', 'PPLG'],
    ['XI-PPLG', 'PPLG'],
    ['XII-PPLG', 'PPLG'],
];
$st = $pdo->prepare('INSERT IGNORE INTO kelas (nama_kelas, jurusan) VALUES (:n, :j)');
foreach ($kelas as [$n, $j]) {
    $st->execute([':n' => $n, ':j' => $j]);
}

$st = $pdo->prepare('INSERT IGNORE INTO users (username, password) VALUES (:u, :p)');
$st->execute([':u' => 'admin', ':p' => password_hash('admin123', PASSWORD_DEFAULT)]);

$idKelas = (int) $pdo->query("SELECT id_kelas FROM kelas WHERE nama_kelas = 'X-PPLG'")->fetchColumn();
$siswa = [
    ['0012345678', 'Andi Pratama', 'Samarinda'],
    ['0098765432', 'Siti Aisyah', 'Sungai Kunjang'],
    ['1234567890', 'Budi Santoso', 'Samarinda Ulu'],
];
$st = $pdo->prepare('INSERT IGNORE INTO siswa (nisn, nama, alamat, id_kelas) VALUES (:nisn, :nama, :alamat, :k)');
foreach ($siswa as [$nisn, $nama, $alamat]) {
    $st->execute([':nisn' => $nisn, ':nama' => $nama, ':alamat' => $alamat, ':k' => $idKelas]);
}

echo "Seeder selesai. Login: admin / admin123\n";