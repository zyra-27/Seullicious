<?php
session_start();
if(!isset($_SESSION['username'])){ header("Location: ../auth/login.php"); exit; }
if($_SESSION['status'] !== 'admin'){ header("Location: ../auth/login.php"); exit; }
include "../config/koneksi.php";
include "sidebar.php";
$order = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders");
$total_orders = mysqli_fetch_assoc($order)['total'];
$revenue = mysqli_query($koneksi,"SELECT SUM(total) as total FROM orders WHERE order_status='DONE'");
$total_revenue = mysqli_fetch_assoc($revenue)['total'];
$menu = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM menu");
$total_menu = mysqli_fetch_assoc($menu)['total'];
$active = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders WHERE order_status='NEW'");
$active_orders = mysqli_fetch_assoc($active)['total'];
$recent = mysqli_query($koneksi,"SELECT * FROM orders ORDER BY id_order DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Seoullicious</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--ink:#1a1008;--paper:#f7f2ea;--cream:#efe8d8;--gold:#b8893a;--gold-light:#e8c97a;--muted:#8a7660;--border:#e2d9c8}
body{font-family:'DM Sans',sans-serif;background:var(--paper);min-height:100vh}
.main{margin-left:240px;padding:44px 48px;max-width:1200px}
.page-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:40px;padding-bottom:28px;border-bottom:1px solid var(--border)}
.page-eyebrow{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:6px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:36px;color:var(--ink);line-height:1}
.page-date{font-size:13px;color:var(--muted)}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:40px}
.stat-card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:26px 24px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(26,16,8,0.08)}
.stat-card::after{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--gold);opacity:0;transition:opacity .2s}
.stat-card:hover::after{opacity:1}
.stat-icon{width:36px;height:36px;border-radius:8px;background:var(--cream);display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--gold);margin-bottom:16px}
.stat-label{font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);font-weight:500;margin-bottom:6px}
.stat-value{font-family:'Cormorant Garamond',serif;font-size:30px;color:var(--ink);line-height:1}
.stat-value.small{font-size:22px}
.stat-change{margin-top:10px;font-size:11px;color:var(--muted)}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.section-title{font-family:'Cormorant Garamond',serif;font-size:20px;color:var(--ink)}
.section-link{font-size:12px;color:var(--gold);text-decoration:none;font-weight:600}
.section-link:hover{text-decoration:underline}
.table-wrap{background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}
thead th{padding:14px 20px;text-align:left;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);background:var(--paper);border-bottom:1px solid var(--border)}
tbody td{padding:16px 20px;font-size:14px;color:var(--ink);border-bottom:1px solid #f0ebe0}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:#faf7f2}
.order-id{font-family:monospace;font-size:12px;color:var(--muted);letter-spacing:.5px}
.order-date{font-size:12px;color:var(--muted)}
.order-total{font-weight:600}
.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}
.badge-done{background:#eaf6ef;color:#2a6641;border:1px solid #c4e8d2}
.badge-new{background:#fef6e4;color:#8a6500;border:1px solid #f0dfa0}
</style>
</head>
<body>
<div class="main">
  <div class="page-header">
    <div>
      <div class="page-eyebrow">Admin Panel</div>
      <h1 class="page-title">Dashboard</h1>
    </div>
    <div class="page-date"><?= date('l, d F Y') ?></div>
  </div>
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-receipt"></i></div>
      <div class="stat-label">Total Orders</div>
      <div class="stat-value"><?= number_format($total_orders) ?></div>
      <div class="stat-change">Semua waktu</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-wallet"></i></div>
      <div class="stat-label">Pendapatan</div>
      <div class="stat-value small">Rp <?= number_format($total_revenue) ?></div>
      <div class="stat-change">Order selesai</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-utensils"></i></div>
      <div class="stat-label">Total Menu</div>
      <div class="stat-value"><?= number_format($total_menu) ?></div>
      <div class="stat-change">Item aktif</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-fire"></i></div>
      <div class="stat-label">Order Aktif</div>
      <div class="stat-value"><?= number_format($active_orders) ?></div>
      <div class="stat-change">Menunggu proses</div>
    </div>
  </div>
  <div class="section-header">
    <h2 class="section-title">Order Terbaru</h2>
    <a href="history.php" class="section-link">Lihat semua →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Order ID</th><th>Tanggal</th><th>Tipe</th><th>Total</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($recent)): ?>
        <tr>
          <td><span class="order-id">#<?= str_pad($r['id_order'],4,'0',STR_PAD_LEFT) ?></span></td>
          <td><span class="order-date"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></span></td>
          <td><?= htmlspecialchars($r['order_type']) ?></td>
          <td class="order-total">Rp <?= number_format($r['total']) ?></td>
          <td>
            <?php if($r['order_status']=='DONE'): ?>
              <span class="badge badge-done">Done</span>
            <?php else: ?>
              <span class="badge badge-new">New</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>