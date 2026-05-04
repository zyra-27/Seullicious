<?php
include "../config/koneksi.php";

$id     = $_GET['id']     ?? '';
$status = $_GET['status'] ?? '';

// Validasi status yang diperbolehkan
if(!in_array($status, ['PROCESS', 'DONE'])){
    header("Location: ../admin/order_list.php");
    exit;
}

// Validasi id harus angka
if(!is_numeric($id)){
    header("Location: ../admin/order_list.php");
    exit;
}

$id     = (int) $id;
$status = mysqli_real_escape_string($koneksi, $status);

mysqli_query($koneksi, "UPDATE orders SET order_status='$status' WHERE id_order=$id");

header("Location: ../admin/order_list.php");
exit;
?>