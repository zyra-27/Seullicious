<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<title>Seoullicious — Order</title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+KR:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --red:       #c0392b;
  --red-light: #e8534a;
  --red-glow:  rgba(192,57,43,0.18);
  --gold:      #b79055;
  --gold-light:#d4a96a;
  --cream:     rgba(255,248,235,0.10);
  --cream2:    rgba(255,240,210,0.13);
  --dark:      #fff8f0;
  --dark2:     #f5debb;
  --muted:     rgba(255,230,190,0.65);
  --border:    rgba(183,144,85,0.30);
  --card-bg:   rgba(18,8,3,0.78);
  --shadow:    0 24px 60px rgba(0,0,0,0.6);
}

*{ margin:0; padding:0; box-sizing:border-box; }

body{
  font-family:'Plus Jakarta Sans', sans-serif;
  background: #1a0f07;
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* ── Background: foto makanan gelap & moody ── */
body::before{
  content:'';
  position:fixed; inset:0; z-index:0;
  background: url('../assets/bg-pos.jpg') center/cover no-repeat fixed;
  filter: brightness(0.22) saturate(1.4) sepia(0.2);
  pointer-events:none;
}

/* ── Vignette overlay ── */
body::after{
  content:'';
  position:fixed; inset:0; z-index:0;
  background:
    radial-gradient(ellipse at 20% 50%, rgba(192,57,43,0.08) 0%, transparent 60%),
    linear-gradient(180deg, rgba(18,8,3,0.5) 0%, rgba(18,8,3,0.2) 50%, rgba(18,8,3,0.6) 100%);
  pointer-events:none;
}

.wrapper{
  position:relative; z-index:1;
  max-width:1100px;
  margin:0 auto;
  padding:32px 24px 60px;
}

/* ── Header ── */
.page-header{
  display:flex; align-items:center; gap:16px;
  margin-bottom:32px;
  animation: fadeDown 0.5s ease both;
}
.header-icon{
  width:52px; height:52px; border-radius:16px;
  background:linear-gradient(135deg, var(--red), var(--red-light));
  display:flex; align-items:center; justify-content:center;
  font-size:22px; color:white;
  box-shadow:0 8px 24px var(--red-glow);
}
.header-text h1{
  font-family:'Noto Serif KR', serif;
  font-size:26px; font-weight:700; color:#fff8f0;
  letter-spacing:-0.3px;
}
.header-text p{
  font-size:13px; color:rgba(255,220,170,0.65); margin-top:2px;
}
.header-text p::before{
  content:'주문 • ';
  font-family:'Noto Serif KR', serif;
  color:var(--gold); font-size:12px;
}

/* Back button */
.back-btn{
  display:inline-flex; align-items:center; gap:8px;
  color:var(--muted); font-size:13px; font-weight:600;
  text-decoration:none; margin-bottom:20px;
  padding:8px 14px; border-radius:10px;
  border:1px solid var(--border);
  background:rgba(255,255,255,0.04);
  transition:all 0.2s; animation: fadeDown 0.5s ease 0.05s both;
}
.back-btn:hover{ color:var(--dark); border-color:var(--gold); background:rgba(255,255,255,0.08); }

/* ── Order type toggle ── */
.order-type{
  display:flex; gap:0;
  background: rgba(183,144,85,0.10);
  border-radius:16px; padding:5px;
  margin-bottom:28px; width:fit-content;
  border:1px solid var(--border);
  animation: fadeDown 0.5s ease 0.1s both;
}
.type-btn{
  padding:11px 32px;
  border-radius:12px; border:none;
  cursor:pointer; background:transparent;
  font-family:'Plus Jakarta Sans', sans-serif;
  font-weight:600; font-size:14px;
  color:var(--muted); transition:all 0.25s ease;
  display:flex; align-items:center; gap:8px;
}
.type-btn:hover{ color:var(--dark); }
.type-btn.active{
  background:linear-gradient(135deg, var(--red), var(--red-light));
  color:white; box-shadow:0 6px 18px var(--red-glow);
}

/* ── Grid ── */
.container{
  display:grid;
  grid-template-columns:1.5fr 1fr;
  gap:22px;
  animation: fadeUp 0.5s ease 0.18s both;
}

/* ── Card base ── */
.card{
  background:var(--card-bg);
  border-radius:24px;
  border:1px solid var(--border);
  box-shadow:var(--shadow);
  backdrop-filter:blur(12px);
  overflow:hidden;
}
.card-header{
  padding:18px 22px 14px;
  border-bottom:1px solid var(--border);
  display:flex; align-items:center; gap:10px;
}
.card-header h3{
  font-family:'Noto Serif KR', serif;
  font-size:16px; font-weight:700; color:var(--dark);
}
.card-header .hangul{
  font-size:11px; color:var(--gold); margin-left:auto;
  font-family:'Noto Serif KR', serif;
}
.card-body{ padding:18px 22px; }

/* ── Order items ── */
.item{
  display:flex; align-items:center; gap:14px;
  padding:14px 16px; border-radius:14px;
  margin-bottom:10px;
  background:var(--cream);
  border:1px solid transparent;
  transition:all 0.22s ease; position:relative;
}
.item::before{
  content:''; position:absolute; left:0; top:50%; transform:translateY(-50%);
  width:3px; height:0; background:var(--red);
  border-radius:0 3px 3px 0; transition:height 0.25s ease;
}
.item:hover::before{ height:60%; }
.item:hover{
  background:var(--cream2); border-color:var(--border);
  box-shadow:0 4px 16px rgba(192,57,43,0.08);
}
.item-icon{
  width:46px; height:46px; border-radius:12px;
  background:linear-gradient(135deg,rgba(192,57,43,0.15),rgba(183,144,85,0.12));
  display:flex; align-items:center; justify-content:center;
  font-size:18px; flex-shrink:0; color:var(--gold-light);
  border:1px solid var(--border);
}
.item-info{ flex:1; }
.item-info h3{ font-size:14px; font-weight:700; color:var(--dark); margin-bottom:2px; }
.item-info p{ font-size:12px; color:var(--muted); font-weight:500; }

/* qty controls */
.qty{ display:flex; align-items:center; gap:8px; }
.qty form{ display:inline; }
.btn{
  width:30px; height:30px; border:none; border-radius:9px;
  cursor:pointer; font-size:15px; font-weight:700;
  display:flex; align-items:center; justify-content:center;
  transition:all 0.2s;
}
.btn:hover{ transform:scale(1.12); }
.plus{ background:linear-gradient(135deg,var(--red),var(--red-light)); color:white; box-shadow:0 3px 10px var(--red-glow); }
.minus{ background:var(--cream2); color:var(--muted); border:1px solid var(--border); }
.qty-num{ font-size:15px; font-weight:700; color:var(--dark); min-width:22px; text-align:center; }
.item-price{ font-weight:700; font-size:14px; color:var(--dark); white-space:nowrap; }

/* note area */
.note-area{ margin-top:16px; padding-top:16px; border-top:1px dashed var(--border); }
.note-area label{
  font-size:13px; font-weight:600; color:var(--muted);
  display:flex; align-items:center; gap:6px; margin-bottom:8px;
}
.note-area textarea{
  width:100%; padding:10px 14px;
  border:1px solid var(--border); border-radius:12px;
  background:rgba(255,255,255,0.05);
  font-family:'Plus Jakarta Sans', sans-serif;
  font-size:13px; color:#fff8f0; resize:none; height:80px;
  transition:border-color 0.2s;
}
.note-area textarea::placeholder{ color:rgba(255,220,170,0.35); }
.note-area textarea:focus{ outline:none; border-color:var(--gold); }

/* ── Payment card ── */
.section-label{
  font-size:11px; font-weight:700; letter-spacing:1.5px;
  text-transform:uppercase; color:var(--gold);
  margin-bottom:12px; display:flex; align-items:center; gap:6px;
}
.section-label::before{ content:'—'; opacity:0.5; }

.table-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:18px; }
.table{
  padding:13px 6px; text-align:center;
  border-radius:12px; background:var(--cream);
  border:1.5px solid var(--border);
  cursor:pointer; font-weight:700; font-size:14px;
  color:var(--muted); transition:all 0.22s ease;
}
.table:hover{ border-color:var(--gold); color:var(--dark); transform:translateY(-2px); }
.table.selected{
  background:linear-gradient(135deg,var(--red),var(--red-light));
  color:white; border-color:transparent;
  box-shadow:0 6px 18px var(--red-glow); transform:translateY(-2px);
}

.time-row{ display:flex; align-items:center; gap:8px; margin-bottom:20px; }
.time-row input[type="time"]{
  flex:1; padding:9px 12px;
  border:1.5px solid var(--border); border-radius:10px;
  background:rgba(255,255,255,0.06);
  font-family:'Plus Jakarta Sans', sans-serif;
  font-size:13px; color:#fff8f0; transition:0.2s; color-scheme:dark;
}
.time-row input[type="time"]:focus{ outline:none; border-color:var(--gold); }
.time-row .sep{ font-size:12px; color:var(--muted); font-weight:600; white-space:nowrap; }

.pay-option{
  border:1.5px solid var(--border);
  padding:13px 16px; border-radius:14px;
  margin-bottom:9px; cursor:pointer;
  transition:all 0.22s ease;
  display:flex; align-items:center; gap:12px;
}
.pay-option:hover{ border-color:var(--gold); background:var(--cream); }
.pay-option.active{
  border-color:var(--red); background:rgba(192,57,43,0.15);
  box-shadow:0 4px 14px var(--red-glow);
}
.pay-icon{
  width:38px; height:38px; border-radius:10px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-size:16px; color:var(--gold-light);
  background:var(--cream2); border:1px solid var(--border);
}
.pay-info b{ font-size:13px; color:var(--dark); display:block; }
.pay-info p{ font-size:11px; color:var(--muted); margin-top:1px; }
.pay-check{
  margin-left:auto; width:20px; height:20px; border-radius:50%;
  background:linear-gradient(135deg,var(--red),var(--red-light));
  display:none; align-items:center; justify-content:center;
  color:white; font-size:11px; box-shadow:0 3px 10px var(--red-glow);
}
.pay-option.active .pay-check{ display:flex; }

/* summary */
.summary-box{
  background:rgba(183,144,85,0.08);
  border:1px solid var(--border);
  border-radius:16px; padding:16px 18px;
  margin:18px 0 16px;
}
.summary-row{
  display:flex; justify-content:space-between; align-items:center;
  margin-bottom:10px; font-size:13px; color:var(--muted);
}
.summary-row:last-child{ margin-bottom:0; }
.summary-row b{ color:var(--dark); font-weight:600; }
.summary-divider{ height:1px; background:var(--border); margin:12px 0; }
.summary-total{ display:flex; justify-content:space-between; align-items:center; }
.summary-total span{ font-size:13px; font-weight:700; color:var(--muted); }
.summary-total b{
  font-family:'Noto Serif KR', serif;
  font-size:22px; font-weight:700; color:var(--dark);
}

/* checkout button */
.checkout{
  width:100%; padding:15px; border:none; border-radius:14px;
  background:linear-gradient(135deg,var(--red),var(--red-light));
  color:white; font-size:15px; font-weight:700;
  font-family:'Plus Jakarta Sans', sans-serif;
  cursor:pointer; transition:all 0.3s ease;
  display:flex; align-items:center; justify-content:center; gap:10px;
  box-shadow:0 8px 24px var(--red-glow); letter-spacing:0.3px;
}
.checkout:hover{ transform:translateY(-2px); box-shadow:0 14px 36px var(--red-glow); }
.checkout:active{ transform:translateY(0); }

/* ── Empty state ── */
.empty{ text-align:center; padding:80px 20px; }
.empty-icon{
  width:90px; height:90px; border-radius:50%;
  background:rgba(183,144,85,0.1);
  border:2px dashed var(--border);
  display:flex; align-items:center; justify-content:center;
  font-size:32px; color:rgba(255,220,170,0.4);
  margin:0 auto 20px;
  animation:float 3s ease-in-out infinite;
}
@keyframes float{ 0%,100%{transform:translateY(0);} 50%{transform:translateY(-10px);} }
.empty h3{ font-family:'Noto Serif KR',serif; font-size:20px; color:var(--dark); margin-bottom:8px; }
.empty p{ color:var(--muted); font-size:14px; margin-bottom:24px; }
.empty-btn{
  display:inline-flex; align-items:center; gap:8px;
  background:linear-gradient(135deg,var(--red),var(--red-light));
  color:white; border:none; border-radius:12px;
  padding:12px 24px; font-size:14px; font-weight:700;
  cursor:pointer; font-family:inherit; text-decoration:none;
  box-shadow:0 6px 18px var(--red-glow); transition:transform 0.2s;
}
.empty-btn:hover{ transform:translateY(-2px); }

/* ── Korean deco dots ── */
.korean-deco{
  position:fixed; bottom:30px; left:30px; z-index:0;
  display:flex; flex-direction:column; gap:6px; opacity:0.12;
  pointer-events:none;
}
.korean-deco span{ display:flex; gap:6px; }
.korean-deco span::before,
.korean-deco span::after{
  content:''; width:6px; height:6px; border-radius:50%; background:var(--red);
}

/* ── Animations ── */
@keyframes fadeDown{ from{opacity:0;transform:translateY(-16px);}to{opacity:1;transform:translateY(0);} }
@keyframes fadeUp{ from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);} }

/* ── Responsive ── */
@media(max-width:780px){
  .container{ grid-template-columns:1fr; }
  .order-type{ width:100%; }
  .type-btn{ flex:1; justify-content:center; }
  .wrapper{ padding:20px 16px 40px; }
}
</style>
</head>

<body>
<div class="korean-deco"><span></span><span></span><span></span></div>

<div class="wrapper">

  <!-- HEADER -->
  <a href="pos.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Menu</a>
  <div class="page-header">
    <div class="header-icon"><i class="fas fa-basket-shopping"></i></div>
    <div class="header-text">
      <h1>Order</h1>
      <p>Konfirmasi pesananmu</p>
    </div>
  </div>

  <?php if(empty($cart)): ?>

  <!-- EMPTY STATE -->
  <div class="empty">
    <div class="empty-icon"><i class="fas fa-bowl-food"></i></div>
    <h3>Keranjang Kosong</h3>
    <p>Belum ada pesanan. Yuk pilih menu dulu!</p>
    <a href="pos.php" class="empty-btn"><i class="fas fa-utensils"></i> Pilih Menu</a>
  </div>

  <?php else: ?>

  <!-- FORM -->
  <form action="../process/bayar.php" method="POST">
    <input type="hidden" name="order_type"     id="order_type">
    <input type="hidden" name="table_number"   id="table_number">
    <input type="hidden" name="payment_method" id="payment_method">
    <input type="hidden" name="jam_mulai"      id="jam_mulai">
    <input type="hidden" name="jam_selesai"    id="jam_selesai">

    <!-- ORDER TYPE -->
    <div class="order-type">
      <button type="button" class="type-btn active" onclick="selectType(this,'dinein')">
        <i class="fas fa-utensils"></i> Dine In
      </button>
      <button type="button" class="type-btn" onclick="selectType(this,'takeaway')">
        <i class="fas fa-bag-shopping"></i> Take Away
      </button>
    </div>

    <div class="container">

      <!-- LEFT: ORDER ITEMS -->
      <div class="card">
        <div class="card-header">
          <i class="fas fa-receipt" style="color:var(--red);font-size:15px;"></i>
          <h3>Pesanan Kamu</h3>
          <span class="hangul">주문 내역</span>
        </div>
        <div class="card-body">

          <?php
          $total = 0;
          $fa_icons = ['fa-bowl-food','fa-drumstick-bite','fa-fish','fa-egg','fa-bacon','fa-shrimp','fa-pizza-slice','fa-carrot'];
          $ei = 0;
          foreach($cart as $item):
            $subtotal = $item['harga'] * $item['qty'];
            $total   += $subtotal;
          ?>
          <div class="item">
            <div class="item-icon">
              <i class="fas <?= $fa_icons[$ei++ % count($fa_icons)] ?>"></i>
            </div>
            <div class="item-info">
              <h3><?= htmlspecialchars($item['nama']) ?></h3>
              <p>Rp <?= number_format($item['harga']) ?> / item</p>
            </div>
            <div class="qty">
              <form action="../process/update_qty.php" method="POST">
                <input type="hidden" name="id_menu" value="<?= $item['id_menu'] ?>">
                <input type="hidden" name="action" value="minus">
                <button type="submit" class="btn minus"><i class="fas fa-minus" style="font-size:10px;"></i></button>
              </form>
              <span class="qty-num"><?= $item['qty'] ?></span>
              <form action="../process/update_qty.php" method="POST">
                <input type="hidden" name="id_menu" value="<?= $item['id_menu'] ?>">
                <input type="hidden" name="action" value="plus">
                <button type="submit" class="btn plus"><i class="fas fa-plus" style="font-size:10px;"></i></button>
              </form>
            </div>
            <div class="item-price">Rp <?= number_format($subtotal) ?></div>
          </div>
          <?php endforeach; ?>

          <!-- CATATAN -->
          <div class="note-area">
            <label><i class="fas fa-pen-to-square" style="color:var(--gold);"></i> Catatan (opsional)</label>
            <textarea name="catatan" placeholder="Contoh: tanpa pedas, extra saus, tidak pakai bawang..."></textarea>
          </div>

        </div>
      </div>

      <!-- RIGHT: PAYMENT -->
      <div class="card">
        <div class="card-header">
          <i class="fas fa-credit-card" style="color:var(--red);font-size:15px;"></i>
          <h3>Pembayaran</h3>
          <span class="hangul">결제</span>
        </div>
        <div class="card-body">

          <!-- PILIH MEJA -->
          <div id="tableBox">
            <div class="section-label">Pilih Meja</div>
            <div class="table-grid">
              <?php for($i=1;$i<=12;$i++): ?>
              <div class="table" data-id="<?= $i ?>"><?= $i ?></div>
              <?php endfor; ?>
            </div>
            <div class="section-label" style="margin-bottom:8px;">Jam Booking</div>
            <div class="time-row">
              <input type="time" id="jamMulai">
              <span class="sep">sampai</span>
              <input type="time" id="jamSelesai">
            </div>
          </div>

          <!-- METODE PEMBAYARAN -->
          <div class="section-label">Metode Pembayaran</div>

          <div class="pay-option active" onclick="selectPay(this,'Card')">
            <div class="pay-icon"><i class="fas fa-credit-card"></i></div>
            <div class="pay-info">
              <b>Card</b>
              <p>Bayar menggunakan kartu</p>
            </div>
            <div class="pay-check"><i class="fas fa-check"></i></div>
          </div>

          <div class="pay-option" onclick="selectPay(this,'Cash')">
            <div class="pay-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="pay-info">
              <b>Cash</b>
              <p>Pembayaran di kasir</p>
            </div>
            <div class="pay-check"><i class="fas fa-check"></i></div>
          </div>

          <div class="pay-option" onclick="selectPay(this,'QR')">
            <div class="pay-icon"><i class="fas fa-qrcode"></i></div>
            <div class="pay-info">
              <b>QR / E-Wallet</b>
              <p>QRIS · GoPay · OVO</p>
            </div>
            <div class="pay-check"><i class="fas fa-check"></i></div>
          </div>

          <!-- SUMMARY -->
          <div class="summary-box">
            <div class="summary-row">
              <span>Subtotal</span>
              <b>Rp <?= number_format($total) ?></b>
            </div>
            <div class="summary-row">
              <span>Tax (10%)</span>
              <b>Rp <?= number_format($total * 0.1) ?></b>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total">
              <span>Total</span>
              <b>Rp <?= number_format($total * 1.1) ?></b>
            </div>
          </div>

          <!-- CONFIRM BUTTON -->
          <button type="submit" class="checkout">
            <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
          </button>

        </div>
      </div>

    </div>
  </form>

  <?php endif; ?>

</div>

<script>
document.getElementById("order_type").value     = "dinein";
document.getElementById("payment_method").value = "Card";

function selectType(el, type){
  document.querySelectorAll(".type-btn").forEach(b=>b.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("order_type").value = type;
  const box = document.getElementById("tableBox");
  box.style.display = type === "dinein" ? "block" : "none";
  if(type !== "dinein") document.getElementById("table_number").value = "";
}

document.querySelectorAll(".table").forEach(t=>{
  t.onclick = function(){
    document.querySelectorAll(".table").forEach(x=>x.classList.remove("selected"));
    this.classList.add("selected");
    document.getElementById("table_number").value = this.dataset.id;
  };
});

function selectPay(el, method){
  document.querySelectorAll(".pay-option").forEach(e=>e.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("payment_method").value = method;
}

document.getElementById("jamMulai").onchange = function(){
  document.getElementById("jam_mulai").value = this.value;
};
document.getElementById("jamSelesai").onchange = function(){
  document.getElementById("jam_selesai").value = this.value;
};
</script>
</body>
</html>