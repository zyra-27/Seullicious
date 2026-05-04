<?php
include "../config/koneksi.php";
include "../admin/sidebar.php"; // pakai sidebar terpisah

$search = $_GET['search'] ?? "";
$kategori = $_GET['kategori'] ?? "all";

$sql = "SELECT * FROM menu WHERE nama_menu LIKE '%$search%'";

if($kategori != "all"){
    $sql .= " AND kategori='$kategori'";
}

$menu = mysqli_query($koneksi,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Menu Management</title>

<style>

body{
margin:0;
font-family:'Segoe UI',sans-serif;
background:#f6f6f6;
}

/* MAIN */
.main{
margin-left:240px; /* kasih space buat sidebar */
padding:40px 60px;
}

.title{
font-size:30px;
font-weight:700;
margin-bottom:25px;
}

/* TOP BAR */
.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.search-area{
display:flex;
gap:10px;
}

.search-area input{
padding:10px;
border-radius:8px;
border:1px solid #ddd;
width:230px;
}

.search-area select{
padding:10px;
border-radius:8px;
border:1px solid #ddd;
}

.search-area button{
padding:10px 18px;
border:none;
background:#b88a44;
color:white;
border-radius:8px;
cursor:pointer;
}

.btn-add{
background:#b88a44;
padding:12px 20px;
border-radius:10px;
color:white;
text-decoration:none;
font-weight:600;
}

/* GRID */
.menu-grid{
display:grid;
grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
gap:30px;
}

/* CARD */
.card{
background:white;
border-radius:16px;
padding:25px;
text-align:center;
box-shadow:0 8px 18px rgba(0,0,0,0.08);
transition:0.3s;
}

.card:hover{
transform:translateY(-5px);
}

/* IMAGE */
.card img{
width:110px;
height:110px;
object-fit:cover;
margin-bottom:12px;
}

/* TEXT */
.card h3{
margin:8px 0;
}

.price{
color:#888;
margin-bottom:10px;
}

/* BADGE */
.badge{
padding:5px 12px;
border-radius:20px;
font-size:12px;
color:white;
display:inline-block;
margin-bottom:12px;
}

.food{background:#4CAF50;}
.drink{background:#2196F3;}
.snack{background:#FF9800;}

/* BUTTON */
.btn{
padding:7px 14px;
border-radius:8px;
font-size:13px;
text-decoration:none;
margin:4px;
display:inline-block;
}

.edit{
background:#3f8efc;
color:white;
}

.delete{
background:#e53935;
color:white;
}

</style>
</head>

<body>

<div class="main">

<div class="title">Menu Management</div>

<div class="top-bar">

<form method="GET" class="search-area">

<input type="text" name="search" placeholder="Search menu..."
value="<?php echo $search ?>">

<select name="kategori">
<option value="all">All</option>
<option value="food">Food</option>
<option value="drink">Drink</option>
<option value="snack">Snack</option>
</select>

<button>Filter</button>

</form>

<a class="btn-add" href="tambah_menu.php">
+ Tambah Menu
</a>

</div>

<div class="menu-grid">

<?php while($m=mysqli_fetch_assoc($menu)){ ?>

<div class="card">

<?php
$gambar = "../assets/".$m['gambar'];

if($m['gambar'] != "" && file_exists($gambar)){
    echo "<img src='../assets/".$m['gambar']."'>";
}else{
    echo "<img src='../assets/food.jpg'>";
}
?>

<h3><?php echo $m['nama_menu']; ?></h3>

<div class="price">
Rp <?php echo number_format($m['harga']); ?>
</div>

<span class="badge <?php echo $m['kategori']; ?>">
<?php echo ucfirst($m['kategori']); ?>
</span>

<br>

<a class="btn edit"
href="edit_menu.php?id=<?php echo $m['id_menu']; ?>">
Edit
</a>

<a class="btn delete"
onclick="return confirm('Hapus menu ini?')"
href="hapus_menu.php?id=<?php echo $m['id_menu']; ?>">
Delete
</a>

</div>

<?php } ?>

</div>

</div>

</body>
</html>