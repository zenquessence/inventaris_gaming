<?php
/**
 * index.php — Front Controller / Central Router (updated Phase 3)
 * Default page is now 'dashboard'.
 */

session_start();
require_once __DIR__ . '/config/database.php';

$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

switch ($page) {

    // ── Authentication ────────────────────────────────────────────
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'proses') {
            AuthController::processLogin();
        } else {
            AuthController::showLogin();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        AuthController::logout();
        break;

    // ── Dashboard ─────────────────────────────────────────────────
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        DashboardController::index();
        break;

    // ── Barang CRUD ────────────────────────────────────────────
    case 'barang':
        require_once __DIR__ . '/controllers/BarangController.php';
        switch ($action) {
            case 'create': BarangController::create(); break;
            case 'store':  BarangController::store();  break;
            case 'edit':   BarangController::edit();   break;
            case 'update': BarangController::update(); break;
            case 'delete': BarangController::delete(); break;
            default:       BarangController::index();  break;
        }
        break;

    // ── 404 ───────────────────────────────────────────────────────
    default:
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
              <title>404</title>
              <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
              <style>
                body{font-family:sans-serif;background:#060a12;color:#e2e8f0;
                     display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;}
                h1{font-family:"Orbitron",sans-serif;font-size:4rem;color:#7c3aed;}
                a{color:#a78bfa;}
              </style></head><body>
              <div><h1>404</h1><p>Halaman tidak ditemukan.</p>
              <p><a href="index.php">← Kembali ke Dashboard</a></p></div>
              </body></html>';
        break;
}
