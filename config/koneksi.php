<?php

$host="localhost";
$user="root";
$password="";
$db="pos_seullicious";

$koneksi=mysqli_connect($host,$user,$password,$db);

if(!$koneksi){
die("Koneksi gagal");
}

?>