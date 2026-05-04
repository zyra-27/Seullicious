<?php
include "../config/koneksi.php";
include "sidebar.php";

$nama   = $_POST['nama_toko'];
$alamat = $_POST['alamat'];
$hp     = $_POST['no_hp'];
$metode = $_POST['metode'];
$pajak  = $_POST['pajak'];

$cek = mysqli_query($koneksi,"SELECT * FROM setting LIMIT 1");

if(mysqli_num_rows($cek) > 0){
    mysqli_query($koneksi,"UPDATE setting SET
        nama_toko='$nama',
        alamat='$alamat',
        no_hp='$hp',
        metode='$metode',
        pajak='$pajak'
    ");
} else {
    mysqli_query($koneksi,"INSERT INTO setting
    (nama_toko,alamat,no_hp,metode,pajak)
    VALUES
    ('$nama','$alamat','$hp','$metode','$pajak')");
}

echo "success";