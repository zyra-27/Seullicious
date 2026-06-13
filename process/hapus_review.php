<?php
session_start();
include "../config/koneksi.php";
header('Content-Type: application/json');

$type = $_POST['type'] ?? '';
$id   = intval($_POST['id'] ?? 0);

if($id <= 0){ echo json_encode(['success'=>false]); exit; }

if($type === 'menu'){
  $q = mysqli_query($koneksi, "DELETE FROM review_menu WHERE id_review=$id");
} elseif($type === 'restoran'){
  $q = mysqli_query($koneksi, "DELETE FROM review_restoran WHERE id_review=$id");
} else {
  echo json_encode(['success'=>false]); exit;
}

echo json_encode(['success'=> (bool)$q]);
?>
