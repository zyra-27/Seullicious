<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Cashier';

// Top 4 best seller
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Seoullicious — Home</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#faf6f0;display:flex;height:100vh;overflow:hidden;}

/* ========== SIDEBAR ========== */
.sidebar{
  width:200px;min-width:200px;
  background:#8B5A2B;
  background-image:url('../assets/bg-pos.jpg');
  background-size:cover;background-position:center;
  background-blend-mode:multiply;
  display:flex;flex-direction:column;padding:24px 16px;
  height:100vh;position:relative;z-index:10;
  transition:margin-left 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
}
.sidebar.hide{margin-left:-200px;opacity:0;}
.sidebar-logo{
  color:#f5d080;font-size:18px;font-weight:800;
  margin-bottom:4px;padding-left:8px;
  font-family:'Playfair Display',serif;
}
.sidebar-logo-sub{
  font-size:10px;color:rgba(255,240,200,0.35);
  padding-left:8px;margin-bottom:32px;letter-spacing:0.5px;
}
.nav-label{
  font-size:9px;color:rgba(255,240,200,0.3);
  letter-spacing:1px;text-transform:uppercase;
  padding-left:14px;margin-bottom:8px;
}
.nav-item{
  display:flex;align-items:center;gap:12px;
  padding:11px 14px;border-radius:12px;
  color:rgba(255,240,210,0.7);text-decoration:none;
  font-size:14px;margin-bottom:4px;
  transition:background 0.2s, color 0.2s, transform 0.15s;
}
.nav-item:hover{background:rgba(255,255,255,0.1);color:#fff;transform:translateX(3px);}
.nav-item.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;box-shadow:0 4px 14px rgba(139,90,26,0.4);}
.nav-item i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}
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
  background:white;padding:16px 28px;
  display:flex;align-items:center;justify-content:space-between;
  border-bottom:1px solid #e8ddd0;flex-shrink:0;
  animation:fadeDown 0.5s ease both;
  box-shadow:0 2px 12px rgba(139,90,26,0.05);
}
@keyframes fadeDown{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);}}
.topbar-left{display:flex;align-items:center;gap:14px;}
.toggle-btn{
  background:#f5f0e8;border:none;width:36px;height:36px;
  border-radius:10px;cursor:pointer;font-size:16px;
  display:flex;align-items:center;justify-content:center;
  transition:background 0.2s, transform 0.15s;
}
.toggle-btn:hover{background:#ede4d4;transform:scale(1.05);}
.greeting h2{font-size:18px;font-weight:700;color:#2d1a08;}
.greeting p{font-size:12px;color:#a08060;margin-top:1px;}
.topbar-right{display:flex;align-items:center;gap:12px;}
.search-box{
  display:flex;align-items:center;gap:8px;
  background:#f5f0e8;border-radius:10px;padding:8px 14px;
  border:1.5px solid transparent;
  transition:border-color 0.2s, box-shadow 0.2s;
}
.search-box:focus-within{border-color:#c28b3c;box-shadow:0 0 0 3px rgba(194,139,60,0.12);}
.search-box input{border:none;background:transparent;outline:none;font-size:14px;width:180px;color:#2d1a08;font-family:inherit;}
.search-box i{color:#a08060;font-size:13px;}

/* Ripple Button */
.order-btn{
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;border-radius:10px;
  padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;
  position:relative;overflow:hidden;
  transition:opacity 0.2s, transform 0.15s;
}
.order-btn:hover{opacity:0.88;transform:translateY(-1px);}
.order-btn::after{
  content:'';position:absolute;border-radius:50%;
  background:rgba(255,255,255,0.35);
  width:100px;height:100px;top:50%;left:50%;
  transform:translate(-50%,-50%) scale(0);opacity:1;
  transition:transform 0.5s ease, opacity 0.5s ease;
  pointer-events:none;
}
.order-btn:active::after{transform:translate(-50%,-50%) scale(3);opacity:0;transition:0s;}

/* ========== CONTENT ========== */
.content{flex:1;overflow-y:auto;padding:24px 28px;scroll-behavior:smooth;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

/* ========== HERO ========== */
.hero{
  display:flex;align-items:stretch;
  margin-bottom:28px;
  animation:fadeUp 0.6s ease 0.15s both;
  border-radius:24px;
  overflow:hidden;
  border:1px solid #ded0b8;
  min-height:320px;
}

/* LEFT PANEL — photo bg + overlay */
.hero-left{
  flex:1;position:relative;
  padding:36px 36px;
  /* background photo from sidebar asset, fallback to warm cream */
  background-image:url('../assets/bg-pos.jpg');
  background-size:cover;
  background-position:center;
  display:flex;flex-direction:column;justify-content:center;
}
/* Low-opacity overlay so text stays readable */
.hero-left::before{
  content:'';position:absolute;inset:0;
  background:rgba(250,246,240,0.82);
  backdrop-filter:blur(1px);
  pointer-events:none;z-index:0;
}
.hero-left > *{position:relative;z-index:1;}
.hero-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:#fff8ee;border:1px solid #f0d9a8;
  border-radius:20px;padding:5px 14px;
  font-size:11px;font-weight:600;color:#8b5a1a;margin-bottom:18px;
}
.hero-tag-dot{
  width:7px;height:7px;border-radius:50%;background:#c28b3c;
}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(0.7);}}
.hero-tag-dot{animation:pulse 1.8s ease-in-out infinite;}

.hero-title{
  font-family:'Playfair Display',serif;
  font-size:42px;font-weight:900;color:#2d1a08;
  line-height:1.1;margin-bottom:14px;
}
.hero-title .accent{color:#c28b3c;}
.hero-title .dark{color:#8b5a1a;}
.hero-desc{font-size:14px;color:#a08060;line-height:1.75;margin-bottom:28px;max-width:380px;}
.hero-btns{display:flex;align-items:center;gap:12px;margin-bottom:32px;}

/* Primary button with ripple */
.btn-primary{
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;border-radius:12px;
  padding:13px 26px;font-size:14px;font-weight:700;
  cursor:pointer;position:relative;overflow:hidden;
  box-shadow:0 6px 20px rgba(139,90,26,0.35);
  transition:transform 0.2s, box-shadow 0.2s;
  font-family:inherit;
}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(139,90,26,0.45);}
.btn-primary::after{
  content:'';position:absolute;border-radius:50%;
  background:rgba(255,255,255,0.3);
  width:120px;height:120px;top:50%;left:50%;
  transform:translate(-50%,-50%) scale(0);opacity:1;
  transition:transform 0.5s ease, opacity 0.5s ease;
  pointer-events:none;
}
.btn-primary:active::after{transform:translate(-50%,-50%) scale(3);opacity:0;transition:0s;}

/* Outline button with fill transition */
.btn-outline{
  background:rgba(255,255,255,0.75);color:#8b5a1a;
  border:1.5px solid #c28b3c;border-radius:12px;
  padding:12px 22px;font-size:14px;font-weight:600;
  cursor:pointer;
  transition:background 0.25s, color 0.25s, transform 0.2s;
  font-family:inherit;
  backdrop-filter:blur(4px);
}
.btn-outline:hover{background:#8b5a1a;color:white;transform:translateY(-3px);}

/* Stats */
.hero-stats{display:flex;align-items:center;gap:0;}
.hstat{text-align:center;padding:0 20px;}
.hstat:first-child{padding-left:0;}
.hstat-num{font-size:26px;font-weight:800;color:#2d1a08;line-height:1;}
.hstat-label{font-size:11px;color:#a08060;margin-top:4px;font-weight:500;}
.hstat-div{width:1px;height:36px;background:#e8ddd0;}

/* Hero Right — dark brown bg, centered food image */
.hero-right{
  position:relative;flex-shrink:0;
  width:340px;
  background:linear-gradient(145deg,#1c0f00,#3d2008,#5a3010);
  display:flex;align-items:center;justify-content:center;
  animation:fadeRight 0.7s ease 0.25s both;
  overflow:hidden;
}
.hero-right::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at 50% 55%, rgba(194,139,60,0.22) 0%, transparent 65%);
  pointer-events:none;z-index:0;
}
.hero-right-inner{
  position:relative;z-index:1;
  display:flex;align-items:center;justify-content:center;
  width:100%;height:100%;
  padding:32px;
}
@keyframes fadeRight{from{opacity:0;transform:translateX(24px);}to{opacity:1;transform:translateX(0);}}

.food-circle{
  width:240px;height:240px;border-radius:50%;
  background:linear-gradient(135deg,#1c0f00,#5a3010);
  display:flex;align-items:center;justify-content:center;
  position:relative;
  box-shadow:0 20px 50px rgba(0,0,0,0.5), 0 0 0 6px rgba(194,139,60,0.15);
  animation:float-main 3.5s ease-in-out infinite;
}
@keyframes float-main{
  0%,100%{transform:translateY(0px) rotate(0deg);}
  50%{transform:translateY(-10px) rotate(1deg);}
}
.food-circle::before{
  content:'';position:absolute;inset:-10px;border-radius:50%;
  border:2px dashed rgba(194,139,60,0.3);
  animation:spin 22s linear infinite;
}
@keyframes spin{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
.food-circle::after{
  content:'';position:absolute;bottom:-24px;left:50%;
  transform:translateX(-50%);
  width:160px;height:24px;
  background:radial-gradient(ellipse,rgba(139,90,26,0.25) 0%,transparent 70%);
  border-radius:50%;
  animation:shadow-float 3.5s ease-in-out infinite;
}
@keyframes shadow-float{
  0%,100%{transform:translateX(-50%) scaleX(1);opacity:0.6;}
  50%{transform:translateX(-50%) scaleX(0.75);opacity:0.25;}
}

.badge-hot{
  position:absolute;top:16px;right:16px;
  background:linear-gradient(135deg,#ef4444,#f97316);
  color:white;font-size:10px;font-weight:800;
  padding:8px 14px;border-radius:12px;
  box-shadow:0 4px 14px rgba(239,68,68,0.45);
  z-index:2;
  animation:badge-pop 0.5s cubic-bezier(0.34,1.56,0.64,1) 1s both;
}
@keyframes badge-pop{from{opacity:0;transform:scale(0.5);}to{opacity:1;transform:scale(1);}}
.badge-hot span{display:block;font-size:18px;font-weight:900;}

.float-card{
  position:absolute;background:white;border-radius:14px;
  padding:10px 14px;box-shadow:0 8px 28px rgba(0,0,0,0.2);
  display:flex;align-items:center;gap:8px;z-index:2;
}
.float-card.left{
  bottom:24px;left:16px;
  animation:badge-pop 0.5s cubic-bezier(0.34,1.56,0.64,1) 1.2s both, float-card-l 3.5s ease-in-out 1.7s infinite;
}
.float-card.top{
  top:24px;left:16px;
  animation:badge-pop 0.5s cubic-bezier(0.34,1.56,0.64,1) 1.4s both, float-card-t 3.5s ease-in-out 1.9s infinite;
}
@keyframes float-card-l{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
@keyframes float-card-t{0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);}}
.fc-icon{font-size:20px;}
.fc-b{font-size:12px;font-weight:700;color:#2d1a08;display:block;}
.fc-s{font-size:10px;color:#a08060;}
/* Pulsing dot on fast order badge */
.fc-dot{
  display:inline-block;width:7px;height:7px;border-radius:50%;
  background:#22c55e;margin-right:4px;
  animation:pulse 1.5s ease-in-out infinite;
}

/* ========== FEATURES ========== */
.feats{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:14px;margin-bottom:28px;
}
.feat{
  background:white;border-radius:18px;padding:20px 18px;
  border:1.5px solid #ede0cc;text-align:center;
  animation:fadeUp 0.5s ease both;
  cursor:pointer;
  transition:transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
  position:relative;overflow:hidden;
}
.feat::before{
  content:'';position:absolute;inset:0;border-radius:inherit;
  background:linear-gradient(135deg,#fff8ee,rgba(250,246,240,0));
  opacity:0;transition:opacity 0.25s;
}
.feat:hover::before{opacity:1;}
.feat:nth-child(1){animation-delay:0.4s;}
.feat:nth-child(2){animation-delay:0.52s;}
.feat:nth-child(3){animation-delay:0.64s;}
.feat:nth-child(4){animation-delay:0.76s;}
.feat:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(139,90,26,0.12);border-color:#c28b3c;}
.feat-ico{
  font-size:32px;margin-bottom:12px;display:block;
  transition:transform 0.25s;
}
.feat:hover .feat-ico{transform:scale(1.15) rotate(-4deg);}
.feat-name{font-size:13px;font-weight:700;color:#2d1a08;margin-bottom:6px;position:relative;}
.feat-desc{font-size:11px;color:#a08060;line-height:1.65;position:relative;}

/* ========== SECTION HEAD ========== */
.section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.section-title{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:#2d1a08;}
.section-title span{color:#c28b3c;}
.see-all{
  font-size:12px;color:#8b5a1a;font-weight:600;cursor:pointer;
  text-decoration:none;padding:5px 12px;border-radius:8px;
  border:1px solid #e0c99a;transition:background 0.2s, color 0.2s;
}
.see-all:hover{background:#8b5a1a;color:white;border-color:#8b5a1a;}

/* ========== MENU GRID ========== */
.menu-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;}

/* Skeleton loading cards */
.skeleton-card{
  background:white;border-radius:16px;overflow:hidden;
  border:1px solid #ede0cc;
}
.skeleton-img{
  height:100px;
  background:linear-gradient(90deg,#f0e8dc 25%,#faf3e8 50%,#f0e8dc 75%);
  background-size:400% 100%;
  animation:shimmer 1.4s ease-in-out infinite;
}
.skeleton-body{padding:12px 14px;display:flex;flex-direction:column;gap:8px;}
.skeleton-line{
  height:12px;border-radius:6px;
  background:linear-gradient(90deg,#f0e8dc 25%,#faf3e8 50%,#f0e8dc 75%);
  background-size:400% 100%;
  animation:shimmer 1.4s ease-in-out infinite;
}
.skeleton-line.short{width:60%;}
.skeleton-btn{
  height:30px;border-radius:8px;margin-top:4px;
  background:linear-gradient(90deg,#f0e8dc 25%,#faf3e8 50%,#f0e8dc 75%);
  background-size:400% 100%;
  animation:shimmer 1.4s ease-in-out infinite;
}
@keyframes shimmer{
  0%{background-position:100% 0;}
  100%{background-position:-100% 0;}
}

/* Real menu cards */
.mc{
  background:white;border-radius:16px;overflow:hidden;
  border:1.5px solid #ede0cc;cursor:pointer;
  transition:transform 0.25s cubic-bezier(0.34,1.3,0.64,1), box-shadow 0.25s ease, border-color 0.25s ease;
  animation:fadeUp 0.5s ease both;
}
.mc:nth-child(1){animation-delay:0.6s;}
.mc:nth-child(2){animation-delay:0.72s;}
.mc:nth-child(3){animation-delay:0.84s;}
.mc:nth-child(4){animation-delay:0.96s;}
.mc:hover{
  transform:translateY(-6px) scale(1.02);
  box-shadow:0 16px 36px rgba(139,90,26,0.2);
  border-color:#c28b3c;
}
.mc-img{
  height:110px;display:flex;align-items:center;
  justify-content:center;font-size:48px;
  background:#fff8ee;
  transition:background 0.25s;
  position:relative;overflow:hidden;
}
.mc:hover .mc-img{background:#fff2e0;}
.mc-img img{transition:transform 0.35s ease;}
.mc:hover .mc-img img{transform:scale(1.08);}
.mc-body{padding:12px 14px;}
.mc-name{font-size:13px;font-weight:700;color:#2d1a08;margin-bottom:3px;transition:color 0.2s;}
.mc:hover .mc-name{color:#8b5a1a;}
.mc-price{font-size:14px;color:#c28b3c;font-weight:800;margin-bottom:10px;}
.mc-btn{
  width:100%;background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;border-radius:8px;
  padding:8px;font-size:12px;font-weight:600;cursor:pointer;
  position:relative;overflow:hidden;
  transition:opacity 0.15s, transform 0.15s;
  font-family:inherit;
}
.mc-btn:hover{opacity:0.88;}
.mc-btn:active{transform:scale(0.96);}
.mc-btn::after{
  content:'';position:absolute;border-radius:50%;
  background:rgba(255,255,255,0.3);
  width:80px;height:80px;top:50%;left:50%;
  transform:translate(-50%,-50%) scale(0);opacity:1;
  pointer-events:none;
}
.mc-btn:active::after{
  animation:ripple-btn 0.4s ease;
}
@keyframes ripple-btn{to{transform:translate(-50%,-50%) scale(4);opacity:0;}}

/* ========== CTA ========== */
.cta{
  background:linear-gradient(135deg,#1c0f00,#3d2000);
  border-radius:22px;padding:28px 36px;
  display:flex;align-items:center;justify-content:space-between;
  animation:fadeUp 0.6s ease 1s both;
  position:relative;overflow:hidden;
  border:1px solid rgba(194,139,60,0.2);
}
.cta::before{
  content:'';position:absolute;right:-60px;top:-60px;
  width:240px;height:240px;
  background:radial-gradient(circle,rgba(194,139,60,0.2),transparent 70%);
  border-radius:50%;
}
.cta::after{
  content:'';position:absolute;left:200px;bottom:-40px;
  width:140px;height:140px;
  background:radial-gradient(circle,rgba(194,139,60,0.1),transparent 70%);
  border-radius:50%;
}
.cta-left{position:relative;z-index:1;}
.cta-title{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:white;margin-bottom:6px;}
.cta-sub{font-size:12px;color:rgba(255,240,200,0.5);line-height:1.6;}
.cta-btn{
  background:linear-gradient(135deg,#c28b3c,#f5d080);
  color:#2d1a08;border:none;border-radius:12px;
  padding:13px 26px;font-size:13px;font-weight:700;
  cursor:pointer;z-index:1;position:relative;white-space:nowrap;
  transition:transform 0.2s, box-shadow 0.2s;
  font-family:inherit;
  box-shadow:0 6px 20px rgba(194,139,60,0.35);
}
.cta-btn:hover{transform:scale(1.05) translateY(-2px);box-shadow:0 12px 28px rgba(194,139,60,0.45);}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">🍜 Seoullicious</div>
  <div class="sidebar-logo-sub">Korean Food Experience</div>

  <div class="nav-label">Main Menu</div>
  <a href="home.php" class="nav-item active"><i class="fas fa-house"></i> Home</a>
  <a href="pos.php" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
  <a href="history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> History</a>
  <a href="review_buka.php" class="nav-item"><i class="fas fa-star"></i> Review</a>

  <div class="sidebar-bottom">
    <div class="user-info">
      <div class="user-av"><?= strtoupper(substr($username,0,1)) ?></div>
      <div>
        <div class="user-av-name"><?= ucfirst($username) ?></div>
        <div class="user-av-role">Cashier</div>
      </div>
    </div>
    <a href="../auth/logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> Logout</a>
  </div>
</div>

<!-- MAIN -->
<div class="main" id="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" id="toggleSidebar" title="Toggle sidebar">☰</button>
      <div class="greeting">
        <h2><?= $greeting ?>, <?= ucfirst($username) ?>!</h2>
        <p><?= date('l, d F Y') ?></p>
      </div>
    </div>
    <div class="topbar-right">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search menu..." id="searchInput">
      </div>
      <a href="pos.php"><button class="order-btn">+ New Order</button></a>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- HERO -->
    <div class="hero">
      <div class="hero-left">
        <div class="hero-tag">
          <div class="hero-tag-dot"></div> Korean Food Experience
        </div>
        <div class="hero-title">
          Taste the<br>
          <span class="accent">Authentic</span><br>
          <span class="dark">Korean Flavors</span>
        </div>
        <div class="hero-desc">
          Enjoy the taste of real Korean cuisine — from spicy tteokbokki to warm ramen that keeps you coming back for more.
        </div>
        <div class="hero-btns">
          <a href="pos.php"><button class="btn-primary">View Menu →</button></a>
          <a href="history.php"><button class="btn-outline">Order History</button></a>
        </div>
        <!-- Stats with count-up animation -->
        <div class="hero-stats">
          <div class="hstat">
            <div class="hstat-num" data-target="20" data-suffix="+">0</div>
            <div class="hstat-label">Menu Items</div>
          </div>
          <div class="hstat-div"></div>
          <div class="hstat">
            <div class="hstat-num" data-target="500" data-suffix="+">0</div>
            <div class="hstat-label">Happy Customers</div>
          </div>
          <div class="hstat-div"></div>
          <div class="hstat">
            <div class="hstat-num" data-target="4.9" data-suffix="" data-decimal="1">0</div>
            <div class="hstat-label">Rating</div>
          </div>
        </div>
      </div>

      <div class="hero-right">
        <div class="badge-hot"><span>🔥</span>#1 Today</div>
        <div class="float-card left">
          <div class="fc-icon">⭐</div>
          <div><span class="fc-b">Best Seller</span><span class="fc-s">Tteokbokki</span></div>
        </div>
        <div class="float-card top">
          <div class="fc-icon">🕐</div>
          <div>
            <span class="fc-b"><span class="fc-dot"></span>Fast Order</span>
            <span class="fc-s">Ready in 5 min</span>
          </div>
        </div>
        <div class="hero-right-inner">
          <div class="food-circle">
            <img src="../assets/tteokbokki.avif"
                 style="width:185px;height:185px;object-fit:cover;border-radius:50%;"
                 onerror="this.style.display='none'">
          </div>
        </div>
      </div>
    </div>

    <!-- FEATURES -->
    <div class="feats">
      <div class="feat">
        <span class="feat-ico">🍱</span>
        <div class="feat-name">Authentic Menu</div>
        <div class="feat-desc">Original Korean recipes loved by hundreds of loyal customers</div>
      </div>
      <div class="feat">
        <span class="feat-ico">⚡</span>
        <div class="feat-name">Easy Ordering</div>
        <div class="feat-desc">Order directly from your table — fast, simple, and hassle-free</div>
      </div>
      <div class="feat">
        <span class="feat-ico">🤖</span>
        <div class="feat-name">AI Assistant</div>
        <div class="feat-desc">Lici is ready to recommend the perfect menu for your mood</div>
      </div>
      <div class="feat">
        <span class="feat-ico">📊</span>
        <div class="feat-name">Track Orders</div>
        <div class="feat-desc">Monitor your order history anytime with ease</div>
      </div>
    </div>


    <!-- CTA -->
    <div class="cta">
      <div class="cta-left">
        <div class="cta-title">Ready to experience Korean Food? 🍜</div>
        <div class="cta-sub">Start ordering now — fast, easy, and delicious!</div>
      </div>
      <a href="pos.php"><button class="cta-btn">Start Ordering Now →</button></a>
    </div>

  </div><!-- /content -->
</div><!-- /main -->

<!-- PHP menu data as JSON for JS rendering (avoids skeleton+PHP mix) -->
<script>
const MENU_DATA = <?= json_encode($top_menus) ?>;
</script>

<script>
/* ---- Sidebar toggle ---- */
document.getElementById("toggleSidebar").onclick = function(){
  document.getElementById("sidebar").classList.toggle("hide");
};

/* ---- Count-up animation for stats ---- */
function animateCount(el) {
  const target  = parseFloat(el.dataset.target);
  const suffix  = el.dataset.suffix  || '';
  const decimal = parseInt(el.dataset.decimal) || 0;
  const duration = 1400;
  const start   = performance.now();

  function step(now) {
    const elapsed = Math.min((now - start) / duration, 1);
    // Ease out cubic
    const ease = 1 - Math.pow(1 - elapsed, 3);
    const value = ease * target;
    el.textContent = decimal > 0
      ? value.toFixed(decimal) + suffix
      : Math.floor(value) + suffix;
    if (elapsed < 1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
}

// Trigger count-up when stats are visible
const statNums = document.querySelectorAll('.hstat-num[data-target]');
const statsObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if(e.isIntersecting && !e.target.dataset.animated) {
      e.target.dataset.animated = '1';
      animateCount(e.target);
    }
  });
}, { threshold: 0.5 });
statNums.forEach(el => statsObserver.observe(el));

/* ---- Render real menu cards after short skeleton delay ---- */
function renderMenuCards() {
  const grid = document.getElementById('menuGrid');
  if (!MENU_DATA || MENU_DATA.length === 0) return;

  const html = MENU_DATA.map((item, i) => {
    const nama   = item.nama_menu || item.nama || 'Menu';
    const harga  = parseInt(item.harga) || 0;
    const gambar = item.gambar || '';
    const delay  = (0.6 + i * 0.12).toFixed(2);

    const imgHTML = gambar
      ? `<img src="../assets/${escHtml(gambar)}"
              style="width:86px;height:86px;object-fit:cover;border-radius:12px;"
              onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
         <span style="display:none;font-size:48px;">🍽️</span>`
      : `<span>🍽️</span>`;

    return `
      <div class="mc" style="animation-delay:${delay}s">
        <div class="mc-img" style="background:#fff8ee;">${imgHTML}</div>
        <div class="mc-body">
          <div class="mc-name">${escHtml(nama)}</div>
          <div class="mc-price">Rp ${formatRp(harga)}</div>
          <a href="pos.php"><button class="mc-btn">+ Add to Order</button></a>
        </div>
      </div>`;
  }).join('');

  // Small delay so skeleton is visible briefly
  setTimeout(() => {
    grid.innerHTML = html;
  }, 600);
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatRp(n) {
  return n.toLocaleString('id-ID');
}

renderMenuCards();

/* ---- Search filter ---- */
document.getElementById('searchInput').addEventListener('input', function() {
  const q = this.value.toLowerCase().trim();
  document.querySelectorAll('.mc').forEach(card => {
    const name = card.querySelector('.mc-name')?.textContent.toLowerCase() || '';
    card.style.display = name.includes(q) ? '' : 'none';
  });
});
</script>
</body>
</html>
