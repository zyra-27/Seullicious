<?php
session_start();
include "../config/koneksi.php";
header('Content-Type: application/json');

$id_user = $_SESSION['id_user'] ?? null;
if(!$id_user){ echo json_encode(['success'=>false,'message'=>'Login dulu!']); exit; }

$id_order = intval($_POST['id_order'] ?? 0);
$bintang  = intval($_POST['bintang'] ?? 0);
$komentar = mysqli_real_escape_string($koneksi, trim($_POST['komentar'] ?? ''));

if($id_order <= 0 || $bintang < 1 || $bintang > 5){
  echo json_encode(['success'=>false,'message'=>'Data tidak valid']); exit;
}

// Cek order milik user ini dan sudah DONE
$cek_order = mysqli_query($koneksi,"
  SELECT id_order FROM orders
  WHERE id_order=$id_order AND id_user=$id_user AND order_status='DONE'
  LIMIT 1
");
if(mysqli_num_rows($cek_order) === 0){
  echo json_encode(['success'=>false,'message'=>'Order tidak valid']); exit;
}

// Cek sudah pernah review order ini
$cek = mysqli_query($koneksi,"SELECT id_review FROM review_restoran WHERE id_user=$id_user AND id_order=$id_order LIMIT 1");
if(mysqli_num_rows($cek) > 0){
  echo json_encode(['success'=>false,'message'=>'Kamu sudah mengulas order ini']); exit;
}

$insert = mysqli_query($koneksi,"
  INSERT INTO review_restoran (id_user, id_order, bintang, komentar)
  VALUES ($id_user, $id_order, $bintang, '$komentar')
");

if($insert){
  echo json_encode(['success'=>true,'message'=>'Ulasan restoran berhasil disimpan']);
} else {
  echo json_encode(['success'=>false,'message'=>'Gagal menyimpan ulasan']);
}
?>
