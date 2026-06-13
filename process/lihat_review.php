<?php
session_start();
include "../config/koneksi.php";

// Statistik
$avg_resto = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT AVG(bintang) as avg, COUNT(*) as total FROM review_restoran"));
$avg_menu  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT AVG(bintang) as avg, COUNT(*) as total FROM review_menu"));

// Semua review menu
$menu_reviews = mysqli_query($koneksi,"
  SELECT rm.*, m.nama_menu, m.gambar, u.username
  FROM review_menu rm
  JOIN menu m ON rm.id_menu = m.id_menu
  JOIN user u ON rm.id_user = u.id_user
  ORDER BY rm.created_at DESC
");

// Semua review restoran
$resto_reviews = mysqli_query($koneksi,"
  SELECT rr.*, u.username, o.tanggal, o.total
  FROM review_restoran rr
  JOIN user u ON rr.id_user = u.id_user
  JOIN orders o ON rr.id_order = o.id_order
  ORDER BY rr.created_at DESC
");

// Rating per menu
$rating_menu = mysqli_query($koneksi,"
  SELECT m.nama_menu, m.gambar, AVG(rm.bintang) as avg_bintang, COUNT(rm.id_review) as total_review
  FROM menu m
  JOIN review_menu rm ON m.id_menu = rm.id_menu
  GROUP BY m.id_menu
  ORDER BY avg_bintang DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Review Management - Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#f5f0e8;display:flex;min-height:100vh;}
.sidebar{
  width:220px;min-width:220px;background:#2d1a08;
  display:flex;flex-direction:column;padding:24px 16px;
  height:100vh;position:fixed;z-index:10;
}
.sidebar-logo{color:#f5d080;font-size:18px;font-weight:800;margin-bottom:32px;padding-left:8px;font-family:'Playfair Display',serif;}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;color:rgba(255,240,210,0.6);text-decoration:none;font-size:14px;margin-bottom:4px;transition:0.2s;}
.nav-item:hover{background:rgba(255,255,255,0.08);color:#fff;}
.nav-item.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;}
.nav-item i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}
.main{margin-left:220px;flex:1;padding:28px;}

/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
.stat-card{background:white;border-radius:16px;padding:20px;border:1px solid #ede0cc;text-align:center;}
.stat-card .icon{font-size:28px;margin-bottom:8px;}
.stat-card .val{font-size:24px;font-weight:800;color:#2d1a08;font-family:'Playfair Display',serif;}
.stat-card .lbl{font-size:12px;color:#a08060;margin-top:4px;}
.stars{color:#f5c518;font-size:13px;}

/* TABS */
.tab-bar{display:flex;gap:8px;border-bottom:2px solid #e8ddd0;margin-bottom:24px;}
.tab-btn{padding:10px 22px;border:none;background:transparent;font-size:14px;font-weight:600;color:#a08060;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:0.2s;border-radius:8px 8px 0 0;}
.tab-btn.active{color:#8b5a1a;border-bottom-color:#c28b3c;}
.tab-content{display:none;}
.tab-content.active{display:block;}

/* TABLE */
.review-table{width:100%;border-collapse:collapse;background:white;border-radius:16px;overflow:hidden;border:1px solid #ede0cc;}
.review-table th{background:#f5f0e8;padding:13px 16px;text-align:left;font-size:12px;font-weight:700;color:#8b5a1a;text-transform:uppercase;letter-spacing:0.5px;}
.review-table td{padding:13px 16px;border-bottom:1px solid #f5ede0;font-size:13px;color:#2d1a08;vertical-align:middle;}
.review-table tr:last-child td{border-bottom:none;}
.review-table tr:hover td{background:#faf8f5;}
.menu-cell{display:flex;align-items:center;gap:10px;}
.menu-cell img{width:36px;height:36px;border-radius:8px;object-fit:cover;}
.badge-stars{color:#f5c518;font-size:12px;}
.btn-hapus{background:#fee2e2;color:#dc2626;border:none;padding:5px 12px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;transition:0.2s;}
.btn-hapus:hover{background:#dc2626;color:white;}

/* TOP MENU RATING */
.rating-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:24px;}
.rating-card{background:white;border-radius:14px;padding:16px;border:1px solid #ede0cc;text-align:center;}
.rating-card img{width:64px;height:64px;border-radius:12px;object-fit:cover;margin-bottom:10px;}
.rating-card h4{font-size:13px;font-weight:700;color:#2d1a08;margin-bottom:4px;}
.rating-card .avg{font-size:22px;font-weight:800;color:#c28b3c;font-family:'Playfair Display',serif;}
.rating-card .count{font-size:11px;color:#a08060;}

.page-title{font-family:'Playfair Display',serif;font-size:24px;color:#2d1a08;margin-bottom:24px;}
.section-title{font-size:15px;font-weight:700;color:#2d1a08;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
</style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-logo">🍜 Seoullicious</div>
  <a href="dashboard.php" class="nav-item"><i class="fas fa-gauge"></i> Dashboard</a>
  <a href="menu.php" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
  <a href="order_list.php" class="nav-item"><i class="fas fa-list"></i> Order List</a>
  <a href="history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> History</a>
  <a href="lihat_review.php" class="nav-item active"><i class="fas fa-star"></i> Review</a>
  <div class="sidebar-bottom">
    <a href="../auth/logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> Logout</a>
  </div>
</div>

<div class="main">
  <div class="page-title">⭐ Review Management</div>

  <!-- STATS -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="icon">🏪</div>
      <div class="val"><?= round($avg_resto['avg'],1) ?: '-' ?></div>
      <div class="stars"><?= str_repeat('★',round($avg_resto['avg']??0)) . str_repeat('☆',5-round($avg_resto['avg']??0)) ?></div>
      <div class="lbl">Avg Rating Restoran</div>
    </div>
    <div class="stat-card">
      <div class="icon">💬</div>
      <div class="val"><?= $avg_resto['total'] ?></div>
      <div class="lbl">Total Review Restoran</div>
    </div>
    <div class="stat-card">
      <div class="icon">🍽️</div>
      <div class="val"><?= round($avg_menu['avg'],1) ?: '-' ?></div>
      <div class="stars"><?= str_repeat('★',round($avg_menu['avg']??0)) . str_repeat('☆',5-round($avg_menu['avg']??0)) ?></div>
      <div class="lbl">Avg Rating Menu</div>
    </div>
    <div class="stat-card">
      <div class="icon">📝</div>
      <div class="val"><?= $avg_menu['total'] ?></div>
      <div class="lbl">Total Review Menu</div>
    </div>
  </div>

  <!-- TABS -->
  <div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('menu',this)">🍽️ Review Menu</button>
    <button class="tab-btn" onclick="switchTab('restoran',this)">🏪 Review Restoran</button>
    <button class="tab-btn" onclick="switchTab('ranking',this)">🏆 Ranking Menu</button>
  </div>

  <!-- TAB MENU -->
  <div class="tab-content active" id="tab-menu">
    <table class="review-table">
      <thead>
        <tr>
          <th>Menu</th>
          <th>User</th>
          <th>Bintang</th>
          <th>Komentar</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($rv = mysqli_fetch_assoc($menu_reviews)): ?>
        <tr>
          <td>
            <div class="menu-cell">
              <img src="../assets/<?= htmlspecialchars($rv['gambar']) ?>" onerror="this.src='../assets/default.png'">
              <?= htmlspecialchars($rv['nama_menu']) ?>
            </div>
          </td>
          <td><?= htmlspecialchars($rv['username']) ?></td>
          <td><span class="badge-stars"><?= str_repeat('★',$rv['bintang']) ?></span> <?= $rv['bintang'] ?>/5</td>
          <td><?= htmlspecialchars($rv['komentar'] ?? '-') ?></td>
          <td><?= date('d M Y', strtotime($rv['created_at'])) ?></td>
          <td><button class="btn-hapus" onclick="hapusReview('menu',<?= $rv['id_review'] ?>)"><i class="fas fa-trash"></i></button></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- TAB RESTORAN -->
  <div class="tab-content" id="tab-restoran">
    <table class="review-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Order #</th>
          <th>Bintang</th>
          <th>Komentar</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($rv = mysqli_fetch_assoc($resto_reviews)): ?>
        <tr>
          <td><?= htmlspecialchars($rv['username']) ?></td>
          <td>#<?= $rv['id_order'] ?></td>
          <td><span class="badge-stars"><?= str_repeat('★',$rv['bintang']) ?></span> <?= $rv['bintang'] ?>/5</td>
          <td><?= htmlspecialchars($rv['komentar'] ?? '-') ?></td>
          <td><?= date('d M Y', strtotime($rv['created_at'])) ?></td>
          <td><button class="btn-hapus" onclick="hapusReview('restoran',<?= $rv['id_review'] ?>)"><i class="fas fa-trash"></i></button></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- TAB RANKING -->
  <div class="tab-content" id="tab-ranking">
    <div class="section-title"><i class="fas fa-trophy" style="color:#c28b3c"></i> Menu Terbaik Berdasarkan Rating</div>
    <div class="rating-grid">
      <?php while($rm = mysqli_fetch_assoc($rating_menu)): ?>
      <div class="rating-card">
        <img src="../assets/<?= htmlspecialchars($rm['gambar']) ?>" onerror="this.src='../assets/default.png'">
        <h4><?= htmlspecialchars($rm['nama_menu']) ?></h4>
        <div class="avg"><?= round($rm['avg_bintang'],1) ?></div>
        <div class="stars"><?= str_repeat('★',round($rm['avg_bintang'])) . str_repeat('☆',5-round($rm['avg_bintang'])) ?></div>
        <div class="count"><?= $rm['total_review'] ?> ulasan</div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<script>
function switchTab(tab, el){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-'+tab).classList.add('active');
}

function hapusReview(type, id){
  if(!confirm('Hapus review ini?')) return;
  fetch('../process/hapus_review.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`type=${type}&id=${id}`
  }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
</body>
</html>
