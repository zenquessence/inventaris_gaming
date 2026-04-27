<?php
$pageTitle = 'Data Barang';
require_once __DIR__ . '/../layouts/header.php';
$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
?>

<!-- Stats Row -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
<?php
$cards = [
    ['🎮', number_format($stats['total_items']),    'Total Item',         '#a78bfa'],
    ['📦', number_format($stats['total_stok']),     'Total Stok',         '#60a5fa'],
    ['💰', 'Rp '.number_format($stats['total_nilai'],0,',','.'), 'Nilai Inventaris', '#4ade80'],
    ['⚠️', number_format($stats['stok_menipis']),   'Stok Menipis (<5)',  '#fbbf24'],
];
foreach ($cards as [$icon, $val, $label, $color]):
?>
<div class="card" style="padding:1.1rem 1.4rem;">
    <div style="font-size:1.6rem;margin-bottom:.4rem;"><?= $icon ?></div>
    <div style="font-size:1.35rem;font-weight:800;color:<?= $color ?>;"><?= $e($val) ?></div>
    <div style="font-size:.73rem;color:#64748b;margin-top:.25rem;text-transform:uppercase;letter-spacing:.04em;"><?= $e($label) ?></div>
</div>
<?php endforeach; ?>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.6rem;">
            <span style="font-size:1.2rem;">🎯</span>
            <h2>Data Gaming Cloud</h2>
            <span style="font-size:.75rem;color:#64748b;">(<?= count($items) ?> item)</span>
        </div>
        <a href="index.php?page=barang&action=create" class="btn btn-primary">
            + Tambah Barang
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Merek</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:3rem;color:#475569;">
                        <div style="font-size:2.5rem;margin-bottom:.75rem;">🎮</div>
                        <div>Belum ada data barang.</div>
                        <a href="index.php?page=barang&action=create"
                           style="color:#a78bfa;text-decoration:none;font-size:.85rem;">
                            + Tambah sekarang
                        </a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $i => $item):
                    // Table view → use thumbnail (fast loading, 150px wide)
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
                    <td style="color:#475569;font-size:.75rem;"><?= $i + 1 ?></td>
                    <td>
                        <?php if ($thumb): ?>
                            <img src="<?= $thumb ?>" alt="foto"
                                 style="width:48px;height:48px;object-fit:cover;border-radius:8px;
                                        border:1px solid rgba(99,102,241,.3);">
                        <?php else: ?>
                            <div style="width:48px;height:48px;border-radius:8px;
                                        background:rgba(124,58,237,.12);display:flex;
                                        align-items:center;justify-content:center;font-size:1.4rem;">🎮</div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600;"><?= $e($item['nama_barang']) ?></td>
                    <td><span style="font-size:.78rem;color:#94a3b8;"><?= $e($item['kategori']) ?></span></td>
                    <td><?= $e($item['merek']) ?></td>
                    <td>
                        <span style="font-weight:700;color:<?= (int)$item['stok'] < 5 ? '#fbbf24' : '#4ade80' ?>;">
                            <?= $e($item['stok']) ?>
                        </span>
                    </td>
                    <td style="font-size:.85rem;">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td><span class="badge <?= $bc ?>"><?= $e($item['kondisi']) ?></span></td>
                    <td>
                        <div style="display:flex;gap:.35rem;">
                            <a href="index.php?page=barang&action=edit&id=<?= (int)$item['id'] ?>"
                               class="btn btn-secondary btn-sm">✏️ Edit</a>
                            <a href="index.php?page=barang&action=delete&id=<?= (int)$item['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus <?= $e(addslashes($item['nama_barang'])) ?>?')">
                               🗑️ Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
