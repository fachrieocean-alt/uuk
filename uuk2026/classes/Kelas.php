<?php
require_once __DIR__ . '/BaseModel.php';

class Kelas extends BaseModel
{
    private ?int $idKelas = null;
    private string $namaKelas = '';
    private string $jurusan = '';

    public function __construct(?PDO $pdo = null)
    {
        parent::__construct($pdo);
    }

    public function getIdKelas(): ?int { return $this->idKelas; }
    public function setIdKelas(?int $v): void { $this->idKelas = $v; }
    public function getNamaKelas(): string { return $this->namaKelas; }
    public function setNamaKelas(string $v): void { $this->namaKelas = trim($v); }
    public function getJurusan(): string { return $this->jurusan; }
    public function setJurusan(string $v): void { $this->jurusan = trim($v); }

    public function getAll(): array
    {
        return $this->pdo->query(
            'SELECT k.*, (SELECT COUNT(*) FROM siswa s WHERE s.id_kelas = k.id_kelas) AS jumlah_siswa
             FROM kelas k ORDER BY k.nama_kelas'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM kelas WHERE id_kelas = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function namaSudahAda(string $nama, ?int $exceptId = null): bool
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM kelas WHERE nama_kelas = :n AND id_kelas <> :id');
        $st->execute([':n' => $nama, ':id' => $exceptId ?? 0]);
        return (int) $st->fetchColumn() > 0;
    }

    /** @return array<string,string> pesan kesalahan per kolom */
    public function validate(): array
    {
        $err = [];
        if ($this->namaKelas === '') {
            $err['nama_kelas'] = 'Nama kelas wajib diisi.';
        } elseif (mb_strlen($this->namaKelas) > 20) {
            $err['nama_kelas'] = 'Nama kelas maksimal 20 karakter.';
        } elseif ($this->namaSudahAda($this->namaKelas, $this->idKelas)) {
            $err['nama_kelas'] = 'Nama kelas sudah dipakai.';
        }
        if ($this->jurusan === '') {
            $err['jurusan'] = 'Jurusan wajib diisi.';
        } elseif (mb_strlen($this->jurusan) > 50) {
            $err['jurusan'] = 'Jurusan maksimal 50 karakter.';
        }
        return $err;
    }

    public function save(): void
    {
        if ($this->idKelas === null) {
            $st = $this->pdo->prepare('INSERT INTO kelas (nama_kelas, jurusan) VALUES (:n, :j)');
            $st->execute([':n' => $this->namaKelas, ':j' => $this->jurusan]);
            $this->idKelas = (int) $this->pdo->lastInsertId();
        } else {
            $st = $this->pdo->prepare('UPDATE kelas SET nama_kelas = :n, jurusan = :j WHERE id_kelas = :id');
            $st->execute([':n' => $this->namaKelas, ':j' => $this->jurusan, ':id' => $this->idKelas]);
        }
    }

    /** Mengembalikan null jika berhasil, atau pesan yang mudah dipahami jika ditolak. */
    public function delete(int $id): ?string
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM siswa WHERE id_kelas = :id');
        $st->execute([':id' => $id]);
        $jumlah = (int) $st->fetchColumn();
        if ($jumlah > 0) {
            return "Kelas tidak dapat dihapus karena masih memiliki $jumlah siswa. Pindahkan atau hapus siswanya terlebih dahulu.";
        }
        $st = $this->pdo->prepare('DELETE FROM kelas WHERE id_kelas = :id');
        $st->execute([':id' => $id]);
        return null;
    }
}
