<?php
header('Content-Type: application/json');
include "../config/koneksi.php";
include "../config/xendit.php";

$order_id = (int)($_GET['id'] ?? 0);
if(!$order_id){ echo json_encode(['status'=>'ERROR']); exit; }

$stmt = mysqli_prepare($koneksi,
    "SELECT order_status, xendit_invoice_id FROM orders WHERE id_order=? LIMIT 1"
);
mysqli_stmt_bind_param($stmt,'i',$order_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$order){ echo json_encode(['status'=>'NOT_FOUND']); exit; }

// Sudah paid di DB
if(in_array($order['order_status'], ['NEW','PAID','DONE'])){
    echo json_encode(['status' => $order['order_status']]); exit;
}

// Cek ke Xendit
$inv_id = $order['xendit_invoice_id'];
if(!$inv_id){ echo json_encode(['status'=>'PENDING']); exit; }

$inv = xendit_check_invoice($inv_id);
$inv_status = $inv['status'] ?? 'PENDING';

if($inv_status === 'PAID' || $inv_status === 'SETTLED'){
    $upd = mysqli_prepare($koneksi,
        "UPDATE orders SET order_status='NEW' WHERE id_order=?"
    );
    mysqli_stmt_bind_param($upd,'i',$order_id);
    mysqli_stmt_execute($upd);
    echo json_encode(['status' => 'NEW']); exit;
}

if($inv_status === 'EXPIRED'){
    echo json_encode(['status' => 'EXPIRED']); exit;
}

echo json_encode(['status' => 'PENDING', 'xendit_status' => $inv_status]);