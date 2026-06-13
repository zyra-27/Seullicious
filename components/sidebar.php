<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<style>
.sidebar{
  width:200px;
  min-width:200px;
  flex-shrink:0;
  position:relative;
  height:100vh;
  background:#8B5A2B;
  background-image: url('../assets/bg-pos.jpg');
  background-size:cover;
  background-position:center;
  background-blend-mode:multiply;
  display:flex;
  flex-direction:column;
  padding:24px 16px;
  z-index:10;
  transition:width 0.3s, min-width 0.3s, padding 0.3s;
  overflow:hidden;
}
.sidebar.hide{
  width:0;
  min-width:0;
  padding:0;
}
.sidebar h2{
  color:#f5d080;
  font-size:18px;
  font-weight:800;
  margin-bottom:32px;
  padding-left:8px;
  font-family:'Playfair Display',serif;
  white-space:nowrap;
}
.sidebar a{
  display:flex;
  align-items:center;
  gap:12px;
  padding:11px 14px;
  border-radius:12px;
  color:rgba(255,240,210,0.7);
  text-decoration:none;
  font-size:14px;
  margin-bottom:4px;
  transition:0.2s;
  white-space:nowrap;
}
.sidebar a:hover{background:rgba(255,255,255,0.1);color:#fff;}
.sidebar a.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;}
.sidebar a i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}
</style>

<div class="sidebar" id="sidebar">
  <h2>🍜 Seoullicious</h2>

  <a href="../admin/dashboard.php" class="<?= $current=='dashboard.php'?'active':'' ?>">
    <i class="fas fa-chart-bar"></i><span>Dashboard</span>
  </a>

  <a href="../admin/menu.php" class="<?= $current=='menu.php'?'active':'' ?>">
    <i class="fas fa-utensils"></i><span>Menu Management</span>
  </a>

  <a href="../admin/orders.php" class="<?= $current=='orders.php'?'active':'' ?>">
    <i class="fas fa-list"></i><span>Order List</span>
  </a>

  <a href="../admin/history.php" class="<?= $current=='history.php'?'active':'' ?>">
    <i class="fas fa-clock-rotate-left"></i><span>History Penjualan</span>
  </a>

  <a href="../admin/setting.php" class="<?= $current=='setting.php'?'active':'' ?>">
    <i class="fas fa-cog"></i><span>Setting</span>
  </a>

  <a href="../admin/review.php" class="<?= $current=='review.php'?'active':'' ?>">
    <i class="fas fa-star"></i><span>Review</span>
  </a>

  <div class="sidebar-bottom">
    <a href="../auth/logout.php">
      <i class="fas fa-right-from-bracket"></i><span>Logout</span>
    </a>
  </div>
</div>