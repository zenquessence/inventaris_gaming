<?php
$pageTitle = 'Edit Barang';
require_once __DIR__ . '/../layouts/header.php';
$e    = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$csrf = $e($_SESSION['csrf_token'] ?? '');

$cats  = ['Headset','Mouse','Keyboard','Controller','Mousepad','Webcam','Capture Card','Lainnya'];
$conds = ['Baru','Baik','Rusak Ringan','Rusak Berat'];

// $item is set by BarangController::edit()
// Edit page → show original high-res file (not thumbnail)
$origSrc = !empty($item['foto'])
    ? 'uploads/original/' . $e($item['foto']) : null;
?>

<div style="max-width:760px;">

    <!-- Breadcrumb -->
    <div style="margin-bottom:1.25rem;font-size:.8rem;color:#64748b;">
        <a href="index.php?page=barang" style="color:#a78bfa;text-decoration:none;">Data Barang</a>
        &rsaquo; Edit — <?= $e($item['nama_barang']) ?>
    </div>

    <div class="card">
        <div class="card-header">
            <span style="font-size:1.2rem;">✏️</span>
            <h2>Edit Barang</h2>
        </div>
        <div class="card-body">
            <form action="index.php?page=barang&action=update"
                  method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

                    <!-- Nama -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="nama_barang">NAMA BARANG <span style="color:#f87171;">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang"
                               class="form-control" required maxlength="150"
                               value="<?= $e($item['nama_barang']) ?>">
                    </div>

                    <!-- Kategori -->
                    <div class="form-group">
                        <label class="form-label" for="kategori">KATEGORI <span style="color:#f87171;">*</span></label>
                        <select id="kategori" name="kategori" class="form-control" required>
                            <?php foreach ($cats as $c): ?>
                            <option value="<?= $e($c) ?>" <?= $item['kategori'] === $c ? 'selected' : '' ?>>
                                <?= $e($c) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Merek -->
                    <div class="form-group">
                        <label class="form-label" for="merek">MEREK <span style="color:#f87171;">*</span></label>
                        <input type="text" id="merek" name="merek"
                               class="form-control" required maxlength="100"
                               value="<?= $e($item['merek']) ?>">
                    </div>

                    <!-- Stok -->
                    <div class="form-group">
                        <label class="form-label" for="stok">STOK <span style="color:#f87171;">*</span></label>
                        <input type="number" id="stok" name="stok"
                               class="form-control" required min="0" max="9999"
                               value="<?= $e($item['stok']) ?>">
                    </div>

                    <!-- Harga -->
                    <div class="form-group">
                        <label class="form-label" for="harga">HARGA (IDR) <span style="color:#f87171;">*</span></label>
                        <input type="number" id="harga" name="harga"
                               class="form-control" required min="0" step="500"
                               value="<?= $e($item['harga']) ?>">
                    </div>

                    <!-- Kondisi -->
                    <div class="form-group">
                        <label class="form-label" for="kondisi">KONDISI <span style="color:#f87171;">*</span></label>
                        <select id="kondisi" name="kondisi" class="form-control" required>
                            <?php foreach ($conds as $c): ?>
                            <option value="<?= $e($c) ?>" <?= $item['kondisi'] === $c ? 'selected' : '' ?>>
                                <?= $e($c) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="deskripsi">DESKRIPSI</label>
                        <textarea id="deskripsi" name="deskripsi"
                                  class="form-control"><?= $e($item['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <!-- Foto Upload -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="foto">GANTI FOTO <small style="color:#64748b;text-transform:none;">(kosongkan jika tidak ingin mengganti)</small></label>

                        <?php if ($origSrc): ?>
                        <div style="margin-bottom:.85rem;display:flex;align-items:center;gap:1rem;">
                            <!-- Edit/detail → load from uploads/original/ (high-res) -->
                            <img src="<?= $origSrc ?>" alt="Foto original saat ini"
                                 style="max-width:120px;max-height:120px;object-fit:contain;
                                        border-radius:10px;border:2px solid rgba(124,58,237,.4);">
                            <div>
                                <div style="font-size:.78rem;color:#94a3b8;">Foto saat ini <span style="color:#7c3aed;">(original)</span></div>
                                <div style="font-size:.72rem;color:#475569;"><?= $e($item['foto']) ?></div>
                                <div style="font-size:.7rem;color:#334155;margin-top:.15rem;">
                                    Thumbnail: <code style="color:#a78bfa;">uploads/thumbs/<?= $e($item['foto_thumb'] ?? '') ?></code>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div style="border:2px dashed rgba(124,58,237,.35);border-radius:10px;
                                    padding:1.25rem;text-align:center;background:rgba(124,58,237,.04);">
                            <input type="file" id="foto" name="foto"
                                   accept="image/jpeg,image/png"
                                   onchange="previewImage(this)"
                                   style="display:none;">
                            <label for="foto" style="cursor:pointer;color:#a78bfa;font-weight:600;font-size:.85rem;">
                                📷 Pilih foto baru
                            </label>
                            <p style="font-size:.72rem;color:#475569;margin-top:.3rem;">
                                JPG / PNG &bull; Maks. 2MB
                            </p>
                            <img id="img-preview" src=""
                                 style="display:none;max-width:140px;margin-top:.75rem;
                                        border-radius:8px;border:2px solid rgba(124,58,237,.4);">
                        </div>
                    </div>

                </div><!-- /grid -->

                <div style="display:flex;gap:.75rem;margin-top:.5rem;">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="index.php?page=barang" class="btn btn-secondary">✕ Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const prev = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
