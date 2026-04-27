<?php
/**
 * controllers/DashboardController.php
 * Fetches summary stats and recent data for the dashboard view.
 * Security: requireAuth guard on index().
 */
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController
{
    public static function index(): void
    {
        AuthController::requireAuth();

        $db = Database::getInstance();

        // ── Real SQL stats ──────────────────────────────────────
        $totalBarang = (int) $db->query(
            'SELECT COUNT(*) FROM barang'
        )->fetchColumn();

        $stokMenipis = (int) $db->query(
            'SELECT COUNT(*) FROM barang WHERE stok < 5'
        )->fetchColumn();

        $totalNilai = (float) $db->query(
            'SELECT COALESCE(SUM(stok * harga), 0) FROM barang'
        )->fetchColumn();

        $totalKategori = (int) $db->query(
            'SELECT COUNT(DISTINCT kategori) FROM barang'
        )->fetchColumn();

        // Dummy — peminjaman table not built yet
        $peminjamanAktif = 0;

        // ── Recent items (last 5) ───────────────────────────────
        $recentItems = $db->query(
            'SELECT id, nama_barang, kategori, merek, stok, kondisi, foto_thumb
             FROM barang ORDER BY created_at DESC LIMIT 5'
        )->fetchAll();

        // ── Render view ─────────────────────────────────────────
        $pageTitle = 'Dashboard';
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
