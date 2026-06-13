<?php
include "../config/koneksi.php";

$id=$_GET['id'];

$data=mysqli_query($koneksi,"
SELECT order_items.*,produk.nama_menu 
FROM order_items 
JOIN produk ON produk.id_menu=order_items.id_menu
WHERE id_order='$id'
");

?>

<h2>Detail Order #<?php echo $id ?></h2>

<table border="1" cellpadding="10">

<tr>

<th>Menu</th>
<th>Qty</th>
<th>Price</th>
<th>Subtotal</th>

</tr>

<?php while($d=mysqli_fetch_array($data)){ ?>

<tr>

<td><?php echo $d['nama_menu'] ?></td>

<td><?php echo $d['qty'] ?></td>

<td><?php echo $d['price'] ?></td>

<td><?php echo $d['subtotal'] ?></td>

</tr>

<?php } ?>

</table>