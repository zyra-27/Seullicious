<?php
session_start();

// Kalau belum login, redirect ke login
if(!isset($_SESSION['username'])){
    header("Location: ../auth/login.php");
    exit;
}

// Kalau bukan customer (status kosong), redirect keluar
if(!empty($_SESSION['status'])){
    header("Location: ../auth/login.php");
    exit;
}
include "../config/koneksi.php";
$query = mysqli_query($koneksi,"SELECT * FROM menu");

$total_item = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $c){
        $total_item += $c['qty'];
    }
}

// Ambil top 4 menu terlaris
$top_query = mysqli_query($koneksi, "
    SELECT m.*, COUNT(oi.id_item) as total_order 
    FROM menu m
    JOIN order_items oi ON m.id_menu = oi.id_menu
    GROUP BY m.id_menu
    ORDER BY total_order DESC
    LIMIT 4
");
$top_menus = [];
while($row = mysqli_fetch_assoc($top_query)) {
    $top_menus[] = $row;
}

$jam = date('H');
if($jam >= 5 && $jam < 12) $greeting = "Good Morning";
elseif($jam >= 12 && $jam < 17) $greeting = "Good Afternoon";
elseif($jam >= 17 && $jam < 21) $greeting = "Good Evening";
else $greeting = "Good Night";

$username = $_SESSION['username'] ?? 'Kasir';

// Warna unik tiap card popular
$card_themes = [
    ['bg'=>'linear-gradient(135deg,#1a0a2e,#4a1a6b)', 'accent'=>'#c084fc', 'light'=>'#e9d5ff'],
    ['bg'=>'linear-gradient(135deg,#7c2d12,#c2410c)',  'accent'=>'#fb923c', 'light'=>'#ffedd5'],
    ['bg'=>'linear-gradient(135deg,#064e3b,#059669)',  'accent'=>'#34d399', 'light'=>'#d1fae5'],
    ['bg'=>'linear-gradient(135deg,#1e3a5f,#1d4ed8)',  'accent'=>'#60a5fa', 'light'=>'#dbeafe'],
];
?>
<!DOCTYPE html>
<html>
<head>
<title>Seoullicious POS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:#f5f0e8;display:flex;height:100vh;overflow:hidden;
}

/* ========== SIDEBAR ========== */
.sidebar{
  width:200px;min-width:200px;
  background:#8B5A2B;
  background-image: url('../assets/bg-pos.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: multiply;
  display:flex;flex-direction:column;padding:24px 16px;
  height:100vh;position:relative;z-index:10;transition:0.3s;
}
.sidebar.hide{margin-left:-200px;}
.sidebar-logo{
  color:#f5d080;font-size:18px;font-weight:800;
  margin-bottom:32px;padding-left:8px;letter-spacing:0.5px;
  font-family:'Playfair Display',serif;
}
.nav-item{
  display:flex;align-items:center;gap:12px;
  padding:11px 14px;border-radius:12px;
  color:rgba(255,240,210,0.7);text-decoration:none;
  font-size:14px;margin-bottom:4px;transition:0.2s;
}
.nav-item:hover{background:rgba(255,255,255,0.1);color:#fff;}
.nav-item.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;}
.nav-item i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}

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
.topbar-right{display:flex;align-items:center;gap:12px;}
.search-box{
  display:flex;align-items:center;gap:8px;
  background:#f5f0e8;border-radius:10px;padding:8px 14px;
  border:1px solid #e0d0bc;
}
.search-box input{border:none;background:transparent;outline:none;font-size:14px;width:200px;color:#2d1a08;}
.search-box i{color:#a08060;font-size:13px;}
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

/* ========== CATEGORY TABS ========== */
.cat-bar{
  padding:16px 24px 0;background:white;
  display:flex;gap:8px;border-bottom:1px solid #e8ddd0;
  overflow-x:auto;flex-shrink:0;
}
.cat-tab{
  padding:8px 18px;border-radius:10px 10px 0 0;
  border:none;background:transparent;cursor:pointer;
  font-size:13px;font-weight:600;color:#a08060;
  border-bottom:3px solid transparent;transition:0.2s;white-space:nowrap;
}
.cat-tab.active{color:#8b5a1a;border-bottom-color:#c28b3c;}
.cat-tab:hover{color:#8b5a1a;}

/* ========== MENU AREA ========== */
.menu-area{flex:1;overflow-y:auto;padding:20px 24px;}

/* ========== POPULAR SECTION ========== */
.popular-section{
  margin-bottom:24px;
  background:linear-gradient(135deg,#1c0f00,#3d2000);
  border-radius:24px;
  padding:22px;
  position:relative;
  overflow:hidden;
}
.popular-section::before{
  content:'';position:absolute;top:-70px;right:-70px;
  width:260px;height:260px;
  background:radial-gradient(circle,rgba(194,139,60,0.22) 0%,transparent 70%);
  border-radius:50%;pointer-events:none;
}
.popular-section::after{
  content:'';position:absolute;bottom:-90px;left:20px;
  width:220px;height:220px;
  background:radial-gradient(circle,rgba(245,208,128,0.1) 0%,transparent 70%);
  border-radius:50%;pointer-events:none;
}
.popular-top{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;position:relative;z-index:1;
}
.popular-top-left{display:flex;align-items:center;gap:12px;}
.popular-fire{
  width:42px;height:42px;
  background:linear-gradient(135deg,#f97316,#fbbf24);
  border-radius:13px;display:flex;align-items:center;justify-content:center;
  font-size:21px;flex-shrink:0;
  box-shadow:0 4px 16px rgba(249,115,22,0.5);
  animation:pulse-fire 2s infinite;
}
@keyframes pulse-fire{
  0%,100%{box-shadow:0 4px 16px rgba(249,115,22,0.5);}
  50%{box-shadow:0 4px 26px rgba(249,115,22,0.8);}
}
.popular-title{
  font-family:'Playfair Display',serif;
  font-size:21px;font-weight:900;color:white;line-height:1.1;
}
.popular-title span{color:#f5d080;}
.popular-subtitle-text{font-size:11px;color:rgba(255,240,200,0.5);margin-top:2px;}
.popular-badge-pill{
  background:linear-gradient(135deg,#c28b3c,#f5d080);
  color:#2d1a08;font-size:10px;font-weight:800;
  padding:6px 15px;border-radius:20px;letter-spacing:1px;
  text-transform:uppercase;
  box-shadow:0 3px 12px rgba(194,139,60,0.55);
  white-space:nowrap;
}
.popular-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:12px;
  position:relative;z-index:1;
}
.popular-card{
  border-radius:18px;overflow:hidden;cursor:pointer;
  transition:transform 0.3s ease, box-shadow 0.3s ease;
  position:relative;display:flex;flex-direction:column;
  animation:fadeSlideUp 0.5s ease both;
}
.popular-card:nth-child(1){animation-delay:0.05s;}
.popular-card:nth-child(2){animation-delay:0.12s;}
.popular-card:nth-child(3){animation-delay:0.19s;}
.popular-card:nth-child(4){animation-delay:0.26s;}
@keyframes fadeSlideUp{
  from{opacity:0;transform:translateY(16px);}
  to{opacity:1;transform:translateY(0);}
}
.popular-card:hover{
  transform:translateY(-5px) scale(1.015);
  box-shadow:0 18px 36px rgba(0,0,0,0.45);
}
.popular-card-img-wrap{
  width:100%;height:140px;overflow:hidden;flex-shrink:0;position:relative;
}
.popular-card-img-wrap img{
  width:100%;height:100%;object-fit:cover;object-position:center;
  display:block;transition:transform 0.4s ease;
}
.popular-card:hover .popular-card-img-wrap img{transform:scale(1.07);}
.popular-card-img-placeholder{
  width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:50px;
}
.pop-rank{
  position:absolute;top:10px;left:10px;z-index:3;
  width:30px;height:30px;border-radius:9px;
  display:flex;align-items:center;justify-content:center;font-size:15px;
  background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);
  border:1.5px solid rgba(255,255,255,0.2);
}
.pop-hot-badge{
  position:absolute;top:10px;right:10px;z-index:3;
  background:linear-gradient(135deg,#ef4444,#f97316);
  color:white;font-size:9px;font-weight:800;
  padding:3px 8px;border-radius:20px;
  letter-spacing:0.5px;text-transform:uppercase;
}
.popular-card-body{
  padding:11px 12px 13px;flex:1;
  display:flex;flex-direction:column;justify-content:space-between;
}
.pop-name{
  font-size:12px;font-weight:700;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px;
}
.pop-orders-row{
  font-size:10px;font-weight:600;display:flex;align-items:center;gap:3px;
  margin-bottom:9px;opacity:0.85;
}
.pop-footer{display:flex;align-items:center;justify-content:space-between;}
.pop-price{font-size:13px;font-weight:800;color:white;}
.pop-add-btn{
  border:none;border-radius:9px;padding:5px 11px;font-size:11px;font-weight:700;
  cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:3px;
}
.pop-add-btn:hover{transform:scale(1.08);}

/* ========== DIVIDER ========== */
.section-divider{
  display:flex;align-items:center;gap:12px;margin-bottom:16px;
}
.section-divider span{font-size:14px;font-weight:700;color:#2d1a08;white-space:nowrap;}
.section-divider::before,.section-divider::after{
  content:'';flex:1;height:1px;background:#e0d0bc;
}

/* ========== MENU GRID ========== */
.menu-grid{
  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:16px;
}

/* ========== MENU CARD + HOVER TOOLTIP ========== */
.menu-card{
  background:white;border-radius:16px;padding:16px;
  text-align:center;border:1px solid #ede0cc;
  transition:0.25s ease;cursor:pointer;
  position:relative;
  z-index:1;
}
.menu-card:hover{
  transform:scale(1.18);
  box-shadow:0 20px 50px rgba(139,90,26,0.28);
  z-index:10;
}
.menu-card img{
  width:100px;height:100px;object-fit:cover;
  object-position:center;border-radius:12px;
  background:#f5f0e8;display:block;margin:0 auto;
}
.menu-card h4{font-size:15px;font-weight:700;color:#2d1a08;margin:10px 0 4px;}
.menu-card p{font-size:14px;color:#8b5a1a;font-weight:600;margin-bottom:10px;}
.add-btn{
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;padding:7px 18px;
  border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;
  width:100%;transition:0.2s;
}
.add-btn:hover{opacity:0.88;}
.qty-box{display:flex;justify-content:center;align-items:center;gap:8px;width:100%;}
.qty-btn{
  background:#f5f0e8;border:1px solid #d4b090;color:#8b5a1a;
  width:28px;height:28px;border-radius:8px;cursor:pointer;font-size:16px;font-weight:700;
  display:flex;align-items:center;justify-content:center;
}
.qty-num{font-size:15px;font-weight:700;color:#2d1a08;min-width:20px;text-align:center;}



/* ========== FLOATING CART BUTTON ========== */
#cart-float-btn{
  position:fixed;bottom:28px;right:28px;z-index:1500;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;border-radius:20px;
  padding:14px 22px;font-size:15px;font-weight:700;
  cursor:pointer;display:flex;align-items:center;gap:10px;
  box-shadow:0 8px 28px rgba(139,90,26,0.45);
  transition:transform 0.2s,box-shadow 0.2s;
}
#cart-float-btn:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(139,90,26,0.55);}
#cart-float-btn .cart-badge{
  background:white;color:#8b5a1a;
  font-size:12px;font-weight:800;
  width:22px;height:22px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
}
#cart-float-btn.hidden{display:none;}

/* ========== CHECKOUT MODAL ========== */
#checkout-modal-overlay{
  position:fixed;inset:0;z-index:3000;
  background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);
  display:none;align-items:center;justify-content:center;
  animation:fadeIn 0.2s ease;
}
#checkout-modal-overlay.open{display:flex;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
#checkout-modal{
  background:white;border-radius:24px;
  width:420px;max-width:94vw;max-height:88vh;
  display:flex;flex-direction:column;
  box-shadow:0 30px 80px rgba(0,0,0,0.35);
  animation:slideUp 0.3s ease;
}
@keyframes slideUp{from{transform:translateY(30px);opacity:0;}to{transform:translateY(0);opacity:1;}}
.modal-header{
  padding:20px 22px 16px;border-bottom:1px solid #ede0cc;
  display:flex;align-items:center;justify-content:space-between;
}
.modal-header-left h3{font-size:18px;font-weight:800;color:#2d1a08;}
.modal-header-left p{font-size:12px;color:#a08060;margin-top:2px;}
.modal-close{
  width:34px;height:34px;border-radius:10px;border:1px solid #ede0cc;
  background:#f5f0e8;cursor:pointer;font-size:18px;
  display:flex;align-items:center;justify-content:center;color:#8b5a1a;
  transition:0.2s;
}
.modal-close:hover{background:#ede0cc;}
.modal-items{flex:1;overflow-y:auto;padding:14px 20px;}
.order-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 0;border-bottom:1px solid #f0e8dc;
}
.order-item img{width:44px;height:44px;border-radius:10px;object-fit:cover;background:#f5f0e8;}
.order-item-info{flex:1;}
.order-item-info b{font-size:14px;color:#2d1a08;display:block;}
.order-item-info span{font-size:13px;color:#c28b3c;font-weight:600;}
.order-item-qty{display:flex;align-items:center;gap:6px;}
.oqty-btn{
  background:#f5f0e8;color:#8b5a1a;
  width:26px;height:26px;border-radius:7px;cursor:pointer;font-size:14px;
  display:flex;align-items:center;justify-content:center;border:none;
}
.modal-footer{padding:18px 22px;border-top:1px solid #ede0cc;}
.total-row{display:flex;justify-content:space-between;margin-bottom:7px;}
.total-row span{font-size:14px;color:#a08060;}
.total-row b{font-size:14px;color:#2d1a08;}
.total-row.grand span,.total-row.grand b{font-size:17px;font-weight:800;color:#2d1a08;}
.checkout-btn{
  width:100%;padding:14px;margin-top:14px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;border-radius:12px;
  font-size:16px;font-weight:700;cursor:pointer;transition:0.2s;
}
.checkout-btn:hover{opacity:0.88;}
.empty-cart{text-align:center;padding:40px 20px;color:#c0a080;}
.empty-cart i{font-size:36px;margin-bottom:10px;display:block;}
.empty-cart p{font-size:13px;}

/* ========== LICI ========== */
#lici-button{
  position:fixed;bottom:20px;left:220px;
  width:52px;height:52px;background:#2d1a08;
  color:white;border-radius:50%;display:flex;align-items:center;
  justify-content:center;cursor:pointer;z-index:2000;font-size:20px;
  box-shadow:0 4px 14px rgba(0,0,0,0.3);transition:left 0.3s;
}
#lici-chat{
  position:fixed;bottom:82px;left:220px;width:340px;height:480px;
  background:white;border-radius:16px;display:none;flex-direction:column;
  box-shadow:0 15px 40px rgba(0,0,0,0.2);z-index:2000;
  border:1px solid #e8ddd0;transition:left 0.3s;
}
.lici-header{
  background:linear-gradient(135deg,#2d1a08,#8b5a1a);
  color:white;padding:14px 16px;border-radius:16px 16px 0 0;
  display:flex;justify-content:space-between;align-items:center;
}
.lici-header span:first-child{font-weight:700;font-size:15px;}
.lici-header span:last-child{cursor:pointer;opacity:0.7;}
#lici-messages{flex:1;padding:14px;overflow:auto;background:#faf8f5;}
.bubble{padding:10px 14px;border-radius:14px;margin-bottom:8px;max-width:82%;font-size:13px;line-height:1.5;}
.user{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;margin-left:auto;}
.ai{background:white;border:1px solid #ede0cc;color:#2d1a08;}
.suggest{padding:8px 14px;display:flex;gap:6px;flex-wrap:wrap;border-top:1px solid #f0e8dc;}
.suggest button{
  background:#f5f0e8;border:1px solid #e0d0bc;
  padding:5px 12px;border-radius:8px;cursor:pointer;font-size:12px;color:#8b5a1a;
}
.lici-input{display:flex;border-top:1px solid #e8ddd0;}
.lici-input input{flex:1;padding:12px 14px;border:none;outline:none;font-size:13px;background:#faf8f5;}
.lici-input button{background:#2d1a08;color:white;border:none;padding:0 14px;cursor:pointer;}
.lici-input button:last-child{border-radius:0 0 16px 0;}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">🍜 Seoullicious</div>
  <a href="home.php" class="nav-item"><i class="fas fa-house"></i> Home</a>
  <a href="pos.php" class="nav-item active"><i class="fas fa-utensils"></i> Menu</a>
  <a href="history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> History</a>
  <a href="review_buka.php" class="nav-item"><i class="fas fa-star"></i> Review</a>
  <div class="sidebar-bottom">
    <a href="../auth/logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> Logout</a>
  </div>
</div>

<!-- MAIN -->
<div class="main" id="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" id="toggleSidebar">☰</button>
      <div class="greeting">
        <h2><?= $greeting ?>, <?= ucfirst($username) ?>!</h2>
        <p><?= date('l, d F Y') ?></p>
      </div>
    </div>
    <div class="topbar-right">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchMenu" placeholder="Search menu...">
      </div>
      <div class="user-badge">
        <div class="user-avatar"><?= strtoupper(substr($username,0,1)) ?></div>
        <span><?= ucfirst($username) ?></span>
      </div>
    </div>
  </div>

  <!-- CATEGORY TABS -->
  <div class="cat-bar">
    <button class="cat-tab active" onclick="filterCat('all',this)">All</button>
    <button class="cat-tab" onclick="filterCat('food',this)">🍱 Food</button>
    <button class="cat-tab" onclick="filterCat('drink',this)">🥤 Drink</button>
    <button class="cat-tab" onclick="filterCat('snack',this)">🍢 Snack</button>
  </div>

  <!-- MENU AREA -->
  <div class="menu-area">

    <!-- POPULAR SECTION -->
    <?php if (!empty($top_menus)): ?>
    <div class="popular-section">
      <div class="popular-top">
        <div class="popular-top-left">
          <div class="popular-fire">🔥</div>
          <div>
            <div class="popular-title">Most <span>Popular</span></div>
            <div class="popular-subtitle-text">Menu paling banyak dipesan</div>
          </div>
        </div>
        <div class="popular-badge-pill">★ Best Seller</div>
      </div>

      <div class="popular-grid">
        <?php foreach($top_menus as $i => $item):
          $rank   = $i + 1;
          $nama   = $item['nama_menu'] ?? $item['nama'] ?? 'Menu';
          $harga  = $item['harga'] ?? 0;
          $gambar = $item['gambar'] ?? '';
          $id     = $item['id_menu'] ?? $item['id'] ?? 0;
          $total  = $item['total_order'] ?? 0;
          $rankEmoji = ['🥇','🥈','🥉','⭐'][$i] ?? '⭐';
          $t = $card_themes[$i];
        ?>
        <div class="popular-card" style="background:<?= $t['bg'] ?>;">
          <div class="pop-rank"><?= $rankEmoji ?></div>
          <?php if($rank === 1): ?><div class="pop-hot-badge">🔥 #1 Today</div><?php endif; ?>

          <div class="popular-card-img-wrap">
            <?php if(!empty($gambar)): ?>
              <img src="../assets/<?= htmlspecialchars($gambar) ?>"
                   onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                   alt="<?= htmlspecialchars($nama) ?>">
              <div class="popular-card-img-placeholder" style="display:none;background:<?= $t['bg'] ?>">🍽️</div>
            <?php else: ?>
              <div class="popular-card-img-placeholder" style="background:<?= $t['bg'] ?>">🍽️</div>
            <?php endif; ?>
          </div>

          <div class="popular-card-body">
            <div>
              <div class="pop-name" style="color:<?= $t['light'] ?>"><?= htmlspecialchars($nama) ?></div>
              <div class="pop-orders-row" style="color:<?= $t['accent'] ?>">
                <i class="fas fa-fire" style="font-size:9px;"></i> <?= $total ?> kali dipesan
              </div>
            </div>
            <div class="pop-footer">
              <div class="pop-price">Rp <?= number_format($harga,0,',','.') ?></div>
              <button class="pop-add-btn"
                style="background:<?= $t['accent'] ?>;color:#111;"
                onclick="addItem(<?= $id ?>, '<?= addslashes($nama) ?>', <?= $harga ?>, '<?= $gambar ?>')">
                <i class="fas fa-plus" style="font-size:9px;"></i> Add
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- DIVIDER -->
    <div class="section-divider"><span>📋 Semua Menu</span></div>

    <!-- MENU GRID -->
    <div class="menu-grid" id="menuGrid">
      <?php
      mysqli_data_seek($query, 0);
      while($data = mysqli_fetch_array($query)): ?>
      <div class="menu-card"
        data-name="<?= strtolower($data['nama_menu']) ?>"
        data-cat="<?= strtolower($data['kategori']) ?>">

        <img src="../assets/<?= $data['gambar'] ?>" onerror="this.src='../assets/default.png'">
        <h4><?= $data['nama_menu'] ?></h4>
        <p>Rp <?= number_format($data['harga'],0,',','.') ?></p>
        <div id="box-<?= $data['id_menu'] ?>">
          <button class="add-btn" onclick="addItem(<?= $data['id_menu'] ?>, '<?= addslashes($data['nama_menu']) ?>', <?= $data['harga'] ?>, '<?= $data['gambar'] ?>')">+ Add</button>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<!-- FLOATING CART BUTTON -->
<button id="cart-float-btn" class="hidden" onclick="openCheckoutModal()">
  <i class="fas fa-shopping-basket"></i>
  <span id="cart-float-label">0 item</span>
  <div class="cart-badge" id="cart-float-badge">0</div>
</button>

<!-- CHECKOUT MODAL POPUP -->
<div id="checkout-modal-overlay" onclick="handleOverlayClick(event)">
  <div id="checkout-modal">
    <div class="modal-header">
      <div class="modal-header-left">
        <h3>🛒 My Order</h3>
        <p id="order-count">0 item</p>
      </div>
      <button class="modal-close" onclick="closeCheckoutModal()">✕</button>
    </div>

    <div class="modal-items" id="orderItems">
      <div class="empty-cart">
        <i class="fas fa-bowl-food"></i>
        <p>Belum ada pesanan</p>
      </div>
    </div>

    <div class="modal-footer">
      <div class="total-row"><span>Subtotal</span><b id="subtotal">Rp 0</b></div>
      <div class="total-row"><span>Tax 10%</span><b id="tax">Rp 0</b></div>
      <div class="total-row grand"><span>Total</span><b id="grandtotal">Rp 0</b></div>
      <button class="checkout-btn" onclick="checkout()">Checkout →</button>
    </div>
  </div>
</div>

<!-- LICI AI -->
<div id="lici-button">🤖</div>
<div id="lici-chat">
  <div class="lici-header">
    <span>🤖 Lici AI</span><span onclick="closeLici()">✖</span>
  </div>
  <div id="lici-messages"></div>
  <div class="suggest">
    <button onclick="quickAsk('rekomendasi')">🍜 Rekomendasi</button>
    <button onclick="quickAsk('murah')">💸 Murah</button>
    <button onclick="quickAsk('minuman')">🥤 Minuman</button>
    <button onclick="quickAsk('pedas')">🌶 Pedas</button>
  </div>
  <div class="lici-input">
    <input id="lici-text" placeholder="Tanya menu...">
    <button onclick="sendLici()">➤</button>
    <button onclick="startVoice()">🎤</button>
  </div>
</div>

<script>
let cart={};

function addItem(id,name,price,img){
  if(cart[id])cart[id].qty++;
  else cart[id]={id,name,price,img,qty:1};
  fetch("../process/add_cart.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"id_menu="+id});
  renderQty(id,cart[id].qty);
  renderOrderPanel();
  updateFloatBtn();
}

function renderQty(id,qty){
  const box=document.getElementById("box-"+id);
  if(box)box.innerHTML=`<div class="qty-box"><button class="qty-btn" onclick="changeQty(${id},-1)">−</button><span class="qty-num" id="qty-${id}">${qty}</span><button class="qty-btn" onclick="changeQty(${id},1)">+</button></div>`;
}

function changeQty(id,val){
  if(!cart[id])return;
  cart[id].qty+=val;
  if(cart[id].qty<=0){
    const s={...cart[id]};delete cart[id];
    const box=document.getElementById("box-"+id);
    if(box)box.innerHTML=`<button class="add-btn" onclick="addItem(${id},'${s.name}',${s.price},'${s.img}')">+ Add</button>`;
    renderOrderPanel();updateFloatBtn();return;
  }
  const el=document.getElementById("qty-"+id);if(el)el.innerText=cart[id].qty;
  fetch("../process/add_cart.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"id_menu="+id+(val<0?"&action=dec":"")});
  renderOrderPanel();updateFloatBtn();
}

function renderOrderPanel(){
  let items=Object.values(cart),html="",subtotal=0;
  if(!items.length){
    document.getElementById("orderItems").innerHTML=`<div class="empty-cart"><i class="fas fa-bowl-food"></i><p>Belum ada pesanan</p></div>`;
    ["order-count","subtotal","tax","grandtotal"].forEach((id,i)=>document.getElementById(id).innerText=i===0?"0 item":"Rp 0");
    return;
  }
  items.forEach(item=>{
    let sub=item.price*item.qty;subtotal+=sub;
    html+=`<div class="order-item">
      <img src="../assets/${item.img}" onerror="this.src='../assets/default.png'">
      <div class="order-item-info"><b>${item.name}</b><span>Rp ${sub.toLocaleString('id-ID')}</span></div>
      <div class="order-item-qty">
        <button class="oqty-btn" onclick="changeQty(${item.id},-1)">−</button>
        <span style="font-size:14px;font-weight:700;min-width:20px;text-align:center">${item.qty}</span>
        <button class="oqty-btn" onclick="changeQty(${item.id},1)">+</button>
      </div>
    </div>`;
  });
  let tax=Math.round(subtotal*.1),grand=subtotal+tax,totalQty=items.reduce((s,i)=>s+i.qty,0);
  document.getElementById("orderItems").innerHTML=html;
  document.getElementById("order-count").innerText=totalQty+" item";
  document.getElementById("subtotal").innerText="Rp "+subtotal.toLocaleString('id-ID');
  document.getElementById("tax").innerText="Rp "+tax.toLocaleString('id-ID');
  document.getElementById("grandtotal").innerText="Rp "+grand.toLocaleString('id-ID');
}

function updateFloatBtn(){
  const btn=document.getElementById("cart-float-btn");
  const items=Object.values(cart);
  const totalQty=items.reduce((s,i)=>s+i.qty,0);
  if(totalQty===0){btn.classList.add("hidden");return;}
  btn.classList.remove("hidden");
  document.getElementById("cart-float-label").innerText=totalQty+" item";
  document.getElementById("cart-float-badge").innerText=totalQty;
}

function openCheckoutModal(){
  document.getElementById("checkout-modal-overlay").classList.add("open");
}
function closeCheckoutModal(){
  document.getElementById("checkout-modal-overlay").classList.remove("open");
}
function handleOverlayClick(e){
  if(e.target===document.getElementById("checkout-modal-overlay")) closeCheckoutModal();
}
function checkout(){
  if(!Object.keys(cart).length){alert("Keranjang kosong!");return;}
  window.location.href="cart.php";
}

document.getElementById("toggleSidebar").onclick=function(){
  document.getElementById("sidebar").classList.toggle("hide");
  let h=document.getElementById("sidebar").classList.contains("hide");
  document.getElementById("lici-button").style.left=h?"20px":"220px";
  document.getElementById("lici-chat").style.left=h?"20px":"220px";
};

document.getElementById("searchMenu").onkeyup=function(){
  let k=this.value.toLowerCase();
  document.querySelectorAll(".menu-card").forEach(c=>c.style.display=c.dataset.name.includes(k)?"block":"none");
};

function filterCat(cat,el){
  document.querySelectorAll(".cat-tab").forEach(t=>t.classList.remove("active"));el.classList.add("active");
  document.querySelectorAll(".menu-card").forEach(c=>c.style.display=(cat==="all"||c.dataset.cat===cat)?"block":"none");
}

document.getElementById("lici-button").onclick=()=>document.getElementById("lici-chat").style.display="flex";
function closeLici(){document.getElementById("lici-chat").style.display="none";}
function quickAsk(t){document.getElementById("lici-text").value=t;sendLici();}
function sendLici(){
  let text=document.getElementById("lici-text").value;if(!text.trim())return;
  let box=document.getElementById("lici-messages");
  box.innerHTML+=`<div class="bubble user">${text}</div>`;
  fetch("../ai/api.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"message="+encodeURIComponent(text)})
  .then(r=>r.json()).then(data=>{
    if(data.error){box.innerHTML+=`<div class="bubble ai">${data.error}</div>`;return;}
    box.innerHTML+=`<div class="bubble ai">${data.response}</div>`;
    if(data.recommendations?.length){
      let c=document.createElement("div");c.style.cssText="display:flex;gap:8px;flex-wrap:wrap;padding:4px 0;";
      data.recommendations.forEach(item=>{c.innerHTML+=`<div style="width:120px;background:white;border-radius:10px;padding:10px;text-align:center;border:1px solid #ede0cc;"><img src="../assets/${item.image}" style="width:70px;height:70px;object-fit:cover;border-radius:8px;"><br><b style="font-size:12px;color:#2d1a08;">${item.name}</b><br><span style="font-size:11px;color:#c28b3c;font-weight:600;">Rp ${item.price.toLocaleString('id-ID')}</span><br><button onclick="addItem(${item.id},'${item.name}',${item.price},'${item.image}')" style="background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;border:none;padding:5px 10px;border-radius:7px;margin-top:5px;font-size:11px;cursor:pointer;">+ Add</button></div>`;});
      box.appendChild(c);
    }
    if(data.follow_up)box.innerHTML+=`<div class="bubble ai">${data.follow_up}</div>`;
    box.scrollTop=box.scrollHeight;
  });
  document.getElementById("lici-text").value="";
}
function startVoice(){
  if(!('webkitSpeechRecognition' in window)){alert("Browser tidak support voice");return;}
  let r=new webkitSpeechRecognition();r.lang="id-ID";
  r.onresult=e=>{document.getElementById("lici-text").value=e.results[0][0].transcript;sendLici();};r.start();
}
</script>
</body>
</html>