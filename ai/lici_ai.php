<?php
session_start();
include "../config/koneksi.php";

$msg = strtolower(trim($_POST['message']));

/* SIMPAN HISTORY */
$_SESSION['lici_history'][] = $msg;

/* ========================= */
/* RESPONSE HUMAN STYLE */
/* ========================= */

function randomText($arr){
    return $arr[array_rand($arr)];
}

/* GREETING */
if(preg_match("/halo|hai|hi/",$msg)){
    echo "Halo juga 😄 kamu lagi pengen makan apa hari ini?";
    exit;
}

/* DETECT INTENT */
$intent = [
    "murah" => preg_match("/murah|hemat/",$msg),
    "mahal" => preg_match("/mahal|premium/",$msg),
    "minuman" => preg_match("/minum|drink|milk|coffee|tea/",$msg),
    "pedas" => preg_match("/pedas|spicy/",$msg),
    "manis" => preg_match("/manis|sweet/",$msg),
    "lapar" => preg_match("/lapar|makan|rekom/",$msg),
];

/* ========================= */
/* RESPON NATURAL */
/* ========================= */

$intro = randomText([
    "Oke aku ngerti 😄",
    "Siap, aku bantu ya 👌",
    "Hmm menarik 🤔",
    "Wah ini cocok sih 🔥"
]);

/* ========================= */
/* QUERY */
/* ========================= */

$where=[];

if($intent["minuman"]){
    $where[]="nama_menu LIKE '%milk%' OR nama_menu LIKE '%tea%' OR nama_menu LIKE '%coffee%'";
}

if($intent["pedas"]){
    $where[]="nama_menu LIKE '%ramen%' OR nama_menu LIKE '%tteok%' OR nama_menu LIKE '%nakji%'";
}

if($intent["manis"]){
    $where[]="nama_menu LIKE '%milk%' OR nama_menu LIKE '%yakult%' OR nama_menu LIKE '%coffee%'";
}

$query="SELECT * FROM menu";

if(count($where)){
    $query.=" WHERE ".implode(" AND ",$where);
}

/* SORT */
if($intent["murah"]){
    $query.=" ORDER BY harga ASC";
}else{
    $query.=" ORDER BY RAND()";
}

$query.=" LIMIT 3";

$res=mysqli_query($koneksi,$query);

/* ========================= */
/* RESPONSE */
/* ========================= */

if(mysqli_num_rows($res)>0){

echo $intro." aku rekomendasiin ini ya 👇||";

while($d=mysqli_fetch_assoc($res)){
echo $d['id_menu']."|".$d['nama_menu']."|".number_format($d['harga'])."|".$d['gambar']."||";
}

/* FOLLOW UP */
echo "Kamu mau aku tambahin ke cart langsung atau mau lihat yang lain? 😄";

}else{
echo "Hmm aku belum nemu 😅 coba kata lain ya";
}