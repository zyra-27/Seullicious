<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* RESET BIAR AMAN */
body{
  margin:0;
}

/* SIDEBAR */
.sidebar{
  width:240px;
  height:100vh;
  background:linear-gradient(180deg,#2d1a08,#b79055);
  color:white;
  position:fixed;
  left:0;
  top:0;
  display:flex;
  flex-direction:column;
  padding:20px 15px;
  z-index:9999; /* 🔥 penting biar nggak ketutup */
}

/* LOGO */
.logo{
  font-size:20px;
  font-weight:800;
  color:#f5d080;
  margin-bottom:30px;
}

/* MENU */
.menu{
  display:flex;
  flex-direction:column;
  gap:8px;
}

.menu a{
  display:flex;
  align-items:center;
  gap:12px;
  padding:12px 14px;
  border-radius:12px;
  text-decoration:none;
  color:rgba(255,255,255,0.8);
  transition:0.2s;
  font-size:14px;
}

/* HOVER */
.menu a:hover{
  background:rgba(255,255,255,0.15);
  color:white;
}

/* ACTIVE */
.menu a.active{
  background:#f5d080;
  color:#2d1a08;
  font-weight:600;
}

/* ICON */
.menu i{
  width:18px;
  text-align:center;
}

/* LOGOUT */
.bottom{
  margin-top:auto;
}

.bottom a{
  display:flex;
  align-items:center;
  gap:10px;
  padding:12px;
  border-radius:10px;
  color:white;
  text-decoration:none;
}

.bottom a:hover{
  background:rgba(255,0,0,0.2);
}

</style>

<!-- SIDEBAR HTML -->
<div class="sidebar">

  <div class="logo">
    🍜 Seoullicious
  </div>

  <div class="menu">

    <a href="dashboard.php" class="<?= $current=='dashboard.php'?'active':'' ?>">
      <i class="fa fa-chart-line"></i> Dashboard
    </a>

    <a href="menu.php" class="<?= $current=='menu.php'?'active':'' ?>">
      <i class="fa fa-utensils"></i> Menu Management
    </a>

    <a href="order_list.php" class="<?= $current=='order_list.php'?'active':'' ?>">
      <i class="fa fa-receipt"></i> Order List
    </a>

    <a href="history.php" class="<?= $current=='history.php'?'active':'' ?>">
      <i class="fa fa-clock"></i> History Penjualan
    </a>
    <a href="review.php" class="nav-item"><i class="fas fa-star"></i> Review</a>


  </div>

  <div class="bottom">
    <a href="../auth/logout.php">
      <i class="fa fa-right-from-bracket"></i> Logout
    </a>
  </div>

</div>