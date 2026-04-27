<?php
/**
 * models/Barang.php
 * All queries use PDO Prepared Statements — no raw variables in SQL.
 */
require_once __DIR__ . '/../config/database.php';

class Barang
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->query(
            'SELECT * FROM barang ORDER BY created_at DESC'
        )->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM barang WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getStats(): array
    {
        return $this->db->query(
            'SELECT
                COUNT(*)                              AS total_items,
                COALESCE(SUM(stok), 0)                AS total_stok,
                COALESCE(SUM(stok * harga), 0)        AS total_nilai,
                SUM(CASE WHEN stok < 5 THEN 1 ELSE 0 END) AS stok_menipis
             FROM barang'
        )->fetch();
    }

    public function insert(array $d): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO barang
             (nama_barang, kategori, merek, stok, harga, kondisi, deskripsi, foto, foto_thumb)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $d['nama_barang'], $d['kategori'], $d['merek'],
            $d['stok'], $d['harga'], $d['kondisi'], $d['deskripsi'],
            $d['foto'], $d['foto_thumb'],
        ]);
    }

    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE barang
             SET nama_barang=?, kategori=?, merek=?, stok=?, harga=?,
                 kondisi=?, deskripsi=?, foto=?, foto_thumb=?
             WHERE id=?'
        );
        return $stmt->execute([
            $d['nama_barang'], $d['kategori'], $d['merek'],
            $d['stok'], $d['harga'], $d['kondisi'], $d['deskripsi'],
            $d['foto'], $d['foto_thumb'], $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM barang WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
