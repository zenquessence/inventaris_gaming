<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layouts/header.php';
$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

// Time-based greeting
$hour     = (int) date('H');
$greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 17 ? 'Selamat Siang' : 'Selamat Malam');
$emoji    = $hour < 12 ? '☀️' : ($hour < 17 ? '🌤️' : '🌙');
$namaUser = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div>
        <div class="greeting"><?= $greeting ?>, <?= $namaUser ?>! 👋</div>
        <div class="sub">Berikut ringkasan inventaris barang gaming hari ini.</div>
    </div>
    <div class="emoji"><?= $emoji ?></div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon purple">🎮</div>
        <div class="stat-value purple"><?= $e($totalBarang) ?></div>
        <div class="stat-label">Total Barang</div>
        <div class="stat-sub"><?= $e($totalKategori) ?> kategori terdaftar</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">⚠️</div>
        <div class="stat-value amber"><?= $e($stokMenipis) ?></div>
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-sub">Item dengan stok &lt; 5 unit</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">📋</div>
        <div class="stat-value blue"><?= $e($peminjamanAktif) ?></div>
        <div class="stat-label">Peminjaman Aktif</div>
        <div class="stat-sub">Fitur segera hadir</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-value green" style="font-size:1.25rem;">
            Rp <?= number_format($totalNilai, 0, ',', '.') ?>
        </div>
        <div class="stat-label">Total Nilai Inventaris</div>
        <div class="stat-sub">Stok × Harga semua item</div>
    </div>

</div>

<!-- Recent Items Table -->
<div class="card">
    <div class="card-header" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.6rem;">
            <span style="font-size:1.1rem;">🕐</span>
            <h2>Barang Terbaru</h2>
        </div>
        <a href="index.php?page=barang" class="btn btn-secondary btn-sm">
            Lihat Semua →
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Merek</th>
                    <th>Stok</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($recentItems)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:var(--text-faint);">
                        Belum ada data.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentItems as $item):
                    $thumb = !empty($item['foto_thumb'])
                        ? 'uploads/thumbs/' . $e($item['foto_thumb']) : null;
                    $bc = match($item['kondisi']) {
                        'Baru'         => 'badge-baru',
                        'Baik'         => 'badge-baik',
                        'Rusak Ringan' => 'badge-rusak-ringan',
                        default        => 'badge-rusak-berat',
                    };
                ?>
                <tr>
                    <td>
                        <?php if ($thumb): ?>
                            <img src="<?= $thumb ?>" alt="foto"
                                 style="width:40px;height:40px;object-fit:cover;
                                        border-radius:8px;border:1px solid var(--border);">
                        <?php else: ?>
                            <div style="width:40px;height:40px;border-radius:8px;
                                        background:rgba(124,58,237,.1);display:flex;
                                        align-items:center;justify-content:center;">🎮</div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600;"><?= $e($item['nama_barang']) ?></td>
                    <td style="color:var(--text-muted);font-size:.82rem;"><?= $e($item['kategori']) ?></td>
                    <td><?= $e($item['merek']) ?></td>
                    <td>
                        <span style="font-weight:700;color:<?= (int)$item['stok'] < 5 ? '#fbbf24' : '#4ade80' ?>;">
                            <?= $e($item['stok']) ?>
                        </span>
                    </td>
                    <td><span class="badge <?= $bc ?>"><?= $e($item['kondisi']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
