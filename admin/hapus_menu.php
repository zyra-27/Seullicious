<?php
include "../config/koneksi.php";

if(isset($_GET['id'])){

$id = $_GET['id'];

//hapus dulu di order_items
mysqli_query($koneksi,"DELETE FROM order_items WHERE id_menu='$id'");

//baru hapus menu
$query = mysqli_query($koneksi,"DELETE FROM menu WHERE id_menu='$id'");

if($query){
echo "<script>
alert('Menu berhasil dihapus');
window.location='menu.php';
</script>";
}else{
echo "<script>
alert('Menu gagal dihapus');
window.location='menu.php';
</script>";
}

}else{
header("location:menu.php");
}
?>