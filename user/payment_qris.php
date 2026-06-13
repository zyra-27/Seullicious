<?php
session_start();
include "../config/koneksi.php";
include "../config/xendit.php";

if(!isset($_SESSION['username'])){
    header("Location: ../auth/login.php"); exit;
}

$order_id   = (int)($_GET['id']  ?? 0);
$external_id = $_GET['ext'] ?? '';

if(!$order_id){ header("Location: pos.php"); exit; }

// Ambil data order
$stmt = mysqli_prepare($koneksi,"SELECT * FROM orders WHERE id_order=? LIMIT 1");
mysqli_stmt_bind_param($stmt,'i',$order_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$order){ header("Location: pos.php"); exit; }

// Kalau sudah dibayar, langsung ke receipt
if($order['order_status'] === 'PAID' || $order['order_status'] === 'NEW'){
    header("Location: receipt.php?id={$order_id}"); exit;
}

$qr_string = $order['qr_string'] ?? '';
$total     = $order['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seoullicious — Pembayaran QRIS</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'DM Sans',sans-serif;
  background:#faf6f0;
  min-height:100vh;
  display:flex;align-items:center;justify-content:center;
  padding:24px 16px;
}
.card{
  background:white;
  border-radius:24px;
  padding:36px 32px;
  max-width:420px; width:100%;
  box-shadow:0 20px 60px rgba(28,15,0,0.1);
  text-align:center;
  animation:rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes rise{from{opacity:0;transform:translateY(24px);}to{opacity:1;transform:translateY(0);}}

.logo{
  font-family:'Cormorant Garamond',serif;
  font-size:22px;font-weight:700;color:#2d1a08;
  margin-bottom:4px;
}
.order-num{font-size:12px;color:#a08060;margin-bottom:24px;}

.qr-wrap{
  background:#f5f0e8;
  border-radius:18px;padding:20px;
  display:inline-block;
  margin-bottom:20px;
  border:2px solid #e8ddd0;
  position:relative;
}
#qrcode img, #qrcode canvas{
  border-radius:8px;
  display:block;
}
.qr-logo{
  position:absolute;
  top:50%;left:50%;
  transform:translate(-50%,-50%);
  width:40px;height:40px;
  background:white;
  border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;
  box-shadow:0 2px 8px rgba(0,0,0,0.15);
}

.amount{
  font-family:'Cormorant Garamond',serif;
  font-size:32px;font-weight:700;color:#2d1a08;
  margin-bottom:4px;
}
.amount-label{font-size:12px;color:#a08060;margin-bottom:20px;}

.timer-wrap{
  background:#fff8ee;border:1px solid #f0d9a8;
  border-radius:12px;padding:10px 16px;
  margin-bottom:20px;
  display:flex;align-items:center;justify-content:center;gap:8px;
}
.timer-wrap i{color:#c28b3c;}
.timer{font-size:15px;font-weight:700;color:#8b5a1a;}
.timer-label{font-size:12px;color:#a08060;}

.status-badge{
  display:inline-flex;align-items:center;gap:6px;
  padding:8px 16px;border-radius:20px;
  font-size:13px;font-weight:600;margin-bottom:20px;
}
.status-pending{background:#fff3cd;color:#856404;}
.status-paid{background:#d1fae5;color:#065f46;}

.instructions{
  text-align:left;background:#faf6f0;
  border-radius:12px;padding:14px 16px;
  margin-bottom:20px;
}
.instructions p{font-size:12px;color:#a08060;margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
.instructions ol{padding-left:16px;}
.instructions li{font-size:13px;color:#5a3a18;line-height:1.8;}

.btn-check{
  width:100%;padding:14px;border:none;border-radius:14px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;font-size:14px;font-weight:700;
  cursor:pointer;font-family:inherit;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:opacity 0.2s,transform 0.15s;
  margin-bottom:10px;
}
.btn-check:hover{opacity:0.88;transform:translateY(-2px);}

.btn-cancel{
  width:100%;padding:12px;border:1.5px solid #e8ddd0;
  border-radius:14px;background:transparent;
  color:#a08060;font-size:13px;font-weight:600;
  cursor:pointer;font-family:inherit;transition:all 0.2s;
}
.btn-cancel:hover{background:#f5f0e8;color:#5a3a18;}

.spinner{
  display:none;
  width:20px;height:20px;
  border:2px solid rgba(255,255,255,0.3);
  border-top-color:white;
  border-radius:50%;
  animation:spin 0.7s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg);}}

.paid-overlay{
  display:none;
  position:fixed;inset:0;
  background:rgba(6,95,70,0.92);
  backdrop-filter:blur(4px);
  z-index:100;
  align-items:center;justify-content:center;
  flex-direction:column;gap:16px;
  animation:fadeIn 0.4s ease both;
}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
.paid-overlay i{font-size:64px;color:white;}
.paid-overlay h2{font-size:24px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;}
.paid-overlay p{font-size:14px;color:rgba(255,255,255,0.8);}
</style>
</head>
<body>

<div class="paid-overlay" id="paidOverlay">
  <i class="fas fa-circle-check"></i>
  <h2>Pembayaran Berhasil!</h2>
  <p>Mengalihkan ke struk...</p>
</div>

<div class="card">
  <div class="logo">Seoullicious</div>
  <div class="order-num">Order #<?= $order_id ?></div>

  <!-- QR Code -->
  <div class="qr-wrap">
    <div id="qrcode"></div>
    <div class="qr-logo"><i class="fas fa-qrcode" style="color:#c28b3c;font-size:18px;"></i></div>
  </div>

  <div class="amount">Rp <?= number_format($total, 0, ',', '.') ?></div>
  <div class="amount-label">Total yang harus dibayar</div>

  <div class="timer-wrap">
    <i class="fas fa-clock"></i>
    <span class="timer-label">Berlaku selama</span>
    <span class="timer" id="countdown">30:00</span>
  </div>

  <div class="status-badge status-pending" id="statusBadge">
    <i class="fas fa-hourglass-half"></i> Menunggu Pembayaran
  </div>

  <div class="instructions">
    <p>Cara Bayar</p>
    <ol>
      <li>Buka aplikasi GoPay, OVO, Dana, atau m-banking</li>
      <li>Pilih menu <b>Scan QR / QRIS</b></li>
      <li>Scan QR code di atas</li>
      <li>Konfirmasi pembayaran di aplikasimu</li>
    </ol>
  </div>

  <button class="btn-check" onclick="checkPayment()" id="btnCheck">
    <span id="btnText"><i class="fas fa-rotate-right"></i> Cek Status Pembayaran</span>
    <div class="spinner" id="spinner"></div>
  </button>

  <button class="btn-cancel" onclick="if(confirm('Batalkan pesanan?')) window.location='pos.php'">
    Batalkan Pesanan
  </button>
</div>

<script>
const ORDER_ID   = <?= $order_id ?>;
const EXT_ID     = <?= json_encode($external_id) ?>;
const QR_STRING  = <?= json_encode($qr_string) ?>;

// Generate QR code dari qr_string Xendit
new QRCode(document.getElementById("qrcode"), {
  text: QR_STRING || "SEOULLICIOUS-ORDER-" + ORDER_ID,
  width: 220, height: 220,
  colorDark: "#1c0f00", colorLight: "#f5f0e8",
  correctLevel: QRCode.CorrectLevel.M
});

// Countdown 30 menit
let seconds = 30 * 60;
const countdown = document.getElementById('countdown');
const timer = setInterval(() => {
  seconds--;
  if(seconds <= 0){ clearInterval(timer); countdown.textContent = '00:00'; return; }
  const m = String(Math.floor(seconds/60)).padStart(2,'0');
  const s = String(seconds % 60).padStart(2,'0');
  countdown.textContent = `${m}:${s}`;
}, 1000);

// Auto cek setiap 8 detik
let autoCheck = setInterval(() => checkPayment(true), 8000);

async function checkPayment(auto = false){
  if(!auto){
    document.getElementById('btnText').style.display = 'none';
    document.getElementById('spinner').style.display = 'block';
  }

  try {
    const res = await fetch(`../process/check_payment.php?id=${ORDER_ID}`);
    const data = await res.json();

    if(data.status === 'PAID' || data.status === 'NEW'){
      clearInterval(autoCheck);
      clearInterval(timer);
      document.getElementById('paidOverlay').style.display = 'flex';
      setTimeout(() => {
        window.location = `receipt.php?id=${ORDER_ID}`;
      }, 2000);
    }
  } catch(e){}

  if(!auto){
    document.getElementById('btnText').style.display = 'flex';
    document.getElementById('spinner').style.display = 'none';
  }
}
</script>
</body>
</html>