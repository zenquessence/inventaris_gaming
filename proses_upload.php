
// =============================
// proses_upload.php
// =============================
<?php
include 'koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$nama     = htmlspecialchars($_POST['nama']);
$kategori = htmlspecialchars($_POST['kategori']);
$stok     = isset($_POST['stok']) ? $_POST['stok'] : 0;
$harga    = isset($_POST['harga']) ? $_POST['harga'] : 0;

if (trim($nama) === '') {
    echo "<script>alert('Nama barang tidak boleh kosong!'); history.back();</script>";
    exit;
}
if (!is_numeric($stok) || $stok < 0) {
    echo "<script>alert('Stok tidak valid!'); history.back();</script>";
    exit;
}
if (!is_numeric($harga) || $harga < 0) {
    echo "<script>alert('Harga tidak valid!'); history.back();</script>";
    exit;
}

$stok = (int)$stok;
$harga = (float)$harga;

// =============================
// FOLDER
// =============================
$dir_original = "uploads/original/";
$dir_thumb    = "uploads/thumbs/";

if (!is_dir($dir_original)) mkdir($dir_original, 0777, true);
if (!is_dir($dir_thumb)) mkdir($dir_thumb, 0777, true);

// =============================
// VALIDASI UPLOAD
// =============================
if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('Error upload: " . $_FILES['gambar']['error'] . "'); history.back();</script>";
    exit;
}

if ($_FILES['gambar']['size'] > 2097152) { // 2MB
    echo "<script>alert('File terlalu besar (max 2MB)'); history.back();</script>";
    exit;
}

$allowed_ext = ['jpg', 'jpeg', 'png'];
$file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
if (!in_array($file_ext, $allowed_ext)) {
    echo "<script>alert('Ekstensi file tidak diizinkan! (Hanya jpg, jpeg, png)'); history.back();</script>";
    exit;
}

// =============================
// NAMA FILE
// =============================
$filename = uniqid() . "_" . basename($_FILES['gambar']['name']);
$path_original = $dir_original . $filename;

// =============================
// UPLOAD ORIGINAL
// =============================
if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $path_original)) {
    die("Gagal upload file!");
}

// =============================
// DETEKSI MIME
// =============================
$mime = mime_content_type($path_original);

switch ($mime) {
    case 'image/jpeg':
        $src = imagecreatefromjpeg($path_original);
        break;
    case 'image/png':
        $src = imagecreatefrompng($path_original);
        break;
    default:
        unlink($path_original);
        die("Format tidak didukung!");
}

if (!$src) {
    unlink($path_original);
    die("Gambar tidak valid!");
}

// =============================
// BUAT THUMBNAIL (100x100)
// =============================
$width  = imagesx($src);
$height = imagesy($src);

$tmp = imagecreatetruecolor(100, 100);

imagecopyresampled(
    $tmp, $src,
    0, 0, 0, 0,
    100, 100,
    $width, $height
);

$path_thumb = $dir_thumb . "thumb_" . $filename;
imagejpeg($tmp, $path_thumb, 80);

// =============================
// SIMPAN DATABASE
// =============================
$stmt = $conn->prepare("INSERT INTO barang(nama_barang,kategori,stok,harga,thumbpath,filepath) VALUES(?,?,?,?,?,?)");
$stmt->execute([
    $nama,
    $kategori,
    $stok,
    $harga,
    $path_thumb,
    $path_original
]);

header("Location: tampil_barang.php");
exit;
?>


// =============================
// tampil_barang.php (PAKAI THUMBNAIL)
// =============================
<?php include 'koneksi.php'; ?>

<h2>Data Inventaris</h2>
<a href="tambah_barang.php">+ Tambah</a><br><br>

<table border="1" cellpadding="10">
<tr>
<th>No</th>
<th>Nama</th>
<th>Kategori</th>
<th>Stok</th>
<th>Harga</th>
<th>Gambar</th>
<th>Aksi</th>
</tr>

<?php
$q = $conn->query("SELECT * FROM barang ORDER BY uploaded_at DESC");
$no = 1;
while($d = $q->fetch_assoc()){
?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($d['nama_barang']) ?></td>
<td><?= htmlspecialchars($d['kategori']) ?></td>
<td><?= $d['stok'] ?></td>
<td><?= $d['harga'] ?></td>

<td>
<img src="<?= $d['thumbpath'] ?>" width="80">
</td>

<td>
<a href="edit_barang.php?id=<?= $d['id'] ?>">
<button style="background:green;color:white">EDIT</button>
</a>

<form action="hapus.php" method="POST" style="display:inline">
<input type="hidden" name="id" value="<?= $d['id'] ?>">
<input type="hidden" name="file" value="<?= $d['filepath'] ?>">
<input type="hidden" name="thumb" value="<?= $d['thumbpath'] ?>">
<button style="background:red;color:white" onclick="return confirm('Yakin hapus?')">DELETE</button>
</form>
</td>
</tr>
<?php } ?>
</table>

// =============================
// CATATAN
// =============================
// filepath  -> gambar original
// thumbpath -> gambar kecil (ditampilkan)
