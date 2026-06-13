<?php
session_start();
include "../config/koneksi.php";
include "../config/xendit.php";

/* VALIDASI CART */
if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    header("Location: ../user/cart.php"); exit;
}

$cart        = $_SESSION['cart'];
$order_type  = $_POST['order_type']    ?? 'dinein';
$meja        = !empty($_POST['table_number'])  ? $_POST['table_number']  : NULL;
$metode      = $_POST['payment_method'] ?? 'Cash';
$jam_mulai   = !empty($_POST['jam_mulai'])   ? $_POST['jam_mulai']   : NULL;
$jam_selesai = !empty($_POST['jam_selesai']) ? $_POST['jam_selesai'] : NULL;
$catatan     = $_POST['catatan'] ?? '';
$id_user     = $_SESSION['id_user'] ?? NULL;
$username    = $_SESSION['username'] ?? 'Customer';

/* HITUNG TOTAL */
$subtotal = 0;
foreach($cart as $item) $subtotal += $item['harga'] * $item['qty'];
$tax   = round($subtotal * 0.1);
$total = $subtotal + $tax;

/* ── QR / E-Wallet → Xendit Invoice ── */
if($metode === 'QR' || $metode === 'Card'){

    mysqli_begin_transaction($koneksi);
    try {
        // Pastikan kolom ada
        @mysqli_query($koneksi,"ALTER TABLE orders ADD COLUMN IF NOT EXISTS catatan TEXT DEFAULT NULL");
        @mysqli_query($koneksi,"ALTER TABLE orders ADD COLUMN IF NOT EXISTS xendit_invoice_id VARCHAR(100) DEFAULT NULL");
        @mysqli_query($koneksi,"ALTER TABLE orders ADD COLUMN IF NOT EXISTS xendit_external_id VARCHAR(100) DEFAULT NULL");

        // Simpan order dengan status PENDING
        $stmt = mysqli_prepare($koneksi,"
            INSERT INTO orders
            (id_user, total, order_type, meja, metode_bayar, jam_mulai, jam_selesai, order_status, catatan, tanggal)
            VALUES (?, ?, ?, ?, 'QRIS', ?, ?, 'PENDING', ?, NOW())
        ");
        mysqli_stmt_bind_param($stmt,'iisssss',
            $id_user, $total, $order_type, $meja, $jam_mulai, $jam_selesai, $catatan
        );
        if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_stmt_error($stmt));
        $order_id = mysqli_insert_id($koneksi);

        // Simpan items
        $stmt_item = mysqli_prepare($koneksi,"
            INSERT INTO order_items (id_order, id_menu, qty, subtotal) VALUES (?,?,?,?)
        ");
        foreach($cart as $item){
            $sub = $item['harga'] * $item['qty'];
            mysqli_stmt_bind_param($stmt_item,'iiii',$order_id,$item['id_menu'],$item['qty'],$sub);
            if(!mysqli_stmt_execute($stmt_item)) throw new Exception(mysqli_stmt_error($stmt_item));
        }

        mysqli_commit($koneksi);

        // Buat Xendit Invoice
        $external_id = 'SEOUL-' . $order_id . '-' . time();
        $result = xendit_create_invoice(
            $external_id,
            $total,
            $username,
            'Seoullicious Order #' . $order_id
        );

        if(!$result['ok']){
            throw new Exception('Gagal buat payment: ' . $result['error']);
        }

        $invoice_id  = $result['data']['id'];
        $invoice_url = $result['data']['invoice_url'];

        // Simpan invoice ID ke order
        $upd = mysqli_prepare($koneksi,
            "UPDATE orders SET xendit_invoice_id=?, xendit_external_id=? WHERE id_order=?"
        );
        mysqli_stmt_bind_param($upd,'ssi',$invoice_id,$external_id,$order_id);
        mysqli_stmt_execute($upd);

        // Simpan order_id di session untuk cek setelah balik dari Xendit
        $_SESSION['pending_order_id'] = $order_id;
        unset($_SESSION['cart']);

        // Redirect ke halaman pembayaran Xendit
        header("Location: " . $invoice_url);
        exit;

    } catch(Exception $e){
        mysqli_rollback($koneksi);
        show_error($e->getMessage());
    }

} else {
    /* ── Cash / Card → langsung simpan ── */
    mysqli_begin_transaction($koneksi);
    try {
        @mysqli_query($koneksi,"ALTER TABLE orders ADD COLUMN IF NOT EXISTS catatan TEXT DEFAULT NULL");

        $stmt = mysqli_prepare($koneksi,"
            INSERT INTO orders
            (id_user, total, order_type, meja, metode_bayar, jam_mulai, jam_selesai, order_status, catatan, tanggal)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'NEW', ?, NOW())
        ");
        mysqli_stmt_bind_param($stmt,'iissssss',
            $id_user, $total, $order_type, $meja, $metode, $jam_mulai, $jam_selesai, $catatan
        );
        if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_stmt_error($stmt));
        $order_id = mysqli_insert_id($koneksi);

        $stmt_item = mysqli_prepare($koneksi,"
            INSERT INTO order_items (id_order, id_menu, qty, subtotal) VALUES (?,?,?,?)
        ");
        foreach($cart as $item){
            $sub = $item['harga'] * $item['qty'];
            mysqli_stmt_bind_param($stmt_item,'iiii',$order_id,$item['id_menu'],$item['qty'],$sub);
            if(!mysqli_stmt_execute($stmt_item)) throw new Exception(mysqli_stmt_item_error($stmt_item));
        }

        mysqli_commit($koneksi);
        unset($_SESSION['cart']);
        header("Location: ../user/receipt.php?id={$order_id}");
        exit;

    } catch(Exception $e){
        mysqli_rollback($koneksi);
        show_error($e->getMessage());
    }
}

function show_error($msg){
    echo "<!DOCTYPE html><html><head>
    <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;
    height:100vh;background:#1a0f07;}
    .err{background:#fff;border-radius:16px;padding:32px 40px;text-align:center;max-width:400px;}
    .err h2{color:#c0392b;margin-bottom:8px;} .err p{color:#555;font-size:14px;margin-bottom:20px;}
    .err a{background:#c0392b;color:white;padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:600;}
    </style></head><body><div class='err'>
    <h2>Gagal Menyimpan Order</h2>
    <p>".htmlspecialchars($msg)."</p>
    <a href='javascript:history.back()'>Kembali</a>
    </div></body></html>";
    exit;
}