<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['status'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/koneksi.php";
include "sidebar.php";

/* TOTAL ORDERS */
$order = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders");
$total_orders = mysqli_fetch_assoc($order)['total'];

/* TOTAL REVENUE */
$revenue = mysqli_query($koneksi,"SELECT SUM(total) as total FROM orders WHERE order_status='DONE'");
$total_revenue = mysqli_fetch_assoc($revenue)['total'];

/* TOTAL MENU */
$menu = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM menu");
$total_menu = mysqli_fetch_assoc($menu)['total'];

/* ACTIVE ORDERS (NEW + PROCESS) */
$active = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders WHERE order_status IN ('NEW','PROCESS')");
$active_orders = mysqli_fetch_assoc($active)['total'];

/* IN PROCESS ORDERS */
$proc = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders WHERE order_status='PROCESS'");
$process_orders = mysqli_fetch_assoc($proc)['total'];

/* RECENT ORDERS */
$recent = mysqli_query($koneksi,"SELECT * FROM orders ORDER BY id_order DESC LIMIT 5");

?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard</title>

<style>

body{
font-family:'Segoe UI',sans-serif;
background:#f4f4f4;
margin:0;
}

.main{
margin-left:240px;
padding:40px;
}

.title{
font-size:34px;
font-weight:700;
margin-bottom:30px;
}

.cards{
display:grid;
grid-template-columns:repeat(5,1fr);
gap:20px;
margin-bottom:30px;
}

.card{
background:white;
padding:25px;
border-radius:12px;
box-shadow:0 6px 16px rgba(0,0,0,0.06);
}

.card-title{
font-size:14px;
color:#777;
}

.card-value{
font-size:28px;
font-weight:700;
margin-top:8px;
color:#b88a44;
}

.card-value.process-val{
color:#1565c0;
}

.table-card{
background:white;
border-radius:12px;
padding:25px;
box-shadow:0 6px 16px rgba(0,0,0,0.06);
}

table{
width:100%;
border-collapse:collapse;
}

th{
text-align:left;
padding:15px;
font-size:14px;
color:#777;
border-bottom:1px solid #eee;
}

td{
padding:15px;
border-bottom:1px solid #f2f2f2;
vertical-align: middle;
}

tr:hover{
background:#fafafa;
}

.badge{
padding:5px 12px;
border-radius:20px;
font-size:12px;
font-weight:600;
display:inline-block;
}

.done{
background:#e6f6ec;
color:#2e7d32;
}

.new{
background:#ffe8a3;
color:#8a6500;
}

.process{
background:#e3f0ff;
color:#1565c0;
}

</style>

</head>

<body>

<div class="main">

<div class="title">Dashboard</div>

<div class="cards">

    <div class="card">
        <div class="card-title">Total Orders</div>
        <div class="card-value"><?php echo $total_orders ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Revenue</div>
        <div class="card-value">Rp <?php echo number_format($total_revenue) ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Menu</div>
        <div class="card-value"><?php echo $total_menu ?></div>
    </div>

    <div class="card">
        <div class="card-title">Active Orders</div>
        <div class="card-value"><?php echo $active_orders ?></div>
    </div>

    <div class="card">
        <div class="card-title">In Process</div>
        <div class="card-value process-val"><?php echo $process_orders ?></div>
    </div>

</div>

<div class="table-card">

    <h3 style="margin-bottom:20px">Recent Orders</h3>

    <table>

        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Type</th>
            <th>Total</th>
            <th>Status</th>
        </tr>

        <?php while($r = mysqli_fetch_assoc($recent)){ ?>

        <tr>

            <td>#<?php echo $r['id_order'] ?></td>

            <td><?php echo $r['created_at'] ?></td>

            <td><?php echo $r['order_type'] ?></td>

            <td>Rp <?php echo number_format($r['total']) ?></td>

            <td>
                <?php if($r['order_status'] == "DONE"){ ?>
                    <span class="badge done">✔ DONE</span>
                <?php } elseif($r['order_status'] == "PROCESS"){ ?>
                    <span class="badge process">⏳ PROCESS</span>
                <?php } else { ?>
                    <span class="badge new">NEW</span>
                <?php } ?>
            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</div>

</body>
</html>