<?php
/**
 * controllers/BarangController.php
 *
 * Week 7 — Dual-Save File Upload Logic:
 *   1. Original  → uploads/original/<uniqid>.ext   (high-res)
 *   2. Thumbnail → uploads/thumbs/<uniqid>.ext     (max 150px wide, GD imagecopyresampled)
 *
 * Both files share the SAME base filename.
 * Views use thumbs/ in tables, original/ in detail/edit.
 *
 * Security: requireAuth, CSRF, finfo MIME, 2MB limit, uniqid() naming.
 */
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class BarangController
{
    // ── Directory constants ───────────────────────────────────────
    private const ORIGINAL_DIR = __DIR__ . '/../uploads/original/';
    private const THUMB_DIR    = __DIR__ . '/../uploads/thumbs/';
    private const MAX_SIZE     = 2 * 1024 * 1024;           // 2 MB
    private const THUMB_W      = 150;                        // max thumbnail width (px)
    private const ALLOWED_MIME = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

    // ── index ─────────────────────────────────────────────────────
    public static function index(): void
    {
        AuthController::requireAuth();
        $model     = new Barang();
        $items     = $model->getAll();
        $stats     = $model->getStats();
        $pageTitle = 'Data Barang';
        require_once __DIR__ . '/../views/barang/index.php';
    }

    // ── create (show form) ────────────────────────────────────────
    public static function create(): void
    {
        AuthController::requireAuth();
        AuthController::generateCsrfToken();
        $pageTitle = 'Tambah Barang';
        require_once __DIR__ . '/../views/barang/create.php';
    }

    // ── store (process POST) ──────────────────────────────────────
    public static function store(): void
    {
        AuthController::requireAuth();
        self::verifyCsrf();

        $data = self::validateInput();

        // ── DUAL-SAVE upload logic ────────────────────────────────
        $upload = self::processUpload();          // returns ['filename'=>'xx.jpg'] or null
        $data['foto']       = $upload['filename'] ?? null;   // same name for both dirs
        $data['foto_thumb'] = $upload['filename'] ?? null;

        (new Barang())->insert($data);

        $_SESSION['flash_success'] = 'Barang berhasil ditambahkan!';
        header('Location: index.php?page=barang');
        exit;
    }

    // ── edit (show form) ──────────────────────────────────────────
    public static function edit(): void
    {
        AuthController::requireAuth();
        $item = self::findOrFail((int)($_GET['id'] ?? 0));
        AuthController::generateCsrfToken();
        $pageTitle = 'Edit Barang';
        require_once __DIR__ . '/../views/barang/edit.php';
    }

    // ── update (process POST) ─────────────────────────────────────
    public static function update(): void
    {
        AuthController::requireAuth();
        self::verifyCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $old  = self::findOrFail($id);
        $data = self::validateInput();

        // ── DUAL-SAVE upload logic (only if new file provided) ────
        $upload = self::processUpload();
        if ($upload) {
            // Delete old originals and thumbs before saving new ones
            self::deleteFiles($old['foto']);
            $data['foto']       = $upload['filename'];
            $data['foto_thumb'] = $upload['filename'];   // same base name
        } else {
            // Keep existing filenames unchanged
            $data['foto']       = $old['foto'];
            $data['foto_thumb'] = $old['foto_thumb'];
        }

        (new Barang())->update($id, $data);

        $_SESSION['flash_success'] = 'Barang berhasil diperbarui!';
        header('Location: index.php?page=barang');
        exit;
    }

    // ── delete ────────────────────────────────────────────────────
    public static function delete(): void
    {
        AuthController::requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $old = self::findOrFail($id);
        self::deleteFiles($old['foto']);
        (new Barang())->delete($id);
        $_SESSION['flash_success'] = 'Barang berhasil dihapus.';
        header('Location: index.php?page=barang');
        exit;
    }

    // ================================================================
    //  PRIVATE HELPERS
    // ================================================================

    /** Verify CSRF token from POST. */
    private static function verifyCsrf(): void
    {
        if (
            empty($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
        ) {
            $_SESSION['flash_error'] = 'Token keamanan tidak valid.';
            header('Location: index.php?page=barang');
            exit;
        }
        unset($_SESSION['csrf_token']);
    }

    /** Server-side validation. Returns clean data array or redirects. */
    private static function validateInput(): array
    {
        $cats  = ['Headset','Mouse','Keyboard','Controller','Mousepad','Webcam','Capture Card','Lainnya'];
        $conds = ['Baru','Baik','Rusak Ringan','Rusak Berat'];

        $nama     = trim($_POST['nama_barang'] ?? '');
        $kategori = $_POST['kategori'] ?? '';
        $merek    = trim($_POST['merek'] ?? '');
        $stok     = $_POST['stok'] ?? '';
        $harga    = $_POST['harga'] ?? '';
        $kondisi  = $_POST['kondisi'] ?? '';
        $desk     = trim($_POST['deskripsi'] ?? '');

        $errors = [];
        if ($nama === '')                       $errors[] = 'Nama barang wajib diisi.';
        if (strlen($nama) > 150)                $errors[] = 'Nama maks 150 karakter.';
        if (!in_array($kategori, $cats, true))  $errors[] = 'Kategori tidak valid.';
        if ($merek === '')                      $errors[] = 'Merek wajib diisi.';
        if (!is_numeric($stok)  || $stok < 0)  $errors[] = 'Stok harus angka ≥ 0.';
        if (!is_numeric($harga) || $harga < 0) $errors[] = 'Harga harus angka ≥ 0.';
        if (!in_array($kondisi, $conds, true))  $errors[] = 'Kondisi tidak valid.';

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $_SESSION['old_input']   = $_POST;
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=barang'));
            exit;
        }

        return [
            'nama_barang' => $nama,
            'kategori'       => $kategori,
            'merek'          => $merek,
            'stok'           => (int)$stok,
            'harga'          => (float)$harga,
            'kondisi'        => $kondisi,
            'deskripsi'      => $desk,
        ];
    }

    /**
     * WEEK 7 — Dual-Save Upload Logic
     *
     * Steps:
     *   1. Validate $_FILES error code
     *   2. Check size ≤ 2MB
     *   3. Verify MIME type via finfo_file() (not $_FILES['type'])
     *   4. Generate unique filename: uniqid('', true) . '.' . ext
     *   5. move_uploaded_file() → uploads/original/<filename>
     *   6. generateThumbnail()  → uploads/thumbs/<filename>  (max 150px wide, GD)
     *
     * @return array|null  ['filename' => 'abc123.jpg'] or null if no file.
     */
    private static function processUpload(): ?array
    {
        // No file selected — optional field
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        // 1. Check upload error code
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Upload gagal (kode error: ' . (int)$_FILES['foto']['error'] . ').';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=barang'));
            exit;
        }

        // 2. Size check — max 2MB
        if ($_FILES['foto']['size'] > self::MAX_SIZE) {
            $_SESSION['flash_error'] = 'Ukuran file maksimal 2MB.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=barang'));
            exit;
        }

        // 3. MIME type via finfo (NOT $_FILES['type'] — can be spoofed)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($_FILES['foto']['tmp_name']);

        if (!array_key_exists($mime, self::ALLOWED_MIME)) {
            $_SESSION['flash_error'] = 'Tipe file tidak diizinkan. Hanya JPG dan PNG.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=barang'));
            exit;
        }

        // 4. Generate unique filename (same base used for both original & thumb)
        $ext      = self::ALLOWED_MIME[$mime];
        $filename = uniqid('', true) . '.' . $ext;   // e.g. 65b2a1c9f3d84.jpg

        // Ensure directories exist
        if (!is_dir(self::ORIGINAL_DIR)) mkdir(self::ORIGINAL_DIR, 0755, true);
        if (!is_dir(self::THUMB_DIR))    mkdir(self::THUMB_DIR,    0755, true);

        // 5. Save original file (full resolution)
        $originalPath = self::ORIGINAL_DIR . $filename;
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $originalPath)) {
            $_SESSION['flash_error'] = 'Gagal menyimpan file ke server.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=barang'));
            exit;
        }

        // 6. Generate thumbnail with GD Library → uploads/thumbs/<filename>
        $thumbPath = self::THUMB_DIR . $filename;
        self::generateThumbnail($originalPath, $thumbPath, $mime);

        return ['filename' => $filename];
    }

    /**
     * GD Library — generate proportional thumbnail (max 150px wide).
     *
     * Uses imagecopyresampled() for high-quality downsampling.
     * Preserves PNG transparency.
     *
     * @param string $srcPath  Absolute path to original file.
     * @param string $destPath Absolute path for the thumbnail output.
     * @param string $mime     'image/jpeg' or 'image/png'.
     */
    private static function generateThumbnail(string $srcPath, string $destPath, string $mime): void
    {
        // Read source dimensions
        [$srcW, $srcH] = getimagesize($srcPath);

        // Calculate proportional thumbnail dimensions (max width = THUMB_W)
        if ($srcW <= self::THUMB_W) {
            // Already small enough — just copy as-is
            $thumbW = $srcW;
            $thumbH = $srcH;
        } else {
            $thumbW = self::THUMB_W;
            $thumbH = (int) round($srcH * (self::THUMB_W / $srcW));
        }

        // Create source GD resource
        $srcImg = match ($mime) {
            'image/png'  => imagecreatefrompng($srcPath),
            default      => imagecreatefromjpeg($srcPath),
        };

        // Create blank destination canvas
        $thumbImg = imagecreatetruecolor($thumbW, $thumbH);

        // Preserve alpha channel for PNG
        if ($mime === 'image/png') {
            imagealphablending($thumbImg, false);
            imagesavealpha($thumbImg, true);
            $transparent = imagecolorallocatealpha($thumbImg, 255, 255, 255, 127);
            imagefill($thumbImg, 0, 0, $transparent);
        }

        // Resample (high-quality resize)
        imagecopyresampled(
            $thumbImg,  // destination
            $srcImg,    // source
            0, 0,       // dest x, y
            0, 0,       // src x, y
            $thumbW, $thumbH,   // dest width, height
            $srcW, $srcH        // src width, height
        );

        // Save thumbnail to disk
        if ($mime === 'image/png') {
            imagepng($thumbImg, $destPath, 8);   // compression 8/9
        } else {
            imagejpeg($thumbImg, $destPath, 85); // quality 85%
        }

        // Free GD memory
        imagedestroy($srcImg);
        imagedestroy($thumbImg);
    }

    /** Delete both original and thumb files for a given base filename. */
    private static function deleteFiles(?string $filename): void
    {
        if (!$filename) return;
        $orig  = self::ORIGINAL_DIR . $filename;
        $thumb = self::THUMB_DIR    . $filename;
        if (file_exists($orig))  @unlink($orig);
        if (file_exists($thumb)) @unlink($thumb);
    }

    /** Find a record by ID or redirect with error. */
    private static function findOrFail(int $id): array
    {
        $item = (new Barang())->getById($id);
        if (!$item) {
            $_SESSION['flash_error'] = 'Data tidak ditemukan.';
            header('Location: index.php?page=barang');
            exit;
        }
        return $item;
    }
}
