<?php
session_start();
include "../config/koneksi.php";
$query = mysqli_query($koneksi,"SELECT * FROM menu");

$total_item = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $c){ $total_item += $c['qty']; }
}

$top_query = mysqli_query($koneksi,"
    SELECT m.*, COUNT(oi.id_item) as total_order 
    FROM menu m
    JOIN order_items oi ON m.id_menu = oi.id_menu
    GROUP BY m.id_menu ORDER BY total_order DESC LIMIT 4
");
$top_menus = [];
while($row = mysqli_fetch_assoc($top_query)) { $top_menus[] = $row; }

$jam = date('H');
if($jam >= 5 && $jam < 12) $greeting = "Selamat Pagi";
elseif($jam >= 12 && $jam < 17) $greeting = "Selamat Siang";
elseif($jam >= 17 && $jam < 21) $greeting = "Selamat Sore";
else $greeting = "Selamat Malam";

$username = $_SESSION['username'] ?? 'Kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Seoullicious — Kasir</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --ink:#1a1008;--paper:#f7f2ea;--cream:#efe8d8;
  --gold:#b8893a;--gold-light:#e8c97a;--muted:#8a7660;
  --border:#e2d9c8;--dark:#1a1008;--sidebar-w:200px;
}
body{font-family:'DM Sans',sans-serif;background:var(--paper);display:flex;height:100vh;overflow:hidden}

/* SIDEBAR */
.sidebar{width:var(--sidebar-w);min-width:var(--sidebar-w);background:var(--dark);display:flex;flex-direction:column;height:100vh;transition:margin .3s;position:relative;z-index:10;border-right:1px solid rgba(184,137,58,0.1)}
.sidebar.hide{margin-left:calc(-1 * var(--sidebar-w))}
.sidebar-brand{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,0.06)}
.sidebar-brand-name{font-family:'DM Serif Display',serif;color:#e8c97a;font-size:16px;display:block}
.sidebar-brand-sub{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.25);margin-top:3px}
.sidebar-nav{padding:12px 10px;flex:1}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:rgba(255,255,255,0.4);font-size:13px;margin-bottom:2px;transition:all .15s}
.nav-item i{width:15px;text-align:center;font-size:12px}
.nav-item:hover{background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.8)}
.nav-item.active{background:rgba(184,137,58,0.15);color:#e8c97a}
.sidebar-bottom{padding:10px 10px 20px;border-top:1px solid rgba(255,255,255,0.06)}

/* MAIN */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0}

/* TOPBAR */
.topbar{background:#fff;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);flex-shrink:0}
.topbar-left{display:flex;align-items:center;gap:14px}
.toggle-btn{background:var(--cream);border:none;width:34px;height:34px;border-radius:8px;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;color:var(--ink);transition:background .15s}
.toggle-btn:hover{background:var(--border)}
.greeting-name{font-size:16px;font-weight:600;color:var(--ink)}
.greeting-date{font-size:11px;color:var(--muted);margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:12px}
.search-wrap{display:flex;align-items:center;gap:8px;background:var(--paper);border:1px solid var(--border);border-radius:8px;padding:7px 14px}
.search-wrap i{color:var(--muted);font-size:12px}
.search-wrap input{border:none;background:transparent;outline:none;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--ink);width:180px}
.search-wrap input::placeholder{color:#c4b89a}
.user-chip{display:flex;align-items:center;gap:8px;padding:6px 12px;background:var(--paper);border:1px solid var(--border);border-radius:8px}
.user-avatar{width:26px;height:26px;border-radius:6px;background:var(--ink);color:var(--gold-light);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;font-family:'DM Serif Display',serif}
.user-chip span{font-size:13px;font-weight:500;color:var(--ink)}

/* CATEGORY TABS */
.cat-bar{background:#fff;padding:0 24px;display:flex;border-bottom:1px solid var(--border);flex-shrink:0;overflow-x:auto}
.cat-tab{padding:12px 20px;border:none;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;color:var(--muted);border-bottom:2px solid transparent;transition:all .15s;white-space:nowrap;margin-bottom:-1px}
.cat-tab:hover{color:var(--ink)}
.cat-tab.active{color:var(--gold);border-bottom-color:var(--gold);font-weight:600}

/* MENU AREA */
.menu-area{flex:1;overflow-y:auto;padding:24px}

/* POPULAR */
.popular-section{margin-bottom:28px}
.popular-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.popular-label{display:flex;align-items:center;gap:10px}
.popular-label-text{font-family:'DM Serif Display',serif;font-size:18px;color:var(--ink)}
.popular-badge{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--gold);font-weight:600;background:rgba(184,137,58,0.1);padding:3px 9px;border-radius:4px}
.popular-count{font-size:12px;color:var(--muted)}
.popular-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.popular-card{background:var(--ink);border-radius:12px;overflow:hidden;position:relative;cursor:pointer;transition:transform .2s,box-shadow .2s}
.popular-card:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(26,16,8,0.25)}
.popular-card-img{width:100%;height:110px;object-fit:cover;display:block;opacity:.85;transition:opacity .2s}
.popular-card:hover .popular-card-img{opacity:1}
.popular-card-rank{position:absolute;top:10px;left:10px;font-size:18px;line-height:1}
.popular-card-body{padding:12px 14px}
.popular-card-name{font-weight:600;font-size:13px;color:rgba(255,255,255,0.9);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.popular-card-orders{font-size:11px;color:rgba(232,201,122,0.6);margin-bottom:10px}
.popular-card-footer{display:flex;align-items:center;justify-content:space-between}
.popular-card-price{font-size:13px;font-weight:600;color:var(--gold-light)}
.popular-add{background:rgba(184,137,58,0.25);border:1px solid rgba(184,137,58,0.4);color:var(--gold-light);font-size:11px;font-weight:600;padding:4px 10px;border-radius:5px;cursor:pointer;transition:background .15s;font-family:'DM Sans',sans-serif}
.popular-add:hover{background:rgba(184,137,58,0.45)}

/* ALL MENU */
.section-label{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);font-weight:600;margin-bottom:14px;display:flex;align-items:center;gap:12px}
.section-label::after{content:'';flex:1;height:1px;background:var(--border)}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px}
.menu-card{background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden;cursor:pointer;transition:transform .2s,box-shadow .2s}
.menu-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(26,16,8,0.08)}
.menu-card img{width:100%;height:100px;object-fit:cover;display:block;background:var(--cream)}
.menu-card-body{padding:10px 12px}
.menu-card-name{font-size:13px;font-weight:600;color:var(--ink);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.menu-card-price{font-size:12px;color:var(--gold);font-weight:600;margin-bottom:10px}
.add-btn{width:100%;padding:7px;background:var(--ink);color:var(--gold-light);border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s}
.add-btn:hover{background:#2d1e0e}
.qty-box{display:flex;align-items:center;justify-content:space-between;background:var(--cream);border-radius:6px;padding:4px 6px}
.qty-btn{background:none;border:none;width:22px;height:22px;border-radius:5px;cursor:pointer;font-size:15px;color:var(--gold);display:flex;align-items:center;justify-content:center;transition:background .15s}
.qty-btn:hover{background:rgba(184,137,58,0.15)}
.qty-num{font-size:13px;font-weight:700;color:var(--ink);min-width:20px;text-align:center}

/* FLOATING CART */
#cart-float-btn{position:fixed;bottom:24px;right:24px;background:var(--ink);color:var(--gold-light);border:none;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:10px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;box-shadow:0 8px 28px rgba(26,16,8,0.35);transition:transform .2s;z-index:500}
#cart-float-btn:hover{transform:translateY(-2px)}
#cart-float-btn.hidden{display:none}
.cart-badge{background:var(--gold);color:var(--ink);width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}

/* CHECKOUT MODAL */
#checkout-modal-overlay{display:none;position:fixed;inset:0;background:rgba(26,16,8,0.5);backdrop-filter:blur(3px);z-index:1000;align-items:center;justify-content:center}
#checkout-modal-overlay.open{display:flex}
#checkout-modal{background:#fff;border-radius:16px;width:420px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,0.3);overflow:hidden}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--paper)}
.modal-title{font-family:'DM Serif Display',serif;font-size:20px;color:var(--ink)}
.modal-count{font-size:12px;color:var(--muted);margin-top:2px}
.modal-close{background:var(--cream);border:none;width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:14px;color:var(--muted);display:flex;align-items:center;justify-content:center}
.modal-close:hover{background:var(--border);color:var(--ink)}
.modal-items{flex:1;overflow-y:auto;padding:16px 24px}
.order-item{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f0ebe0}
.order-item:last-child{border-bottom:none}
.order-item img{width:46px;height:46px;border-radius:8px;object-fit:cover;background:var(--cream)}
.order-item-info{flex:1;min-width:0}
.order-item-name{font-size:13px;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.order-item-sub{font-size:12px;color:var(--gold);font-weight:600;margin-top:2px}
.order-item-qty{display:flex;align-items:center;gap:8px}
.oqty-btn{background:var(--cream);border:none;width:26px;height:26px;border-radius:6px;cursor:pointer;font-size:14px;color:var(--gold);display:flex;align-items:center;justify-content:center}
.oqty-btn:hover{background:var(--border)}
.empty-cart{text-align:center;padding:48px 20px;color:var(--muted)}
.empty-cart i{font-size:32px;margin-bottom:10px;display:block;opacity:.4}
.empty-cart p{font-size:13px}
.modal-footer{padding:18px 24px;border-top:1px solid var(--border);background:var(--paper)}
.total-row{display:flex;justify-content:space-between;align-items:center;padding:4px 0}
.total-row span{font-size:13px;color:var(--muted)}
.total-row b{font-size:13px;color:var(--ink);font-weight:600}
.total-row.grand{padding:10px 0 0;margin-top:6px;border-top:1px solid var(--border)}
.total-row.grand span{font-size:15px;color:var(--ink);font-weight:600}
.total-row.grand b{font-size:18px;color:var(--ink);font-weight:700}
.checkout-btn{width:100%;padding:14px;margin-top:14px;background:var(--ink);color:var(--gold-light);border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s,transform .15s}
.checkout-btn:hover{background:#2d1e0e;transform:translateY(-1px)}

/* LICI */
#lici-button{position:fixed;bottom:24px;left:216px;width:44px;height:44px;background:var(--ink);color:var(--gold-light);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:2000;font-size:17px;box-shadow:0 4px 14px rgba(0,0,0,0.25);transition:left .3s}
#lici-chat{position:fixed;bottom:78px;left:216px;width:320px;height:450px;background:#fff;border-radius:14px;display:none;flex-direction:column;box-shadow:0 16px 48px rgba(0,0,0,0.18);z-index:2000;border:1px solid var(--border);transition:left .3s;overflow:hidden}
.lici-header{background:var(--ink);color:var(--gold-light);padding:14px 16px;display:flex;justify-content:space-between;align-items:center}
.lici-header-title{font-size:14px;font-weight:600}
.lici-close{cursor:pointer;opacity:.6;font-size:13px}
.lici-close:hover{opacity:1}
#lici-messages{flex:1;padding:14px;overflow-y:auto;background:var(--paper)}
.bubble{padding:9px 13px;border-radius:10px;margin-bottom:8px;max-width:82%;font-size:13px;line-height:1.5}
.user{background:var(--ink);color:var(--gold-light);margin-left:auto}
.ai{background:#fff;border:1px solid var(--border);color:var(--ink)}
.lici-suggest{padding:8px 12px;display:flex;gap:6px;flex-wrap:wrap;border-top:1px solid var(--border);background:#fff}
.lici-suggest button{background:var(--cream);border:1px solid var(--border);padding:5px 10px;border-radius:6px;cursor:pointer;font-size:11px;color:var(--gold);font-family:'DM Sans',sans-serif;transition:background .15s}
.lici-suggest button:hover{background:var(--border)}
.lici-input{display:flex;border-top:1px solid var(--border);background:#fff}
.lici-input input{flex:1;padding:11px 14px;border:none;outline:none;font-size:13px;background:transparent;font-family:'DM Sans',sans-serif;color:var(--ink)}
.lici-input button{background:var(--ink);color:var(--gold-light);border:none;padding:0 12px;cursor:pointer;font-size:13px}
.lici-input button:last-child{border-radius:0 0 14px 0}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <span class="sidebar-brand-name">Seoullicious</span>
    <span class="sidebar-brand-sub">Kasir</span>
  </div>
  <div class="sidebar-nav">
    <a href="home.php" class="nav-item active"><i class="fas fa-utensils"></i> Menu</a>
    <a href="history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> Riwayat</a>
    <a href="review.php" class="nav-item"><i class="fas fa-star"></i> Ulasan</a>
  </div>
  <div class="sidebar-bottom">
    <a href="../auth/logout.php" class="nav-item" style="color:rgba(255,100,80,0.5)">
      <i class="fas fa-right-from-bracket"></i> Keluar
    </a>
  </div>
</div>

<!-- MAIN -->
<div class="main" id="main">
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" id="toggleSidebar">☰</button>
      <div>
        <div class="greeting-name"><?= $greeting ?>, <?= ucfirst($username) ?></div>
        <div class="greeting-date"><?= date('l, d F Y') ?></div>
      </div>
    </div>
    <div class="topbar-right">
      <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" id="searchMenu" placeholder="Cari menu...">
      </div>
      <div class="user-chip">
        <div class="user-avatar"><?= strtoupper(substr($username,0,1)) ?></div>
        <span><?= ucfirst($username) ?></span>
      </div>
    </div>
  </div>

  <div class="cat-bar">
    <button class="cat-tab active" onclick="filterCat('all',this)">Semua</button>
    <button class="cat-tab" onclick="filterCat('food',this)">Makanan</button>
    <button class="cat-tab" onclick="filterCat('drink',this)">Minuman</button>
    <button class="cat-tab" onclick="filterCat('snack',this)">Snack</button>
  </div>

  <div class="menu-area">
    <?php if(!empty($top_menus)): ?>
    <div class="popular-section">
      <div class="popular-header">
        <div class="popular-label">
          <span class="popular-label-text">Paling Populer</span>
          <span class="popular-badge">Best Seller</span>
        </div>
        <span class="popular-count"><?= count($top_menus) ?> menu</span>
      </div>
      <div class="popular-grid">
        <?php foreach($top_menus as $i => $item):
          $nama   = $item['nama_menu'] ?? 'Menu';
          $harga  = $item['harga'] ?? 0;
          $gambar = $item['gambar'] ?? '';
          $id     = $item['id_menu'] ?? 0;
          $total  = $item['total_order'] ?? 0;
          $rankEmoji = ['🥇','🥈','🥉','⭐'][$i] ?? '⭐';
        ?>
        <div class="popular-card">
          <?php if(!empty($gambar)): ?>
          <img class="popular-card-img" src="../assets/<?= htmlspecialchars($gambar) ?>"
               onerror="this.style.display='none'" alt="<?= htmlspecialchars($nama) ?>">
          <?php endif; ?>
          <div class="popular-card-rank"><?= $rankEmoji ?></div>
          <div class="popular-card-body">
            <div class="popular-card-name"><?= htmlspecialchars($nama) ?></div>
            <div class="popular-card-orders"><i class="fas fa-fire" style="font-size:9px"></i> <?= $total ?> order</div>
            <div class="popular-card-footer">
              <span class="popular-card-price">Rp <?= number_format($harga,0,',','.') ?></span>
              <button class="popular-add" onclick="addItem(<?= $id ?>,'<?= addslashes($nama) ?>',<?= $harga ?>,'<?= $gambar ?>')">+ Add</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="section-label">Semua Menu</div>
    <div class="menu-grid" id="menuGrid">
      <?php mysqli_data_seek($query,0); while($data = mysqli_fetch_array($query)): ?>
      <div class="menu-card" data-name="<?= strtolower($data['nama_menu']) ?>" data-cat="<?= strtolower($data['kategori']) ?>">
        <img src="../assets/<?= $data['gambar'] ?>" onerror="this.src='../assets/default.png'" alt="<?= htmlspecialchars($data['nama_menu']) ?>">
        <div class="menu-card-body">
          <div class="menu-card-name"><?= htmlspecialchars($data['nama_menu']) ?></div>
          <div class="menu-card-price">Rp <?= number_format($data['harga'],0,',','.') ?></div>
          <div id="box-<?= $data['id_menu'] ?>">
            <button class="add-btn" onclick="addItem(<?= $data['id_menu'] ?>,'<?= addslashes($data['nama_menu']) ?>',<?= $data['harga'] ?>,'<?= $data['gambar'] ?>')">+ Tambah</button>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<!-- FLOATING CART -->
<button id="cart-float-btn" class="hidden" onclick="openCheckoutModal()">
  <i class="fas fa-shopping-basket"></i>
  <span id="cart-float-label">0 item</span>
  <div class="cart-badge" id="cart-float-badge">0</div>
</button>

<!-- MODAL CHECKOUT -->
<div id="checkout-modal-overlay" onclick="handleOverlayClick(event)">
  <div id="checkout-modal">
    <div class="modal-header">
      <div>
        <div class="modal-title">Pesanan Saya</div>
        <div class="modal-count" id="order-count">0 item</div>
      </div>
      <button class="modal-close" onclick="closeCheckoutModal()">✕</button>
    </div>
    <div class="modal-items" id="orderItems">
      <div class="empty-cart"><i class="fas fa-bowl-food"></i><p>Belum ada pesanan</p></div>
    </div>
    <div class="modal-footer">
      <div class="total-row"><span>Subtotal</span><b id="subtotal">Rp 0</b></div>
      <div class="total-row"><span>Pajak 10%</span><b id="tax">Rp 0</b></div>
      <div class="total-row grand"><span>Total</span><b id="grandtotal">Rp 0</b></div>
      <button class="checkout-btn" onclick="checkout()">Lanjut ke Pembayaran →</button>
    </div>
  </div>
</div>

<!-- LICI AI -->
<div id="lici-button" title="Lici AI">🤖</div>
<div id="lici-chat">
  <div class="lici-header">
    <span class="lici-header-title">🤖 Lici AI</span>
    <span class="lici-close" onclick="closeLici()">✕</span>
  </div>
  <div id="lici-messages"></div>
  <div class="lici-suggest">
    <button onclick="quickAsk('rekomendasi')">Rekomendasi</button>
    <button onclick="quickAsk('murah')">Paling Murah</button>
    <button onclick="quickAsk('minuman')">Minuman</button>
    <button onclick="quickAsk('pedas')">Menu Pedas</button>
  </div>
  <div class="lici-input">
    <input id="lici-text" placeholder="Tanya seputar menu...">
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
  renderQty(id,cart[id].qty);renderOrderPanel();updateFloatBtn();
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
    if(box)box.innerHTML=`<button class="add-btn" onclick="addItem(${id},'${s.name}',${s.price},'${s.img}')">+ Tambah</button>`;
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
    html+=`<div class="order-item"><img src="../assets/${item.img}" onerror="this.src='../assets/default.png'"><div class="order-item-info"><div class="order-item-name">${item.name}</div><div class="order-item-sub">Rp ${sub.toLocaleString('id-ID')}</div></div><div class="order-item-qty"><button class="oqty-btn" onclick="changeQty(${item.id},-1)">−</button><span style="font-size:13px;font-weight:700;min-width:18px;text-align:center">${item.qty}</span><button class="oqty-btn" onclick="changeQty(${item.id},1)">+</button></div></div>`;
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
  const totalQty=Object.values(cart).reduce((s,i)=>s+i.qty,0);
  if(totalQty===0){btn.classList.add("hidden");return;}
  btn.classList.remove("hidden");
  document.getElementById("cart-float-label").innerText=totalQty+" item";
  document.getElementById("cart-float-badge").innerText=totalQty;
}
function openCheckoutModal(){document.getElementById("checkout-modal-overlay").classList.add("open")}
function closeCheckoutModal(){document.getElementById("checkout-modal-overlay").classList.remove("open")}
function handleOverlayClick(e){if(e.target===document.getElementById("checkout-modal-overlay"))closeCheckoutModal()}
function checkout(){
  if(!Object.keys(cart).length){alert("Keranjang masih kosong!");return;}
  window.location.href="cart.php";
}
document.getElementById("toggleSidebar").onclick=function(){
  const sb=document.getElementById("sidebar");sb.classList.toggle("hide");
  const h=sb.classList.contains("hide");
  document.getElementById("lici-button").style.left=h?"16px":"216px";
  document.getElementById("lici-chat").style.left=h?"16px":"216px";
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
function closeLici(){document.getElementById("lici-chat").style.display="none"}
function quickAsk(t){document.getElementById("lici-text").value=t;sendLici()}
function sendLici(){
  let text=document.getElementById("lici-text").value;if(!text.trim())return;
  let box=document.getElementById("lici-messages");
  box.innerHTML+=`<div class="bubble user">${text}</div>`;
  fetch("../ai/api.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"message="+encodeURIComponent(text)})
  .then(r=>r.json()).then(data=>{
    if(data.error){box.innerHTML+=`<div class="bubble ai">${data.error}</div>`;return}
    box.innerHTML+=`<div class="bubble ai">${data.response}</div>`;
    if(data.recommendations?.length){
      let c=document.createElement("div");c.style.cssText="display:flex;gap:8px;flex-wrap:wrap;padding:4px 0";
      data.recommendations.forEach(item=>{c.innerHTML+=`<div style="width:110px;background:#fff;border-radius:8px;padding:8px;text-align:center;border:1px solid #e2d9c8"><img src="../assets/${item.image}" style="width:60px;height:60px;object-fit:cover;border-radius:6px"><br><b style="font-size:11px;color:#1a1008">${item.name}</b><br><span style="font-size:10px;color:#b8893a;font-weight:600">Rp ${item.price.toLocaleString('id-ID')}</span><br><button onclick="addItem(${item.id},'${item.name}',${item.price},'${item.image}')" style="background:#1a1008;color:#e8c97a;border:none;padding:4px 8px;border-radius:5px;margin-top:5px;font-size:10px;cursor:pointer">+ Add</button></div>`});
      box.appendChild(c);
    }
    if(data.follow_up)box.innerHTML+=`<div class="bubble ai">${data.follow_up}</div>`;
    box.scrollTop=box.scrollHeight;
  });
  document.getElementById("lici-text").value="";
}
function startVoice(){
  if(!('webkitSpeechRecognition' in window)){alert("Browser tidak support voice");return}
  let r=new webkitSpeechRecognition();r.lang="id-ID";
  r.onresult=e=>{document.getElementById("lici-text").value=e.results[0][0].transcript;sendLici()};r.start();
}
</script>
</body>
</html>