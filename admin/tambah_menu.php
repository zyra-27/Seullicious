<?php
session_start();
include "../config/koneksi.php";

if(isset($_POST['simpan'])){

$nama_menu = $_POST['nama_menu'];
$harga = $_POST['harga'];
$kategori = $_POST['kategori'];

/* UPLOAD GAMBAR */

$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];

move_uploaded_file($tmp,"../upload/".$gambar);

/* INSERT DATABASE */

mysqli_query($koneksi,"INSERT INTO menu (nama_menu,kategori,harga,gambar)
VALUES ('$nama_menu','$kategori','$harga','$gambar')");

echo "<script>
alert('Menu berhasil ditambahkan');
window.location='menu.php';
</script>";

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Tambah Menu</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>

body{
background:#f5f5f5;
font-family:Segoe UI;
}

.main{
margin-left:240px;
padding:40px;
}

.card{
padding:30px;
border-radius:10px;
}

</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">

<h2>Tambah Menu</h2>

<div class="card">

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Nama Menu</label>
<input type="text" name="nama_menu" class="form-control" required>
</div>

<div class="form-group">
<label>Kategori</label>

<select name="kategori" required>

<option value="">-- Pilih Kategori --</option>

<option value="food">Food</option>

<option value="drink">Drink</option>

<option value="snack">Snack</option>

</select>

</div>

<div class="form-group">
<label>Harga</label>
<input type="number" name="harga" class="form-control" required>
</div>

<div class="form-group">
<label>Gambar Menu</label>
<input type="file" name="gambar" class="form-control" required>
</div>

<button type="submit" name="simpan" class="btn btn-warning">
Simpan Menu
</button>

<a href="menu.php" class="btn btn-secondary">
Kembali
</a>

</form>

</div>

</div>

</body>
</html>