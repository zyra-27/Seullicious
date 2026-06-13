<?php
session_start();
include "../config/koneksi.php";
include "../admin/sidebar.php";

$username = $_SESSION['username'] ?? 'Kasir';

$jam = date('H');
if($jam >= 5 && $jam < 12) $greeting = "Good Morning";
elseif($jam >= 12 && $jam < 17) $greeting = "Good Afternoon";
elseif($jam >= 17 && $jam < 21) $greeting = "Good Evening";
else $greeting = "Good Night";

$limit  = 10;
$page   = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$count_q   = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM orders");
$total_row = mysqli_fetch_assoc($count_q)['total'] ?? 0;
$total_pages = ceil($total_row / $limit);

$data = mysqli_query($koneksi,"
    SELECT * FROM orders 
    ORDER BY id_order DESC 
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>History — Seoullicious</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:#f5f0e8;
  display:flex;
  height:100vh;
  overflow:hidden;
}

/* ========== SIDEBAR WRAPPER ========== */
/* Pastikan sidebar punya width tetap dan tidak overlap */
body > :first-child,
.sidebar {
  width: 210px;
  flex-shrink: 0;
  height: 100vh;
  position: relative;
  z-index: 100;
}

/* ========== MAIN ========== */
.main{
  flex:1;
  display:flex;
  flex-direction:column;
  overflow:hidden;
  min-width:0; /* penting agar flex tidak overflow */
}

/* ========== TOPBAR ========== */
.topbar{
  background:white;
  padding:16px 24px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  border-bottom:1px solid #e8ddd0;
  flex-shrink:0;
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
  font-family:'Playfair Display',serif;
  font-size:22px;font-weight:700;color:#2d1a08;
}
.page-title p{font-size:12px;color:#a08060;margin-top:1px;}

/* ========== STATS ROW ========== */
.stats-row{
  display:grid;grid-template-columns:repeat(3,1fr);gap:14px;
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
.history-row:hover{background:#fdf8f2;}

.order-num{min-width:110px;}
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
  display:flex;gap:16px;align-items:center;flex-wrap:wrap;
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

.empty{text-align:center;padding:60px 20px;color:#c0a080;}
.empty i{font-size:40px;margin-bottom:12px;display:block;}
.empty p{font-size:14px;}

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

<?php /* sidebar sudah di-include di atas */ ?>

<!-- ========== MAIN ========== -->
<div class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" id="toggleSidebar">☰</button>
      <div class="greeting">
        <h2><?= $greeting ?>, <?= ucfirst($username) ?>!</h2>
        <p><?= date('l, d F Y') ?></p>
      </div>
    </div>
    <div class="user-badge">
      <div class="user-avatar"><?= strtoupper(substr($username,0,1)) ?></div>
      <span><?= ucfirst($username) ?></span>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <div class="page-title">
      <div class="page-title-icon"><i class="fas fa-clock-rotate-left"></i></div>
      <div>
        <h3>Transaction History</h3>
        <p>Semua riwayat pesanan</p>
      </div>
    </div>

    <?php
    $stat_total = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c, SUM(total) as s FROM orders"));
    $stat_today = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM orders WHERE DATE(tanggal)=CURDATE()"));
    ?>
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef3e2;">📋</div>
        <div>
          <div class="stat-label">Total Order</div>
          <div class="stat-value"><?= number_format($stat_total['c']) ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#e8f8ee;">📅</div>
        <div>
          <div class="stat-label">Order Hari Ini</div>
          <div class="stat-value"><?= number_format($stat_today['c']) ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fde8e8;">💰</div>
        <div>
          <div class="stat-label">Total Pendapatan</div>
          <div class="stat-value" style="font-size:14px;">Rp <?= number_format($stat_total['s'],0,',','.') ?></div>
        </div>
      </div>
    </div>

    <div class="history-wrap">
      <div class="history-header">
        <span>Riwayat Transaksi</span>
        <small><?= $total_row ?> total order</small>
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
            $badgeLabel = ($type === 'dinein' || $type === 'dine-in') ? 'Dine In' : 'Take Away';
          ?>
          <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>

          <div class="order-meta">
            <div class="meta-item">
              <i class="fas fa-chair"></i>
              <?= $row['meja'] ? "Meja ".$row['meja'] : "Take Away" ?>
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
            <i class="fas fa-receipt"></i> Detail
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
        <div class="empty">
          <i class="fas fa-bowl-food"></i>
          <p>Belum ada transaksi</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
// Toggle sidebar collapse
const toggleBtn = document.getElementById('toggleSidebar');
if(toggleBtn){
  toggleBtn.addEventListener('click', () => {
    const sidebar = document.querySelector('.sidebar') || document.body.firstElementChild;
    sidebar.classList.toggle('collapsed');
  });
}
</script>
</body>
</html>