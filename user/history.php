<?php
session_start();
include "../config/koneksi.php";

$username = $_SESSION['username'] ?? '';
$id_user  = $_SESSION['id_user'] ?? 0;

if(empty($username)) {
    header("Location: ../auth/login.php");
    exit;
}

// ── Language + profile dari DB ──────────────────────────────────────────────
$set_row = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM user_settings WHERE user_id='$id_user' LIMIT 1"
));
$lang = $set_row['language'] ?? 'id';
if(isset($_POST['switch_lang'])){
    $nl = $_POST['switch_lang'] === 'en' ? 'en' : 'id';
    if($set_row){
        mysqli_query($koneksi,"UPDATE user_settings SET language='$nl' WHERE user_id='$id_user'");
    } else {
        mysqli_query($koneksi,"INSERT INTO user_settings (user_id,language) VALUES ('$id_user','$nl')");
    }
    $lang = $nl;
    header("Location: history.php"); exit;
}
$display_name = $set_row['display_name'] ?? $username;
$photo_path   = $set_row['photo_path']   ?? '';
if(empty($display_name)) $display_name = $username;

$jam = date('H');
if($lang === 'en'){
    if($jam>=5&&$jam<12)      $greeting = "Good Morning";
    elseif($jam>=12&&$jam<17) $greeting = "Good Afternoon";
    elseif($jam>=17&&$jam<21) $greeting = "Good Evening";
    else                       $greeting = "Good Night";
} else {
    if($jam>=5&&$jam<12)      $greeting = "Selamat Pagi";
    elseif($jam>=12&&$jam<17) $greeting = "Selamat Siang";
    elseif($jam>=17&&$jam<21) $greeting = "Selamat Sore";
    else                       $greeting = "Selamat Malam";
}

// ── i18n ─────────────────────────────────────────────────────────────────────
$t = $lang === 'en' ? [
    'page_title'   => 'History — Seoullicious',
    'main_menu'    => 'Main Menu',
    'nav_home'     => 'Home',
    'nav_menu'     => 'Menu',
    'nav_history'  => 'History',
    'nav_review'   => 'Review',
    'nav_logout'   => 'Logout',
    'title_h'      => 'Transaction History',
    'title_sub'    => 'Your order records',
    'stat_total'   => 'My Total Orders',
    'stat_today'   => "Today's Orders",
    'history_head' => 'Transaction Records',
    'total_order'  => 'total orders',
    'empty'        => 'No transactions yet',
    'detail'       => 'Detail',
    'dine_in'      => 'Dine In',
    'take_away'    => 'Take Away',
    'table'        => 'Table',
] : [
    'page_title'   => 'Riwayat — Seoullicious',
    'main_menu'    => 'Menu Utama',
    'nav_home'     => 'Beranda',
    'nav_menu'     => 'Menu',
    'nav_history'  => 'Riwayat',
    'nav_review'   => 'Ulasan',
    'nav_logout'   => 'Keluar',
    'title_h'      => 'Riwayat Transaksi',
    'title_sub'    => 'Riwayat pesanan kamu',
    'stat_total'   => 'Total Order Saya',
    'stat_today'   => 'Order Hari Ini',
    'history_head' => 'Riwayat Transaksi',
    'total_order'  => 'total order',
    'empty'        => 'Belum ada transaksi',
    'detail'       => 'Detail',
    'dine_in'      => 'Dine In',
    'take_away'    => 'Take Away',
    'table'        => 'Meja',
];

$limit  = 10;
$page   = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// ✅ Pakai id_user (integer), bukan username string
$count_q   = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders WHERE id_user='$id_user'");
$total_row = mysqli_fetch_assoc($count_q)['total'] ?? 0;
$total_pages = ceil($total_row / $limit);

$data = mysqli_query($koneksi,"
    SELECT * FROM orders 
    WHERE id_user='$id_user'
    ORDER BY id_order DESC 
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['page_title'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/seoullicious_shared.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'DM Sans',sans-serif;
  background:#f5f0e8;
  display:flex;height:100vh;overflow:hidden;
}

/* ========== SIDEBAR ========== */
.sidebar{
  width:200px;min-width:200px;
  background:#1c0f00;
  display:flex;flex-direction:column;padding:24px 16px;
  height:100vh;position:relative;z-index:10;
  border-right:1px solid rgba(194,139,60,0.12);
  transition:margin-left 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
}
.sidebar.hide{margin-left:-200px;opacity:0;}
.sidebar-logo{
  color:#f5d080;font-size:19px;font-weight:600;
  margin-bottom:3px;padding-left:8px;letter-spacing:0.3px;
  font-family:'Cormorant Garamond',serif;
}
.sidebar-logo-sub{
  font-size:9px;color:rgba(245,208,128,0.3);
  padding-left:8px;margin-bottom:32px;letter-spacing:1.5px;text-transform:uppercase;
}
.nav-item{
  display:flex;align-items:center;gap:12px;
  padding:11px 14px;border-radius:12px;
  color:rgba(255,240,210,0.7);text-decoration:none;
  font-size:14px;margin-bottom:4px;transition:0.2s;
}
.nav-item:hover{background:rgba(255,255,255,0.1);color:#fff;transform:translateX(3px);}
.nav-item.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;box-shadow:0 4px 14px rgba(139,90,26,0.4);}
.nav-item i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}
.nav-label{
  font-size:9px;color:rgba(255,240,200,0.25);
  letter-spacing:1.5px;text-transform:uppercase;
  padding-left:14px;margin-bottom:8px;
}
.user-info{
  display:flex;align-items:center;gap:8px;
  padding:10px 12px;background:rgba(255,255,255,0.06);
  border-radius:12px;margin-bottom:8px;
}
.user-av{
  width:30px;height:30px;border-radius:8px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  display:flex;align-items:center;justify-content:center;
  color:white;font-size:12px;font-weight:800;flex-shrink:0;
}
.user-av-name{font-size:12px;font-weight:600;color:#f5d080;}
.user-av-role{font-size:10px;color:rgba(255,240,200,0.4);}

/* ========== MAIN ========== */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}

/* ========== TOPBAR ========== */
.topbar{
  background:white;padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;
  border-bottom:1px solid #e8ddd0;flex-shrink:0;
}
.topbar-left{display:flex;align-items:center;gap:14px;}
.toggle-btn{
  background:#f5f0e8;border:none;width:36px;height:36px;
  border-radius:10px;cursor:pointer;font-size:16px;
  display:flex;align-items:center;justify-content:center;
}
.greeting h2{font-size:18px;font-weight:700;color:#2d1a08;}
.greeting p{font-size:12px;color:#a08060;margin-top:1px;}
.user-badge{
  display:flex;align-items:center;gap:8px;
  background:#f5f0e8;padding:7px 14px;border-radius:10px;
  border:1px solid #e0d0bc;
}
.user-avatar{
  width:28px;height:28px;border-radius:8px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  display:flex;align-items:center;justify-content:center;
  color:white;font-size:12px;font-weight:700;
}
.user-badge span{font-size:13px;font-weight:600;color:#3b1f08;}
.lang-switcher{display:flex;background:#f5f0e8;border-radius:10px;padding:3px;gap:2px;border:1px solid #e0d0bc;}
.lang-btn{background:transparent;border:none;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;color:#a08060;cursor:pointer;transition:all 0.2s;font-family:inherit;}
.lang-btn.active{background:white;color:#8b5a1a;box-shadow:0 1px 4px rgba(139,90,26,0.15);}

/* ========== CONTENT AREA ========== */
.content{flex:1;overflow-y:auto;padding:24px;}

/* ========== PAGE TITLE ========== */
.page-title{
  display:flex;align-items:center;gap:12px;margin-bottom:20px;
}
.page-title-icon{
  width:42px;height:42px;border-radius:12px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  display:flex;align-items:center;justify-content:center;
  color:white;font-size:18px;
}
.page-title h3{
  font-family:'Cormorant Garamond',serif;
  font-size:22px;font-weight:700;color:#2d1a08;
}
.page-title p{font-size:12px;color:#a08060;margin-top:1px;}

/* ========== STATS ROW ========== */
.stats-row{
  display:grid;grid-template-columns:repeat(2,1fr);gap:14px;
  margin-bottom:20px;
}
.stat-card{
  background:white;border-radius:16px;padding:16px 20px;
  border:1px solid #ede0cc;display:flex;align-items:center;gap:14px;
}
.stat-icon{
  width:44px;height:44px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;font-size:20px;
  flex-shrink:0;
}
.stat-label{font-size:12px;color:#a08060;margin-bottom:3px;}
.stat-value{font-size:18px;font-weight:800;color:#2d1a08;}

/* ========== HISTORY TABLE ========== */
.history-wrap{
  background:white;border-radius:20px;
  border:1px solid #ede0cc;overflow:hidden;
}
.history-header{
  padding:16px 22px;border-bottom:1px solid #ede0cc;
  display:flex;align-items:center;justify-content:space-between;
}
.history-header span{font-size:14px;font-weight:700;color:#2d1a08;}
.history-header small{font-size:12px;color:#a08060;}

.history-row{
  display:flex;align-items:center;
  padding:15px 22px;border-bottom:1px solid #f5ede0;
  transition:background 0.18s;gap:16px;
}
.history-row:last-child{border-bottom:none;}
.history-row:hover{background:#fdf8f2;transform:translateX(2px);}

.order-num{min-width:90px;}
.order-num b{font-size:14px;color:#2d1a08;display:block;}
.order-num small{font-size:11px;color:#b09070;}

.badge{
  padding:4px 12px;border-radius:20px;
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;
  white-space:nowrap;
}
.badge-dinein{background:#fef3e2;color:#c28b3c;}
.badge-takeaway{background:#e8f4fd;color:#2e86c1;}

.order-meta{
  flex:1;font-size:13px;color:#7a6552;
  display:flex;gap:20px;align-items:center;flex-wrap:wrap;
}
.meta-item{display:flex;align-items:center;gap:5px;}
.meta-item i{font-size:11px;color:#c28b3c;}

.order-price{
  font-size:15px;font-weight:800;color:#2d1a08;
  white-space:nowrap;min-width:110px;text-align:right;
}

.detail-btn{
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;padding:7px 16px;border-radius:9px;
  text-decoration:none;font-size:12px;font-weight:600;
  transition:0.2s;white-space:nowrap;
  display:flex;align-items:center;gap:5px;
}
.detail-btn:hover{opacity:0.85;transform:translateY(-1px);}



/* ========== PAGINATION ========== */
.pagination{
  display:flex;gap:6px;justify-content:center;
  padding:18px;border-top:1px solid #ede0cc;
}
.page-btn{
  width:36px;height:36px;border-radius:9px;
  border:1px solid #e0d0bc;background:#f5f0e8;
  display:flex;align-items:center;justify-content:center;
  text-decoration:none;font-size:13px;font-weight:600;
  color:#8b5a1a;transition:0.2s;
}
.page-btn:hover{background:#ede0cc;}
.page-btn.active{
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border-color:transparent;
}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ========== SIDEBAR ========== -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">Seoullicious</div>
  <div class="sidebar-logo-sub">Korean Food Experience</div>
  <a href="home.php" class="nav-item"><i class="fas fa-house"></i> <?= $t['nav_home'] ?></a>
  <a href="pos.php"     class="nav-item"><i class="fas fa-utensils"></i> <?= $t['nav_menu'] ?></a>
  <a href="history.php" class="nav-item active"><i class="fas fa-clock-rotate-left"></i> <?= $t['nav_history'] ?></a>
  <a href="review_buka.php" class="nav-item"><i class="fas fa-star"></i> <?= $t['nav_review'] ?></a>
  <div class="sidebar-bottom">
    <div class="user-info">
      <div class="user-av">
        <?php if($photo_path): ?>
          <img src="../upload/<?= htmlspecialchars($photo_path) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:8px;" alt="">
        <?php else: ?>
          <?= strtoupper(substr($display_name,0,1)) ?>
        <?php endif; ?>
      </div>
      <div>
        <div class="user-av-name"><?= htmlspecialchars($display_name) ?></div>
      </div>
    </div>
    <a href="../auth/logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> <?= $t['nav_logout'] ?></a>
  </div>
</div>

<!-- ========== MAIN ========== -->
<div class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" id="toggleSidebar">☰</button>
      <div class="greeting">
        <h2><?= $greeting ?>, <?= htmlspecialchars($display_name) ?>!</h2>
        <p><?= date('l, d F Y') ?></p>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <!-- Language switcher -->
      <div class="lang-switcher">
        <form method="POST" style="display:contents;">
          <input type="hidden" name="switch_lang" value="id">
          <button type="submit" class="lang-btn <?= $lang==='id'?'active':'' ?>">ID</button>
        </form>
        <form method="POST" style="display:contents;">
          <input type="hidden" name="switch_lang" value="en">
          <button type="submit" class="lang-btn <?= $lang==='en'?'active':'' ?>">EN</button>
        </form>
      </div>
      <div class="user-badge">
        <div class="user-avatar">
          <?php if($photo_path): ?>
            <img src="../upload/<?= htmlspecialchars($photo_path) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:8px;" alt="">
          <?php else: ?>
            <?= strtoupper(substr($display_name,0,1)) ?>
          <?php endif; ?>
        </div>
        <span><?= htmlspecialchars($display_name) ?></span>
      </div>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <div class="page-title">
      <div class="page-title-icon"><i class="fas fa-clock-rotate-left"></i></div>
      <div>
        <h3><?= $t['title_h'] ?></h3>
        <p><?= $t['title_sub'] ?></p>
      </div>
    </div>

    <!-- STATS -->
    <?php
    // ✅ Pakai id_user di semua query stats
    $stat_total = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM orders WHERE id_user='$id_user'"));
    $stat_today = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM orders WHERE id_user='$id_user' AND DATE(tanggal)=CURDATE()"));
    ?>
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef3e2;"><i class="fas fa-receipt" style="color:#c28b3c;font-size:20px;"></i></div>
        <div>
          <div class="stat-label"><?= $t['stat_total'] ?></div>
          <div class="stat-value"><?= number_format($stat_total['c']) ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#e8f8ee;"><i class="fas fa-calendar-day" style="color:#2e7d32;font-size:20px;"></i></div>
        <div>
          <div class="stat-label"><?= $t['stat_today'] ?></div>
          <div class="stat-value"><?= number_format($stat_today['c']) ?></div>
        </div>
      </div>
    </div>

    <!-- HISTORY LIST -->
    <div class="history-wrap">
      <div class="history-header">
        <span><?= $t['history_head'] ?></span>
        <small><?= $total_row ?> <?= $t['total_order'] ?></small>
      </div>

      <?php if($total_row > 0): ?>
        <?php while($row = mysqli_fetch_assoc($data)): ?>
        <div class="history-row">

          <div class="order-num">
            <b>Order #<?= $row['id_order'] ?></b>
            <small><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></small>
          </div>

          <?php
            $type = strtolower($row['order_type']);
            $badgeClass = ($type === 'dinein' || $type === 'dine-in') ? 'badge-dinein' : 'badge-takeaway';
            $badgeLabel = ($type === 'dinein' || $type === 'dine-in') ? $t['dine_in'] : $t['take_away'];
          ?>
          <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>

          <div class="order-meta">
            <div class="meta-item">
              <i class="fas fa-chair"></i>
              <?= $row["meja"] ? $t["table"]." ".$row["meja"] : $t["take_away"] ?>
            </div>
            <div class="meta-item">
              <i class="fas fa-credit-card"></i>
              <?= htmlspecialchars($row['metode_bayar']) ?>
            </div>
            <?php if(!empty($row['jam_mulai'])): ?>
            <div class="meta-item">
              <i class="fas fa-clock"></i>
              <?= $row['jam_mulai'] ?> – <?= $row['jam_selesai'] ?>
            </div>
            <?php endif; ?>
          </div>

          <div class="order-price">
            Rp <?= number_format($row['total'],0,',','.') ?>
          </div>

          <a href="receipt.php?id=<?= $row['id_order'] ?>" class="detail-btn">
            <i class="fas fa-receipt"></i> <?= $t["detail"] ?>
          </a>

        </div>
        <?php endwhile; ?>

        <?php if($total_pages > 1): ?>
        <div class="pagination">
          <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>" class="page-btn"><i class="fas fa-chevron-left" style="font-size:11px;"></i></a>
          <?php endif; ?>
          <?php for($i=1;$i<=$total_pages;$i++): ?>
            <a href="?page=<?= $i ?>" class="page-btn <?= $i==$page?'active':'' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?>" class="page-btn"><i class="fas fa-chevron-right" style="font-size:11px;"></i></a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      <?php else: ?>
        <div class="empty-state">
          <div class="empty-state-icon"><i class="fas fa-bowl-food" style="color:#c28b3c;font-size:30px;"></i></div>
          <div class="empty-state-title"><?= $lang==='en'?'No Orders Yet':'Belum Ada Pesanan' ?></div>
          <div class="empty-state-sub"><?= $lang==='en'?'Your transaction history will appear here after your first order.':'Riwayat transaksi kamu akan muncul di sini setelah pesanan pertama.' ?></div>
          <a href="pos.php" class="empty-state-btn"><?= $lang==='en'?'Order Now →':'Pesan Sekarang →' ?></a>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
const _sb=document.getElementById('sidebar'),_ov=document.getElementById('sidebarOverlay');
document.getElementById('toggleSidebar').onclick=function(){
  if(window.innerWidth<=768){_sb.classList.toggle('mobile-open');_ov.classList.toggle('show');}
  else{_sb.classList.toggle('hide');}
};
_ov.onclick=function(){_sb.classList.remove('mobile-open');_ov.classList.remove('show');};
</script>
</body>
</html>