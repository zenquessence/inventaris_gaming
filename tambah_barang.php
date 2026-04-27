<form action="proses_upload.php" method="POST" enctype="multipart/form-data">
Nama: <input type="text" name="nama"><br>
Kategori: <input type="text" name="kategori"><br>
Stok: <input type="number" name="stok"><br>
Harga: <input type="number" name="harga"><br>

Gambar: 
<input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewGambar(event)">
<br><br>

<!-- PREVIEW -->
<img id="preview" src="#" width="150" style="display:none; border:1px solid #ccc;">

<br><br>
<button>Simpan</button>
</form>

<script>
function previewGambar(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}
</script>