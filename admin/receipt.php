<?php
include "../config/koneksi.php";

$id = $_GET['id'];

$order = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT * FROM orders WHERE id_order='$id'
"));

$items = mysqli_query($koneksi,"
SELECT oi.*,m.nama_menu
FROM order_items oi
JOIN menu m ON oi.id_menu=m.id_menu
WHERE oi.id_order='$id'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Receipt</title>

<style>

body{
background:#f4f6f9;
font-family:'Poppins', sans-serif;
margin:0;
}

/* CONTAINER */
.wrapper{
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
}

/* RECEIPT CARD */
.receipt{
width:360px;
background:white;
padding:25px;
border-radius:20px;
box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

/* HEADER */
.header{
text-align:center;
margin-bottom:15px;
}

.logo{
font-size:22px;
font-weight:bold;
color:#b79055;
}

.subtitle{
font-size:13px;
color:#888;
}

/* INFO */
.info{
font-size:13px;
line-height:1.6;
color:#555;
}

/* LINE */
.line{
border-top:1px dashed #ddd;
margin:15px 0;
}

/* ITEM */
.item{
display:flex;
justify-content:space-between;
margin-bottom:10px;
font-size:14px;
}

.item-name{
font-weight:500;
}

.item-qty{
font-size:12px;
color:#888;
}

/* TOTAL */
.total{
display:flex;
justify-content:space-between;
font-size:18px;
font-weight:bold;
margin-top:10px;
}

/* FOOTER */
.footer{
text-align:center;
margin-top:15px;
font-size:13px;
color:#777;
}

/* BUTTON */
.btn{
margin-top:10px;
width:100%;
padding:12px;
border:none;
border-radius:10px;
cursor:pointer;
font-size:14px;
text-align:center;
display:block;
text-decoration:none;
box-sizing:border-box;
}

.print{
background:black;
color:white;
}

.back{
background:#b79055;
color:white;
}

/* ===== REVIEW BUTTON ===== */
.review{
background: linear-gradient(135deg, #f9e4b7, #b79055);
color: white;
font-weight: 600;
letter-spacing: 0.5px;
border: none;
position: relative;
overflow: hidden;
transition: opacity 0.2s;
}

.review:hover{
opacity: 0.9;
}

.review-divider{
text-align:center;
font-size:12px;
color:#aaa;
margin-top:14px;
margin-bottom:2px;
letter-spacing:0.5px;
}
/* ========================= */

/* PRINT MODE */
@media print{
.btn{
display:none;
}
.review-divider{
display:none;
}
body{
background:white;
}
.receipt{
box-shadow:none;
border-radius:0;
}
}

</style>
</head>

<body>

<div class="wrapper">

<div class="receipt">

<!-- HEADER -->
<div class="header">
<div class="logo">SEOULLICIOUS</div>
<div class="subtitle">Cafe & Korean Food</div>
</div>

<!-- INFO -->
<div class="info">
Order ID : <b>#<?= $id ?></b><br>
Tanggal : <?= $order['tanggal'] ?><br>
Type : <?= $order['order_type'] ?><br>

<?php if($order['meja']){ ?>
Meja : <?= $order['meja'] ?><br>
<?php } ?>

Payment : <?= $order['metode_bayar'] ?>
</div>

<div class="line"></div>

<!-- ITEMS -->
<?php while($i=mysqli_fetch_assoc($items)){ ?>

<div class="item">
<div>
<div class="item-name"><?= $i['nama_menu'] ?></div>
<div class="item-qty">x<?= $i['qty'] ?></div>
</div>

<div>
Rp <?= number_format($i['subtotal']) ?>
</div>
</div>

<?php } ?>

<div class="line"></div>

<!-- TOTAL -->
<div class="total">
<div>Total</div>
<div>Rp <?= number_format($order['total']) ?></div>
</div>

<div class="line"></div>

<!-- FOOTER -->
<div class="footer">
Thank You ❤️ <br>
Enjoy Your Meal 🍜
</div>

<!-- BUTTON -->
<button class="btn print" onclick="window.print()">🖨️ Print Receipt</button>


<a href="history.php">
<button class="btn back">⬅ Back to History</button>
</a>

</div>

</div>

</body>
</html>