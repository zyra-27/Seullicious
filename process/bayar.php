<?php
session_start();
include "../config/koneksi.php";

/* VALIDASI CART */
if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    header("Location: ../user/cart.php");
    exit;
}

$cart = $_SESSION['cart'];

/* AMBIL DATA DARI FORM */
$order_type  = $_POST['order_type']     ?? 'dinein';
$meja        = $_POST['table_number']   ?? NULL;
$metode      = $_POST['payment_method'] ?? 'Cash';

/* Konversi string kosong jadi NULL */
$jam_mulai   = (!empty($_POST['jam_mulai']))   ? $_POST['jam_mulai']   : NULL;
$jam_selesai = (!empty($_POST['jam_selesai'])) ? $_POST['jam_selesai'] : NULL;
$meja        = (!empty($meja)) ? $meja : NULL;

/* AMBIL ID USER DARI SESSION — bukan username */
$id_user = $_SESSION['id_user'] 
        ?? $_SESSION['user']['id'] 
        ?? $_SESSION['user']['id_user'] 
        ?? NULL;

/* HITUNG TOTAL (sudah include tax 10%) */
$subtotal_semua = 0;
foreach($cart as $item){
    $subtotal_semua += $item['harga'] * $item['qty'];
}
$tax   = round($subtotal_semua * 0.1);
$total = $subtotal_semua + $tax;

/* START TRANSACTION */
mysqli_begin_transaction($koneksi);

try{

    /* INSERT ke tabel orders — pakai id_user sesuai kolom di DB */
    $stmt = mysqli_prepare($koneksi,"
        INSERT INTO orders 
        (id_user, total, order_type, meja, metode_bayar, jam_mulai, jam_selesai, order_status, tanggal)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'DONE', NOW())
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "iisssss",   // i untuk id_user (integer), i untuk total, s sisanya string
        $id_user,
        $total,
        $order_type,
        $meja,
        $metode,
        $jam_mulai,
        $jam_selesai
    );

    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("Gagal insert order: " . mysqli_stmt_error($stmt));
    }

    $order_id = mysqli_insert_id($koneksi);

    /* INSERT setiap item ke order_items */
    $stmt_item = mysqli_prepare($koneksi,"
        INSERT INTO order_items 
        (id_order, id_menu, qty, subtotal)
        VALUES (?, ?, ?, ?)
    ");

    foreach($cart as $item){
        $sub = $item['harga'] * $item['qty'];

        mysqli_stmt_bind_param(
            $stmt_item,
            "iiii",
            $order_id,
            $item['id_menu'],
            $item['qty'],
            $sub
        );

        if(!mysqli_stmt_execute($stmt_item)){
            throw new Exception("Gagal insert item: " . mysqli_stmt_error($stmt_item));
        }
    }

    /* Semua berhasil → commit */
    mysqli_commit($koneksi);

    /* Hapus cart dari session */
    unset($_SESSION['cart']);

    /* Redirect ke receipt */
    header("Location: ../user/receipt.php?id=" . $order_id);
    exit;

} catch(Exception $e){

    mysqli_rollback($koneksi);

    echo "
    <style>
      body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;background:#1a0f07;}
      .err{background:#fff;border-radius:16px;padding:32px 40px;text-align:center;max-width:400px;}
      .err h2{color:#c0392b;margin-bottom:8px;}
      .err p{color:#555;font-size:14px;margin-bottom:20px;}
      .err a{background:#c0392b;color:white;padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:600;}
    </style>
    <div class='err'>
      <h2>⚠️ Gagal Menyimpan Order</h2>
      <p>" . htmlspecialchars($e->getMessage()) . "</p>
      <a href='javascript:history.back()'>← Kembali</a>
    </div>";
}
?>