<?php $current = basename($_SERVER['PHP_SELF']); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body{margin:0}
.sidebar{width:240px;height:100vh;background:#1a1008;position:fixed;left:0;top:0;display:flex;flex-direction:column;z-index:9999;border-right:1px solid rgba(184,137,58,0.1)}
.sidebar-brand{padding:28px 24px 24px;border-bottom:1px solid rgba(255,255,255,0.06)}
.sidebar-brand-name{font-family:'Cormorant Garamond',serif;font-size:18px;color:#e8c97a;display:block}
.sidebar-brand-sub{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.25);margin-top:3px;font-family:'DM Sans',sans-serif}
.sidebar-section-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.2);padding:20px 24px 8px;font-family:'DM Sans',sans-serif}
.sidebar-nav{padding:4px 12px;flex:1}
.sidebar-nav a{display:flex;align-items:center;gap:11px;padding:10px 14px;border-radius:8px;text-decoration:none;color:rgba(255,255,255,0.45);font-size:13.5px;margin-bottom:2px;transition:all .15s;font-family:'DM Sans',sans-serif}
.sidebar-nav a i{width:16px;text-align:center;font-size:13px}
.sidebar-nav a:hover{background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.8)}
.sidebar-nav a.active{background:rgba(184,137,58,0.15);color:#e8c97a;font-weight:500}
.sidebar-bottom{padding:12px 12px 24px;border-top:1px solid rgba(255,255,255,0.06)}
.sidebar-bottom a{display:flex;align-items:center;gap:11px;padding:10px 14px;border-radius:8px;color:rgba(255,100,80,0.6);text-decoration:none;font-size:13.5px;transition:all .15s;font-family:'DM Sans',sans-serif}
.sidebar-bottom a:hover{background:rgba(200,80,60,0.12);color:rgba(255,120,90,0.9)}
</style>
<div class="sidebar">
  <div class="sidebar-brand">
    <span class="sidebar-brand-name">Seoullicious</span>
    <span class="sidebar-brand-sub">Admin Panel</span>
  </div>
  <div class="sidebar-section-label">Manajemen</div>
  <div class="sidebar-nav">
    <a href="dashboard.php" class="<?= $current=='dashboard.php'?'active':'' ?>"><i class="fa fa-chart-line"></i> Dashboard</a>
    <a href="menu.php" class="<?= $current=='menu.php'?'active':'' ?>"><i class="fa fa-utensils"></i> Menu</a>
    <a href="order_list.php" class="<?= $current=='order_list.php'?'active':'' ?>"><i class="fa fa-receipt"></i> Daftar Order</a>
    <a href="history.php" class="<?= $current=='history.php'?'active':'' ?>"><i class="fa fa-clock-rotate-left"></i> Riwayat</a>
    <a href="review.php" class="<?= $current=='review.php'?'active':'' ?>"><i class="fas fa-star"></i> Ulasan</a>
  </div>
  <div class="sidebar-bottom">
    <a href="../auth/logout.php"><i class="fa fa-right-from-bracket"></i> Keluar</a>
  </div>
</div>