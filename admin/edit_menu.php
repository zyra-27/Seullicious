<?php
session_start();
include "../config/koneksi.php";

$id = $_GET['id'];

$query = mysqli_query($koneksi,"SELECT * FROM menu WHERE id_menu='$id'");
$data = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){

$nama_menu = $_POST['nama_menu'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];

$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];

if($gambar != ""){

move_uploaded_file($tmp,"../upload/".$gambar);

mysqli_query($koneksi,"UPDATE menu SET
nama_menu='$nama_menu',
kategori='$kategori',
harga='$harga',
gambar='$gambar'
WHERE id_menu='$id'");

}else{

mysqli_query($koneksi,"UPDATE menu SET
nama_menu='$nama_menu',
kategori='$kategori',
harga='$harga'
WHERE id_menu='$id'");

}

echo "<script>
alert('Menu berhasil diupdate');
window.location='menu.php';
</script>";

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Menu</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>

body{
background:#f5f5f5;
font-family:INTER;
}

.main{
margin-left:250px;
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

<h2>Edit Menu</h2>

<div class="card">

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Nama Menu</label>
<input type="text" name="nama_menu"
value="<?php echo $data['nama_menu']; ?>"
class="form-control">
</div>

<div class="form-group">
<label>Kategori</label>

<select name="kategori" class="form-control">

<option value="Makanan"
<?php if($data['kategori']=="Makanan") echo "selected"; ?>>
Makanan
</option>

<option value="Minuman"
<?php if($data['kategori']=="Minuman") echo "selected"; ?>>
Minuman
</option>

<option value="Snack"
<?php if($data['kategori']=="Snack") echo "selected"; ?>>
Snack
</option>

</select>

</div>

<div class="form-group">
<label>Harga</label>
<input type="number" name="harga"
value="<?php echo $data['harga']; ?>"
class="form-control">
</div>

<div class="form-group">
<label>Gambar</label>

<br>

<img src="../upload/<?php echo $data['gambar']; ?>"
width="120" class="mb-2">

<input type="file" name="gambar" class="form-control">

</div>

<button type="submit" name="update"
class="btn btn-warning">
Update Menu
</button>

<a href="menu.php" class="btn btn-secondary">
Kembali
</a>

</form>

</div>

</div>

</body>
</html>