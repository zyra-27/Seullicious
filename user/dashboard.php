<?php
include "../config/koneksi.php";

/* TOTAL INCOME */
$income = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT SUM(total) as total FROM orders
"))['total'];

/* TOTAL ORDER */
$order = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT COUNT(*) as total FROM orders
"))['total'];

/* BEST SELLER */
$best = mysqli_query($koneksi,"
SELECT m.nama_menu, SUM(oi.qty) as total
FROM order_items oi
JOIN menu m ON oi.id_menu=m.id_menu
GROUP BY oi.id_menu
ORDER BY total DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<style>
body{
font-family:Poppins;
background:#f4f6f9;
display:flex;
margin:0;
}

.sidebar{
width:240px;
background:#b79055;
color:white;
padding:25px;
height:100vh;
}

.sidebar a{
display:block;
padding:12px;
color:white;
text-decoration:none;
border-radius:10px;
}

.content{
flex:1;
padding:30px;
}

.card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:20px;
}

.big{
font-size:24px;
font-weight:bold;
}
</style>
</head>

<body>

<div class="sidebar">
<h2>Seoullicious</h2>
<a href="dashboard.php">Dashboard</a>
<a href="pos.php">Menu</a>
<a href="history.php">History</a>
</div>

<div class="content">

<h1>📊 Dashboard</h1>

<div class="grid">

<div class="card">
Total Income
<div class="big">Rp <?= number_format($income) ?></div>
</div>

<div class="card">
Total Orders
<div class="big"><?= $order ?></div>
</div>

</div>

<div class="card">
<h3>🔥 Best Seller</h3>

<?php while($b=mysqli_fetch_assoc($best)){ ?>
<p><?= $b['nama_menu'] ?> (<?= $b['total'] ?>x)</p>
<?php } ?>

</div>

</div>

</body>
</html>