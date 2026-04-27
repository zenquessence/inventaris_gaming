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

$no = 1; // nomor urut

while($d = $q->fetch(PDO::FETCH_ASSOC)){
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($d['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
    <td><?= htmlspecialchars($d['kategori'], ENT_QUOTES, 'UTF-8') ?></td>
    <td><?= htmlspecialchars($d['stok'], ENT_QUOTES, 'UTF-8') ?></td>
    <td><?= htmlspecialchars($d['harga'], ENT_QUOTES, 'UTF-8') ?></td>

    <!-- gambar pindah ke sini -->
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
            <button style="background:red;color:white"
                onclick="return confirm('Yakin hapus?')">
                DELETE
            </button>
        </form>
    </td>
</tr>
<?php } ?>

</table>