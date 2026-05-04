<?php

include "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query($koneksi,"
UPDATE orders
SET order_status='DONE'
WHERE id_order='$id'
");

header("Location: ../admin/order_list.php");

?>