<?php
require_once __DIR__ . '/BaseModel.php';

class Siswa extends BaseModel
{
    private string $nisn = '';
    private string $nama = '';
    private ?string $alamat = null;
    private ?int $idKelas = null;

    public function __construct(?PDO $pdo = null)
    {
        parent::__construct($pdo);
    }

    public function getNisn(): string { return $this->nisn; }
    public function setNisn(string $v): void { $this->nisn = trim($v); }
    public function getNama(): string { return $this->nama; }
    public function setNama(string $v): void { $this->nama = trim($v); }
    public function getAlamat(): ?string { return $this->alamat; }
    public function setAlamat(?string $v): void
    {
        $v = trim((string) $v);
        $this->alamat = $v === '' ? null : $v;
    }
    public function getIdKelas(): ?int { return $this->idKelas; }
    public function setIdKelas(?int $v): void { $this->idKelas = $v; }

    public function getAll(): array
    {
        return $this->search('');
    }

    /** Daftar siswa + nama kelas (INNER JOIN), dengan pencarian nama/NISN. */
    public function search(string $q): array
    {
        $sql = 'SELECT s.nisn, s.nama, s.alamat, s.id_kelas, k.nama_kelas
                FROM siswa s
                INNER JOIN kelas k ON k.id_kelas = s.id_kelas';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE s.nama LIKE :q OR s.nisn LIKE :q';
            $params[':q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY k.nama_kelas, s.nama';
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public function find(string $nisn): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM siswa WHERE nisn = :n');
        $st->execute([':n' => $nisn]);
        return $st->fetch() ?: null;
    }

    public function exists(string $nisn): bool
    {
        return $this->find($nisn) !== null;
    }

    /**
     * @param string|null $nisnLama NISN sebelum diubah (null saat tambah)
     * @return array<string,string> pesan kesalahan per kolom
     */
    public function validate(?string $nisnLama = null): array
    {
        $err = [];
        if ($this->nisn === '') {
            $err['nisn'] = 'NISN wajib diisi.';
        } elseif (!preg_match('/^\d{10}$/', $this->nisn)) {
            $err['nisn'] = 'NISN harus tepat 10 digit angka.';
        } elseif ($this->nisn !== $nisnLama && $this->exists($this->nisn)) {
            $err['nisn'] = 'NISN sudah terdaftar.';
        }

        if ($this->nama === '') {
            $err['nama'] = 'Nama wajib diisi.';
        } elseif (mb_strlen($this->nama) > 100) {
            $err['nama'] = 'Nama maksimal 100 karakter.';
        }

        if ($this->alamat !== null && mb_strlen($this->alamat) > 255) {
            $err['alamat'] = 'Alamat maksimal 255 karakter.';
        }

        if ($this->idKelas === null) {
            $err['id_kelas'] = 'Kelas wajib dipilih.';
        } else {
            $st = $this->pdo->prepare('SELECT COUNT(*) FROM kelas WHERE id_kelas = :id');
            $st->execute([':id' => $this->idKelas]);
            if ((int) $st->fetchColumn() === 0) {
                $err['id_kelas'] = 'Kelas yang dipilih tidak ada.';
            }
        }
        return $err;
    }

    public function insert(): void
    {
        $st = $this->pdo->prepare(
            'INSERT INTO siswa (nisn, nama, alamat, id_kelas) VALUES (:nisn, :nama, :alamat, :k)'
        );
        $st->execute([
            ':nisn' => $this->nisn,
            ':nama' => $this->nama,
            ':alamat' => $this->alamat,
            ':k' => $this->idKelas,
        ]);
    }

    public function update(string $nisnLama): void
    {
        $st = $this->pdo->prepare(
            'UPDATE siswa SET nisn = :nisn, nama = :nama, alamat = :alamat, id_kelas = :k WHERE nisn = :lama'
        );
        $st->execute([
            ':nisn' => $this->nisn,
            ':nama' => $this->nama,
            ':alamat' => $this->alamat,
            ':k' => $this->idKelas,
            ':lama' => $nisnLama,
        ]);
    }

    public function delete(string $nisn): void
    {
        $st = $this->pdo->prepare('DELETE FROM siswa WHERE nisn = :n');
        $st->execute([':n' => $nisn]);
    }
}
