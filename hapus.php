
// PROGRAM HAPUS DATA Barang

<?php
    include 'koneksi.php';

    $id=$_POST['id'];
    $file=$_POST['file'];
    $thumb=$_POST['thumb'];

    if(file_exists($file)) unlink($file);
    if(file_exists($thumb)) unlink($thumb);

    $stmt = $conn->prepare("DELETE FROM barang WHERE id=?");
    $stmt->execute([$id]);
    header("Location: tampil_barang.php");
?>
