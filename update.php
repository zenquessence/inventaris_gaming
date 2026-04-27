<?php
include 'koneksi.php';

$id       = $_POST['id'];
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

$old_file  = $_POST['old_file'];
$old_thumb = $_POST['old_thumb'];

// =============================
// DEFAULT FILE LAMA
// =============================

$filepath  = $old_file;
$thumbpath = $old_thumb;

// =============================
// FOLDER
// =============================

$dir_original = "uploads/original/";
$dir_thumb    = "uploads/thumbs/";

if (!is_dir($dir_original)) mkdir($dir_original, 0777, true);
if (!is_dir($dir_thumb)) mkdir($dir_thumb, 0777, true);

// =============================
// JIKA ADA UPLOAD GAMBAR BARU
// =============================

if ($_FILES['gambar']['name'] != "") {

    // validasi error
    if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Error upload: " . $_FILES['gambar']['error'] . "'); history.back();</script>";
        exit;
    }

    // size limit 2MB
    if ($_FILES['gambar']['size'] > 2097152) {
        echo "<script>alert('File terlalu besar (max 2MB)'); history.back();</script>";
        exit;
    }

    // extension validation
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_ext)) {
        echo "<script>alert('Ekstensi file tidak diizinkan! (Hanya jpg, jpeg, png)'); history.back();</script>";
        exit;
    }

    // nama file
    $filename = uniqid() . "_" . basename($_FILES['gambar']['name']);
    $path_original = $dir_original . $filename;

    // upload original
    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $path_original)) {
        die("Gagal upload gambar!");
    }

    // =============================
    // DETEKSI MIME
    // =============================
    
    //  mime_content_type : fungsi PHP untuk mendeteksi tipe konten (MIME type) file berdasarkan isinya
    // (bukan cuma ekstensi). Fungsi ini berguna untuk validasi unggahan file, 
    //memastikan keamanan, dan mengatur header respon HTTP yang tepat agar browser 
    //tahu cara menangani file tersebut (misal: image/jpeg,
    
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
    // BUAT THUMBNAIL
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
    // HAPUS FILE LAMA
    // =============================
    if (file_exists($old_file)) unlink($old_file);
    if (file_exists($old_thumb)) unlink($old_thumb);

    // set file baru
    $filepath  = $path_original;
    $thumbpath = $path_thumb;
}

// =============================
// UPDATE DATABASE
// =============================
$stmt = $conn->prepare("
    UPDATE barang 
    SET nama_barang=?, kategori=?, stok=?, harga=?, filepath=?, thumbpath=? 
    WHERE id=?
");

$stmt->execute([
    $nama,
    $kategori,
    $stok,
    $harga,
    $filepath,
    $thumbpath,
    $id
]);

header("Location: tampil_barang.php");
exit;
?>