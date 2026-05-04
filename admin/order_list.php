<?php
include "../config/koneksi.php";
include "sidebar.php";

$data = mysqli_query($koneksi,"SELECT * FROM orders ORDER BY id_order DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Order List</title>

<style>

body{
font-family: 'Segoe UI', sans-serif;
background:#f5f5f5;
margin:0;
}

.main{
margin-left:240px;
padding:40px;
}

.title{
font-size:34px;
font-weight:700;
margin-bottom:25px;
}

.card{
background:white;
border-radius:14px;
box-shadow:0 8px 20px rgba(0,0,0,0.06);
padding:25px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
text-align:left;
padding:16px;
font-size:14px;
color:#777;
border-bottom:1px solid #eee;
}

td{
padding:16px;
font-size:15px;
border-bottom:1px solid #f2f2f2;
vertical-align: middle;
}

tr:hover{
background:#fafafa;
}

.price{
font-weight:600;
color:#b88a44;
}

.badge{
padding:6px 12px;
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

.action-buttons{
display: flex;
gap: 6px;
flex-wrap: wrap;
align-items: center;
}

.action-buttons a{
text-decoration: none;
}

.btn-action{
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:600;
display:inline-block;
cursor:pointer;
transition: opacity 0.2s;
}

.btn-action:hover{
opacity: 0.8;
}

</style>

</head>

<body>

<div class="main">

<div class="title">
Order List
</div>

<div class="card">

<table>

<thead>
<tr>
<th>ID</th>
<th>Tanggal</th>
<th>Type</th>
<th>Meja</th>
<th>Total</th>
<th>Payment</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php while($o = mysqli_fetch_assoc($data)){ ?>

<tr>

<td>#<?php echo $o['id_order']; ?></td>

<td><?php echo $o['created_at']; ?></td>

<td><?php echo $o['order_type']; ?></td>

<td>
<?php
if($o['table_number']){
    echo "Meja " . $o['table_number'];
} else {
    echo "-";
}
?>
</td>

<td class="price">
Rp <?php echo number_format($o['total']); ?>
</td>

<td>
<?php echo $o['metode_bayar']; ?>
</td>

<td>

<?php if($o['order_status'] == "DONE"){ ?>

    <span class="badge done">✔ DONE</span>

<?php } elseif($o['order_status'] == "PROCESS"){ ?>

    <div class="action-buttons">
        <span class="badge process">⏳ PROCESS</span>
        <a href="../process/update_status.php?id=<?php echo $o['id_order']; ?>&status=DONE">
            <span class="btn-action done">✔ Selesai</span>
        </a>
    </div>

<?php } else { // NEW ?>

    <div class="action-buttons">
        <span class="badge new">NEW</span>
        <a href="../process/update_status.php?id=<?php echo $o['id_order']; ?>&status=PROCESS">
            <span class="btn-action process">⏳ Proses</span>
        </a>
        <a href="../process/update_status.php?id=<?php echo $o['id_order']; ?>&status=DONE">
            <span class="btn-action done">✔ Selesai</span>
        </a>
    </div>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>
</html>