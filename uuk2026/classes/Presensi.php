<?php
require_once __DIR__ . '/BaseModel.php';

class Presensi extends BaseModel
{
    private ?int $id = null;
    private string $nisn = '';
    private ?string $tanggalWaktu = null;

    public function __construct(?PDO $pdo = null)
    {
        parent::__construct($pdo);
    }

    public function getId(): ?int { return $this->id; }
    public function getNisn(): string { return $this->nisn; }
    public function setNisn(string $v): void { $this->nisn = trim($v); }
    public function getTanggalWaktu(): ?string { return $this->tanggalWaktu; }

    public function getAll(): array
    {
        return $this->filter(null, null, '');
    }

    /**
     * Rekap presensi: INNER JOIN presensi, siswa, kelas.
     * @param string|null $tanggal format Y-m-d
     * @param int|null    $idKelas
     * @param string      $q cari nama atau NISN
     */
    public function filter(?string $tanggal, ?int $idKelas, string $q): array
    {
        $sql = 'SELECT p.id, p.nisn, s.nama, k.nama_kelas, p.tanggal_waktu
                FROM presensi p
                INNER JOIN siswa s ON s.nisn = p.nisn
                INNER JOIN kelas k ON k.id_kelas = s.id_kelas
                WHERE 1=1';
        $params = [];
        if ($tanggal !== null && $tanggal !== '') {
            $sql .= ' AND DATE(p.tanggal_waktu) = :tgl';
            $params[':tgl'] = $tanggal;
        }
        if ($idKelas !== null) {
            $sql .= ' AND k.id_kelas = :kelas';
            $params[':kelas'] = $idKelas;
        }
        if ($q !== '') {
            $sql .= ' AND (s.nama LIKE :q OR s.nisn LIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY p.tanggal_waktu DESC, p.id DESC';
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /**
     * Simulasi tap kartu: hanya NISN yang dikirim; id dan tanggal_waktu diisi database.
     * @return array{ok:bool,message:string}
     */
    public function tap(): array
    {
        if (!preg_match('/^\d{10}$/', $this->nisn)) {
            return ['ok' => false, 'message' => 'NISN harus 10 digit angka.'];
        }
        $st = $this->pdo->prepare(
            'SELECT s.nama, k.nama_kelas FROM siswa s
             INNER JOIN kelas k ON k.id_kelas = s.id_kelas WHERE s.nisn = :n'
        );
        $st->execute([':n' => $this->nisn]);
        $siswa = $st->fetch();
        if (!$siswa) {
            return ['ok' => false, 'message' => 'Kartu ditolak: NISN ' . $this->nisn . ' tidak terdaftar di sistem.'];
        }
        $ins = $this->pdo->prepare('INSERT INTO presensi (nisn) VALUES (:n)');
        $ins->execute([':n' => $this->nisn]);
        $this->id = (int) $this->pdo->lastInsertId();
        return [
            'ok' => true,
            'message' => 'Presensi tercatat: ' . $siswa['nama'] . ' (' . $siswa['nama_kelas'] . ').',
        ];
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM presensi WHERE id = :id');
        $st->execute([':id' => $id]);
    }
}
