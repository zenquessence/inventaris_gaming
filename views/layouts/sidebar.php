<?php
/**
 * views/layouts/sidebar.php
 * Fixed left sidebar — included by header.php.
 */
$currentPage = $_GET['page'] ?? 'dashboard';
$userName    = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User',  ENT_QUOTES, 'UTF-8');
$userRole    = htmlspecialchars($_SESSION['role']         ?? 'staff', ENT_QUOTES, 'UTF-8');
$userInitial = strtoupper(mb_substr($_SESSION['nama_lengkap'] ?? 'U', 0, 1));
?>
<aside class="sidebar" role="navigation" aria-label="Sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="logo-icon">🎮</div>
        <div class="logo-text">
            GAMING INV
            <span>Inventaris Barang</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>

        <!-- Dashboard -->
        <a href="index.php?page=dashboard"
           class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <!-- Data Barang -->
        <a href="index.php?page=barang"
           class="nav-link <?= $currentPage === 'barang' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 12h2m4-4v2m4-6l-1 1M4.929 19.071A10 10 0 1 1 19.07 4.93"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            Data Barang
        </a>

        <!-- Peminjaman (placeholder) -->
        <a class="nav-link disabled" title="Coming Soon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <path d="M9 12h6M9 16h4"/>
            </svg>
            Peminjaman
            <span class="nav-badge">Soon</span>
        </a>

        <div class="nav-label" style="margin-top:.5rem;">Akun</div>

        <!-- Logout -->
        <a href="index.php?page=logout" class="nav-link"
           onclick="return confirm('Yakin ingin logout?')"
           style="color:#f87171;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
        </a>
    </nav>

    <!-- User Profile (sticky bottom) -->
    <div class="sidebar-user">
        <div class="user-avatar"><?= $userInitial ?></div>
        <div class="user-info">
            <div class="name"><?= $userName ?></div>
            <div class="role"><?= ucfirst($userRole) ?></div>
        </div>
        <a href="index.php?page=logout" class="user-logout"
           onclick="return confirm('Yakin ingin logout?')"
           title="Logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </a>
    </div>

</aside>
