<?php
session_start();
include "../config/koneksi.php";

$id = $_POST['id_menu'];

$query = mysqli_query($koneksi,"SELECT * FROM menu WHERE id_menu='$id'");
$data = mysqli_fetch_assoc($query);

// kalau cart belum ada
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$found = false;

// cek kalau item sudah ada → tambah qty
foreach($_SESSION['cart'] as &$item){
    if($item['id_menu'] == $id){
        $item['qty'] += 1;
        $found = true;
        break;
    }
}

// kalau belum ada → tambah baru
if(!$found){
    $_SESSION['cart'][] = [
        "id_menu"=>$data['id_menu'],
        "nama"=>$data['nama_menu'],
        "harga"=>$data['harga'],
        "qty"=>1
    ];
}

header("Location: ../user/cart.php");