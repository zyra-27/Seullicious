<?php
session_start();
include "../config/koneksi.php";

// Guard: hanya kasir & admin yang boleh akses
if(!isset($_SESSION['status']) || !in_array($_SESSION['status'], ['kasir','admin'])){
    header("Location: ../auth/login.php");
    exit;
}

// ============================================================
// DATA: REVIEW RESTORAN
// ============================================================
$resto_avg_row = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT
        COUNT(*) as total,
        ROUND(AVG(bintang),2) as avg_bintang,
        SUM(bintang=5) as s5,
        SUM(bintang=4) as s4,
        SUM(bintang=3) as s3,
        SUM(bintang=2) as s2,
        SUM(bintang=1) as s1
    FROM review_restoran
"));

$resto_reviews = mysqli_query($koneksi,"
    SELECT rr.*, u.username, u.nama_lengkap, o.tanggal
    FROM review_restoran rr
    LEFT JOIN user u ON rr.id_user = u.id_user
    LEFT JOIN orders o ON rr.id_order = o.id_order
    ORDER BY rr.created_at DESC
    LIMIT 50
");

// ============================================================
// DATA: REVIEW MENU (per menu)
// ============================================================
$menu_stats = mysqli_query($koneksi,"
    SELECT
        m.id_menu,
        m.nama_menu,
        COUNT(rm.id_review) as total,
        ROUND(AVG(rm.bintang),2) as avg_bintang,
        SUM(rm.bintang=5) as s5,
        SUM(rm.bintang=4) as s4,
        SUM(rm.bintang=3) as s3,
        SUM(rm.bintang=2) as s2,
        SUM(rm.bintang=1) as s1
    FROM menu m
    LEFT JOIN review_menu rm ON m.id_menu = rm.id_menu
    GROUP BY m.id_menu, m.nama_menu
    HAVING total > 0
    ORDER BY avg_bintang DESC
");
$menu_stats_data = [];
while($r = mysqli_fetch_assoc($menu_stats)) $menu_stats_data[] = $r;

// Semua review menu terbaru
$menu_reviews = mysqli_query($koneksi,"
    SELECT rm.*, u.username, u.nama_lengkap, m.nama_menu, o.tanggal
    FROM review_menu rm
    LEFT JOIN user u ON rm.id_user = u.id_user
    LEFT JOIN menu m ON rm.id_menu = m.id_menu
    LEFT JOIN orders o ON rm.id_order = o.id_order
    ORDER BY rm.created_at DESC
    LIMIT 50
");

// Active tab dari GET
$tab = $_GET['tab'] ?? 'restoran';

// Greeting berdasarkan jam
$jam = (int)date('H');
if($jam >= 5 && $jam < 12) $greeting = "Good Morning";
elseif($jam >= 12 && $jam < 17) $greeting = "Good Afternoon";
elseif($jam >= 17 && $jam < 21) $greeting = "Good Evening";
else $greeting = "Good Night";

$username = $_SESSION['username'] ?? 'Kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review Dashboard — Seoullicious</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:#f5f0e8;
  display:flex;height:100vh;overflow:hidden;
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

/* ─── PAGE TITLE ─── */
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

/* ─── SUMMARY CARDS ─── */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.sum-card {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    border: 1px solid #ede0cc;
    display: flex;
    align-items: center;
    gap: 14px;
    position: relative;
    overflow: hidden;
}

.sum-card-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.sum-card .label {
    font-size: 12px;
    color: #a08060;
    margin-bottom: 3px;
}

.sum-card .value {
    font-size: 18px;
    font-weight: 800;
    color: #2d1a08;
}

.sum-card .value.gold { color: #c28b3c; }

.stars-display { display: flex; gap: 2px; margin-top: 3px; }
.stars-display .s { font-size: 14px; }
.stars-display .s.filled { color: #f5c518; }
.stars-display .s.empty  { color: #ddd; }

/* ─── TABS ─── */
.tabs {
    display: flex;
    gap: 4px;
    background: #ede0cc;
    border-radius: 14px;
    padding: 5px;
    margin-bottom: 20px;
    width: fit-content;
}

.tab-btn {
    padding: 10px 24px;
    border: none;
    background: transparent;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #a08060;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    display: block;
}

.tab-btn.active {
    background: white;
    color: #c28b3c;
    box-shadow: 0 2px 10px rgba(90,50,10,0.10);
}

.tab-btn:hover:not(.active) { color: #3b1f08; }

/* ─── SECTION ─── */
.section { display: none; }
.section.active { display: block; }

/* ─── RATING CHART ─── */
.chart-card {
    background: white;
    border-radius: 20px;
    padding: 20px 24px;
    border: 1px solid #ede0cc;
    overflow: hidden;
    margin-bottom: 20px;
}

.chart-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    color: #2d1a08;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.rating-overview { display: flex; gap: 32px; align-items: center; }

.rating-big { text-align: center; min-width: 100px; }

.rating-big .num {
    font-family: 'Playfair Display', serif;
    font-size: 52px;
    color: #c28b3c;
    line-height: 1;
}

.rating-big .stars {
    display: flex;
    justify-content: center;
    gap: 3px;
    margin: 6px 0;
    font-size: 18px;
}

.rating-big .count { font-size: 12px; color: #a08060; }

.rating-bars { flex: 1; }

.bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.bar-row .lbl { font-size: 12px; color: #a08060; width: 40px; text-align: right; flex-shrink: 0; }

.bar-track {
    flex: 1;
    height: 10px;
    background: #f5ead6;
    border-radius: 99px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, #c28b3c, #f5c518);
}
.bar-fill.green  { background: linear-gradient(90deg, #4a7c3f, #6db860); }
.bar-fill.yellow { background: linear-gradient(90deg, #b79055, #f5c518); }
.bar-fill.orange { background: linear-gradient(90deg, #c28b3c, #e8a020); }
.bar-fill.red    { background: linear-gradient(90deg, #c0392b, #e74c3c); }

.bar-row .cnt { font-size: 12px; color: #a08060; width: 28px; flex-shrink: 0; }

/* ─── MENU LEADERBOARD ─── */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.menu-stat-card {
    background: white;
    border-radius: 14px;
    padding: 16px 18px;
    border: 1px solid #ede0cc;
    display: flex;
    gap: 14px;
    align-items: center;
}

.rank-badge {
    width: 34px; height: 34px;
    border-radius: 10px;
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    font-weight: bold;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rank-1 { background: #fff3cc; color: #b8860b; }
.rank-2 { background: #f0f0f0; color: #666; }
.rank-3 { background: #fde8d8; color: #b55a1a; }
.rank-n { background: #f5ead6; color: #a08060; }

.menu-stat-info { flex: 1; min-width: 0; }
.menu-stat-name {
    font-size: 14px;
    font-weight: 600;
    color: #2d1a08;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 4px;
}

.mini-bar-row { display: flex; align-items: center; gap: 6px; margin-bottom: 3px; }
.mini-bar-row .lbl { font-size: 10px; color: #a08060; width: 12px; }
.mini-track { flex: 1; height: 5px; background: #f5ead6; border-radius: 99px; overflow: hidden; }
.mini-fill {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, #c28b3c, #f5c518);
}

.menu-stat-avg { text-align: right; flex-shrink: 0; }
.menu-stat-avg .avg-num {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    color: #c28b3c;
    line-height: 1;
}
.menu-stat-avg .avg-star { font-size: 13px; color: #f5c518; }
.menu-stat-avg .avg-cnt  { font-size: 11px; color: #a08060; }

/* ─── REVIEW LIST ─── */
.review-list-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #ede0cc;
    overflow: hidden;
}

.review-list-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f5ede0;
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    color: #2d1a08;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.review-item {
    padding: 16px 22px;
    border-bottom: 1px solid #f5ede0;
    transition: background 0.15s;
}
.review-item:last-child { border-bottom: none; }
.review-item:hover { background: #fdf8f2; }

.review-item-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.reviewer-info { display: flex; align-items: center; gap: 10px; }

.reviewer-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5a1a, #c28b3c);
    color: white;
    font-weight: 700;
    font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.reviewer-name { font-size: 14px; font-weight: 600; color: #2d1a08; }
.reviewer-sub  { font-size: 12px; color: #a08060; }
.review-meta   { text-align: right; }

.review-stars {
    display: flex;
    gap: 2px;
    justify-content: flex-end;
    font-size: 15px;
    margin-bottom: 2px;
}

.review-date { font-size: 11px; color: #a08060; }

.review-menu-tag {
    display: inline-block;
    background: #fef3e2;
    color: #c28b3c;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 20px;
    margin-bottom: 6px;
}

.review-comment {
    font-size: 13px;
    color: #5a3e20;
    line-height: 1.6;
    font-style: italic;
    background: #fdf8f2;
    padding: 8px 12px;
    border-radius: 8px;
    border-left: 3px solid #ede0cc;
}
.review-comment.empty { color: #a08060; font-style: normal; }

.empty-state { text-align: center; padding: 60px 24px; color: #c0a080; }
.empty-state .icon { font-size: 40px; margin-bottom: 10px; }
.empty-state p { font-size: 14px; }

@media(max-width:768px){
  .sidebar{transform:translateX(-200px);transition:transform 0.25s;}
  .sidebar.open{transform:translateX(0);}
  .summary-grid{grid-template-columns:1fr 1fr;}
  .rating-overview{flex-direction:column;}
}
</style>
</head>
<body>

<!-- ========== SIDEBAR ========== -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">🍜 Seoullicious</div>
  <a href="home.php"    class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
  <a href="history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> History</a>
  <a href="review.php"  class="nav-item active"><i class="fas fa-star"></i> Review</a>
  <div class="sidebar-bottom">
    <a href="../auth/logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> Logout</a>
  </div>
</div>

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
      <div class="user-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
      <span><?= ucfirst($username) ?></span>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- PAGE TITLE -->
    <div class="page-title">
      <div class="page-title-icon"><i class="fas fa-star"></i></div>
      <div>
        <h3>Review Dashboard</h3>
        <p>Pantau semua ulasan pelanggan untuk menu dan restoran</p>
      </div>
    </div>

    <?php
    $total_menu_reviews  = $menu_stats_data ? array_sum(array_column($menu_stats_data,'total')) : 0;
    $total_resto_reviews = intval($resto_avg_row['total'] ?? 0);
    $total_all           = $total_menu_reviews + $total_resto_reviews;

    $sum_bintang_menu = 0;
    foreach($menu_stats_data as $ms){
        $sum_bintang_menu += $ms['avg_bintang'] * $ms['total'];
    }
    $avg_menu_global = $total_menu_reviews > 0 ? round($sum_bintang_menu / $total_menu_reviews, 1) : 0;
    $avg_resto       = floatval($resto_avg_row['avg_bintang'] ?? 0);
    ?>

    <!-- SUMMARY CARDS -->
    <div class="summary-grid">
      <div class="sum-card">
        <div class="sum-card-icon" style="background:#fef3e2;">⭐</div>
        <div>
          <div class="label">Total Semua Review</div>
          <div class="value gold"><?= $total_all ?></div>
        </div>
      </div>
      <div class="sum-card">
        <div class="sum-card-icon" style="background:#e8f8ee;">🍽️</div>
        <div>
          <div class="label">Rating Rata-rata Menu</div>
          <div class="value"><?= $avg_menu_global ?: '—' ?></div>
          <div class="stars-display">
            <?php for($i=1;$i<=5;$i++): ?>
              <span class="s <?= $i <= round($avg_menu_global) ? 'filled' : 'empty' ?>">★</span>
            <?php endfor; ?>
          </div>
        </div>
      </div>
      <div class="sum-card">
        <div class="sum-card-icon" style="background:#fde8e8;">🏪</div>
        <div>
          <div class="label">Rating Rata-rata Restoran</div>
          <div class="value"><?= $avg_resto ?: '—' ?></div>
          <div class="stars-display">
            <?php for($i=1;$i<=5;$i++): ?>
              <span class="s <?= $i <= round($avg_resto) ? 'filled' : 'empty' ?>">★</span>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- TABS -->
    <div class="tabs">
      <a href="?tab=restoran" class="tab-btn <?= $tab=='restoran'?'active':'' ?>">🏪 Review Restoran</a>
      <a href="?tab=menu"     class="tab-btn <?= $tab=='menu'?'active':'' ?>">🍽️ Review Menu</a>
    </div>

    <!-- ════ TAB: RESTORAN ════ -->
    <div class="section <?= $tab=='restoran'?'active':'' ?>">

      <div class="chart-card">
        <h3>📊 Distribusi Rating Restoran</h3>
        <?php
        $rt = max(1, intval($resto_avg_row['total']));
        $rs = [5=>$resto_avg_row['s5'],4=>$resto_avg_row['s4'],3=>$resto_avg_row['s3'],2=>$resto_avg_row['s2'],1=>$resto_avg_row['s1']];
        $bar_colors = [5=>'green',4=>'yellow',3=>'orange',2=>'orange',1=>'red'];
        ?>
        <div class="rating-overview">
          <div class="rating-big">
            <div class="num"><?= $avg_resto ?: '—' ?></div>
            <div class="stars">
              <?php for($i=1;$i<=5;$i++): ?>
                <span style="color:<?= $i<=round($avg_resto)?'#f5c518':'#ddd' ?>">★</span>
              <?php endfor; ?>
            </div>
            <div class="count"><?= $total_resto_reviews ?> ulasan</div>
          </div>
          <div class="rating-bars">
            <?php foreach([5,4,3,2,1] as $s): ?>
            <div class="bar-row">
              <div class="lbl"><?= $s ?>★</div>
              <div class="bar-track">
                <div class="bar-fill <?= $bar_colors[$s] ?>"
                     style="width:<?= $rt>0 ? round(($rs[$s]/$rt)*100) : 0 ?>%">
                </div>
              </div>
              <div class="cnt"><?= intval($rs[$s]) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="review-list-card">
        <div class="review-list-header">💬 Semua Ulasan Restoran</div>
        <?php
        $has_resto = false;
        while($rv = mysqli_fetch_assoc($resto_reviews)):
          $has_resto = true;
          $nama = $rv['nama_lengkap'] ?: $rv['username'] ?: 'Pelanggan';
          $initial = strtoupper(substr($nama,0,1));
        ?>
        <div class="review-item">
          <div class="review-item-top">
            <div class="reviewer-info">
              <div class="reviewer-avatar"><?= $initial ?></div>
              <div>
                <div class="reviewer-name"><?= htmlspecialchars($nama) ?></div>
                <div class="reviewer-sub">Order #<?= $rv['id_order'] ?> · <?= $rv['tanggal'] ? date('d M Y', strtotime($rv['tanggal'])) : '—' ?></div>
              </div>
            </div>
            <div class="review-meta">
              <div class="review-stars">
                <?php for($i=1;$i<=5;$i++): ?>
                  <span style="color:<?= $i<=$rv['bintang']?'#f5c518':'#ddd' ?>">★</span>
                <?php endfor; ?>
              </div>
              <div class="review-date"><?= date('d M Y H:i', strtotime($rv['created_at'])) ?></div>
            </div>
          </div>
          <?php if($rv['komentar']): ?>
          <div class="review-comment">"<?= htmlspecialchars($rv['komentar']) ?>"</div>
          <?php else: ?>
          <div class="review-comment empty">Tidak ada komentar.</div>
          <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php if(!$has_resto): ?>
        <div class="empty-state">
          <div class="icon">📭</div>
          <p>Belum ada ulasan restoran.</p>
        </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- ════ TAB: MENU ════ -->
    <div class="section <?= $tab=='menu'?'active':'' ?>">

      <?php if($menu_stats_data): ?>
      <div class="chart-card">
        <h3>🏆 Peringkat Menu Berdasarkan Rating</h3>
        <div class="menu-grid">
          <?php foreach($menu_stats_data as $idx => $ms):
            $rank = $idx + 1;
            $mt   = max(1, intval($ms['total']));
            $rank_cls = $rank===1?'rank-1':($rank===2?'rank-2':($rank===3?'rank-3':'rank-n'));
          ?>
          <div class="menu-stat-card">
            <div class="rank-badge <?= $rank_cls ?>"><?= $rank ?></div>
            <div class="menu-stat-info">
              <div class="menu-stat-name"><?= htmlspecialchars($ms['nama_menu']) ?></div>
              <div class="menu-mini-bars">
                <?php foreach([5,4,3,2,1] as $s): ?>
                <div class="mini-bar-row">
                  <div class="lbl"><?= $s ?></div>
                  <div class="mini-track">
                    <div class="mini-fill" style="width:<?= round(($ms['s'.$s]/$mt)*100) ?>%"></div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="menu-stat-avg">
              <div class="avg-num"><?= number_format($ms['avg_bintang'],1) ?></div>
              <div class="avg-star">★</div>
              <div class="avg-cnt"><?= $ms['total'] ?> ulasan</div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="review-list-card">
        <div class="review-list-header">💬 Semua Ulasan Menu</div>
        <?php
        $has_menu = false;
        while($rv = mysqli_fetch_assoc($menu_reviews)):
          $has_menu = true;
          $nama = $rv['nama_lengkap'] ?: $rv['username'] ?: 'Pelanggan';
          $initial = strtoupper(substr($nama,0,1));
        ?>
        <div class="review-item">
          <div class="review-item-top">
            <div class="reviewer-info">
              <div class="reviewer-avatar"><?= $initial ?></div>
              <div>
                <div class="reviewer-name"><?= htmlspecialchars($nama) ?></div>
                <div class="reviewer-sub">Order #<?= $rv['id_order'] ?> · <?= $rv['tanggal'] ? date('d M Y', strtotime($rv['tanggal'])) : '—' ?></div>
              </div>
            </div>
            <div class="review-meta">
              <div class="review-stars">
                <?php for($i=1;$i<=5;$i++): ?>
                  <span style="color:<?= $i<=$rv['bintang']?'#f5c518':'#ddd' ?>">★</span>
                <?php endfor; ?>
              </div>
              <div class="review-date"><?= date('d M Y H:i', strtotime($rv['created_at'])) ?></div>
            </div>
          </div>
          <div class="review-menu-tag">🍽️ <?= htmlspecialchars($rv['nama_menu']) ?></div>
          <?php if($rv['komentar']): ?>
          <div class="review-comment">"<?= htmlspecialchars($rv['komentar']) ?>"</div>
          <?php else: ?>
          <div class="review-comment empty">Tidak ada komentar.</div>
          <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php if(!$has_menu): ?>
        <div class="empty-state">
          <div class="icon">📭</div>
          <p>Belum ada ulasan menu.</p>
        </div>
        <?php endif; ?>
      </div>

    </div>

  </div><!-- /content -->
</div><!-- /main -->

<script>
document.getElementById("toggleSidebar").onclick = function(){
  document.getElementById("sidebar").classList.toggle("hide");
};
</script>
</body>
</html>