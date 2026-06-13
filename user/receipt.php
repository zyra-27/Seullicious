<?php
session_start();
include "../config/koneksi.php";
include "../config/xendit.php";

// ── Handle return dari Xendit (paid=1&ext=SEOUL-xxx) ──────────────────────────
if(isset($_GET['paid']) && isset($_GET['ext'])){
    $ext_id = $_GET['ext'];
    // Ambil order berdasarkan external_id
    $s = mysqli_prepare($koneksi,"SELECT id_order, xendit_invoice_id FROM orders WHERE xendit_external_id=? LIMIT 1");
    mysqli_stmt_bind_param($s,'s',$ext_id);
    mysqli_stmt_execute($s);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    if($row){
        // Cek status ke Xendit
        $inv = xendit_check_invoice($row['xendit_invoice_id']);
        if(in_array($inv['status'] ?? '', ['PAID','SETTLED'])){
            $upd = mysqli_prepare($koneksi,"UPDATE orders SET order_status='NEW' WHERE id_order=?");
            mysqli_stmt_bind_param($upd,'i',$row['id_order']);
            mysqli_stmt_execute($upd);
        }
        header("Location: receipt.php?id=" . $row['id_order']);
        exit;
    }
}

// ── Handle return dari session (pending_order_id) ─────────────────────────────
if(!isset($_GET['id']) && isset($_SESSION['pending_order_id'])){
    $pending = $_SESSION['pending_order_id'];
    unset($_SESSION['pending_order_id']);
    header("Location: receipt.php?id={$pending}");
    exit;
}

// FIX: prepared statement — no SQL injection
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmtO = mysqli_prepare($koneksi, "SELECT * FROM orders WHERE id_order = ?");
mysqli_stmt_bind_param($stmtO, "i", $id);
mysqli_stmt_execute($stmtO);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtO));
mysqli_stmt_close($stmtO);

$stmtI = mysqli_prepare($koneksi, "SELECT oi.*, m.nama_menu FROM order_items oi JOIN menu m ON oi.id_menu = m.id_menu WHERE oi.id_order = ?");
mysqli_stmt_bind_param($stmtI, "i", $id);
mysqli_stmt_execute($stmtI);
$items_result = mysqli_stmt_get_result($stmtI);
$items = [];
while($row = mysqli_fetch_assoc($items_result)) $items[] = $row;
mysqli_stmt_close($stmtI);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seoullicious — Receipt #<?= $id ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --ink:#1c0f00; --paper:#faf6f0; --cream:#f5f0e8;
  --gold:#c28b3c; --gold-light:#f5d080; --gold-pale:#fdf4e3;
  --muted:#8b6a40; --border:#e8ddd0; --dashed:#d4c5ae;
}
body{
  min-height:100vh;background:var(--cream);
  font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;
  padding:32px 16px;
}

/* ── Card ── */
.receipt{
  width:100%;max-width:400px;
  background:#fff;
  border-radius:20px;
  box-shadow:0 20px 60px rgba(28,15,0,0.1), 0 4px 16px rgba(28,15,0,0.06);
  overflow:hidden;
  animation:rise .45s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes rise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}

/* ── Header band ── */
.receipt-header{
  background:var(--ink);
  padding:32px 28px 28px;
  text-align:center;
  position:relative;
  overflow:hidden;
}
.receipt-header::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 50% -20%, rgba(194,139,60,0.28) 0%, transparent 65%);
  pointer-events:none;
}
.brand-name{
  font-family:'Cormorant Garamond',serif;
  font-size:26px;font-weight:700;letter-spacing:2px;
  color:var(--gold-light);
  position:relative;z-index:1;
}
.brand-sub{
  font-size:11px;letter-spacing:2px;text-transform:uppercase;
  color:rgba(245,208,128,0.4);margin-top:5px;
  position:relative;z-index:1;
}
.order-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(194,139,60,0.18);border:1px solid rgba(194,139,60,0.3);
  border-radius:20px;padding:5px 14px;margin-top:14px;
  font-size:12px;font-weight:600;color:var(--gold-light);
  position:relative;z-index:1;letter-spacing:0.5px;
}
.order-badge-dot{
  width:6px;height:6px;border-radius:50%;background:var(--gold);
  animation:pulse 1.8s ease-in-out infinite;
}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.65)}}

/* ── Body ── */
.receipt-body{padding:24px 28px}

/* Info rows */
.info-grid{
  display:grid;grid-template-columns:auto 1fr;
  gap:6px 14px;font-size:13px;margin-bottom:20px;
}
.info-label{color:var(--muted);font-weight:500;white-space:nowrap}
.info-value{color:var(--ink);font-weight:500;text-align:right}
.info-value b{font-weight:700;color:var(--gold)}

/* Dashed divider */
.dash{
  border:none;border-top:1.5px dashed var(--dashed);
  margin:18px 0;
}

/* Items */
.items-head{
  display:flex;justify-content:space-between;
  font-size:10px;letter-spacing:1.5px;text-transform:uppercase;
  color:var(--muted);font-weight:600;margin-bottom:12px;
}
.item-row{
  display:flex;justify-content:space-between;align-items:flex-start;
  margin-bottom:12px;
}
.item-left{}
.item-name{font-size:14px;font-weight:600;color:var(--ink)}
.item-qty{
  display:inline-block;margin-top:3px;
  font-size:11px;color:var(--muted);
  background:var(--cream);border-radius:4px;
  padding:1px 7px;font-weight:500;
}
.item-price{font-size:14px;font-weight:600;color:var(--ink);white-space:nowrap}

/* Total */
.total-row{
  display:flex;justify-content:space-between;align-items:center;
  background:var(--gold-pale);border-radius:10px;
  padding:14px 16px;margin-top:4px;
}
.total-label{
  font-family:'Cormorant Garamond',serif;
  font-size:18px;font-weight:700;color:var(--ink);
}
.total-amount{
  font-family:'Cormorant Garamond',serif;
  font-size:22px;font-weight:700;color:var(--gold);
}

/* Footer note */
.thank-you{
  text-align:center;padding:18px 0 4px;
  font-size:12.5px;color:var(--muted);line-height:1.8;
}

/* ── Actions ── */
.actions{padding:4px 28px 28px;display:flex;flex-direction:column;gap:10px}

.btn-review{
  display:flex;align-items:center;justify-content:center;gap:8px;
  width:100%;padding:14px;border:none;border-radius:12px;
  background:linear-gradient(135deg,var(--ink),#3d2008);
  color:var(--gold-light);
  font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;
  cursor:pointer;text-decoration:none;
  transition:transform .18s, box-shadow .18s;
  box-shadow:0 6px 20px rgba(28,15,0,0.2);
}
.btn-review:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(28,15,0,0.28)}

.btn-back{
  display:flex;align-items:center;justify-content:center;gap:8px;
  width:100%;padding:13px;border-radius:12px;
  border:1.5px solid var(--border);background:#fff;
  color:var(--muted);
  font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;
  cursor:pointer;text-decoration:none;
  transition:background .18s, border-color .18s, color .18s;
}
.btn-back:hover{background:var(--cream);border-color:var(--gold);color:var(--ink)}

.review-hint{
  text-align:center;font-size:11px;color:var(--muted);
  padding:0 0 4px;letter-spacing:0.3px;
}
</style>
</head>
<body>
<div class="receipt">

  <!-- HEADER -->
  <div class="receipt-header">
    <div class="brand-name">Seoullicious</div>
    <div class="brand-sub">Cafe &amp; Korean Food</div>
    <div class="order-badge">
      <span class="order-badge-dot"></span>
      Order #<?= $id ?> · Selesai
    </div>
  </div>

  <!-- BODY -->
  <div class="receipt-body">

    <!-- Info -->
    <div class="info-grid">
      <span class="info-label">Tanggal</span>
      <span class="info-value"><?= htmlspecialchars($order['tanggal'] ?? '-') ?></span>

      <span class="info-label">Tipe</span>
      <span class="info-value"><?= htmlspecialchars(ucfirst($order['order_type'] ?? '-')) ?></span>

      <?php if(!empty($order['meja'])): ?>
      <span class="info-label">Meja</span>
      <span class="info-value"><b><?= htmlspecialchars($order['meja']) ?></b></span>
      <?php endif; ?>

      <span class="info-label">Pembayaran</span>
      <span class="info-value"><?= htmlspecialchars(ucfirst($order['metode_bayar'] ?? '-')) ?></span>
    </div>

    <hr class="dash">

    <!-- Items -->
    <div class="items-head">
      <span>Item</span>
      <span>Harga</span>
    </div>

    <?php foreach($items as $item): ?>
    <div class="item-row">
      <div class="item-left">
        <div class="item-name"><?= htmlspecialchars($item['nama_menu']) ?></div>
        <span class="item-qty">×<?= (int)$item['qty'] ?></span>
      </div>
      <div class="item-price">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
    </div>
    <?php endforeach; ?>

    <hr class="dash">

    <!-- Total -->
    <div class="total-row">
      <span class="total-label">Total</span>
      <span class="total-amount">Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
    </div>

    <!-- Thank you -->
    <div class="thank-you">
      Terima kasih sudah makan di Seoullicious 🙏<br>
      Selamat menikmati hidanganmu!
    </div>

  </div><!-- /receipt-body -->

  <!-- ACTIONS — no print button -->
  <div class="actions">
    <p class="review-hint">Punya pengalaman hari ini?</p>
    <a href="review.php?id=<?= $id ?>" class="btn-review">
      ★ Tulis Review
    </a>
    <a href="pos.php" class="btn-back">
      Kembali ke Menu
    </a>
  </div>

</div>
</body>
</html>