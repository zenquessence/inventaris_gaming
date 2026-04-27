<?php
    include 'koneksi.php';
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM barang WHERE id=?");
    $stmt->execute([$id]);
    $d = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

        <h2>Koreksi Barang</h2>
        <form action="update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $d['id'] ?>">
        <input type="hidden" name="old_file" value="<?= $d['filepath'] ?>">
        <input type="hidden" name="old_thumb" value="<?= $d['thumbpath'] ?>">

        Nama: <input type="text" name="nama" value="<?= htmlspecialchars($d['nama_barang'], ENT_QUOTES, 'UTF-8') ?>"><br>
        Kategori: <input type="text" name="kategori" value="<?= htmlspecialchars($d['kategori'], ENT_QUOTES, 'UTF-8') ?>"><br>
        Stok: <input type="number" name="stok" value="<?= htmlspecialchars($d['stok'], ENT_QUOTES, 'UTF-8') ?>"><br>
        Harga: <input type="number" name="harga" value="<?= htmlspecialchars($d['harga'], ENT_QUOTES, 'UTF-8') ?>"><br>

        Gambar Lama:<br>
        <img src="<?= $d['thumbpath'] ?>" width="100"><br><br>

        Ganti Gambar (opsional):
        <input type="file" name="gambar"><br><br>

        <button>Update</button> 
</form>