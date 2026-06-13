<?php
include "../config/koneksi.php";

$id_order = $_GET['id'];

// Ambil data order
$order = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT * FROM orders WHERE id_order='$id_order'
"));

// Ambil item-item di order ini (untuk review per menu)
$items = mysqli_query($koneksi, "
    SELECT oi.id_menu, m.nama_menu, oi.qty
    FROM order_items oi
    JOIN menu m ON oi.id_menu = m.id_menu
    WHERE oi.id_order = '$id_order'
");
$menu_list = [];
while($row = mysqli_fetch_assoc($items)){
    $menu_list[] = $row;
}

// Ambil id_user dari session (sesuaikan jika kamu pakai session)
session_start();
$id_user = $_SESSION['id_user'] ?? 1; // fallback ke 1 jika belum ada session

$success = '';
$error   = '';

// ===== PROSES SUBMIT =====
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $type = $_POST['review_type']; // 'menu' atau 'restoran'

    if($type === 'menu'){

        $all_ok = true;

        foreach($menu_list as $menu){
            $id_menu  = $menu['id_menu'];
            $bintang  = intval($_POST['bintang_menu_' . $id_menu] ?? 0);
            $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar_menu_' . $id_menu] ?? '');

            if($bintang < 1 || $bintang > 5){
                $error = 'Mohon beri rating bintang untuk semua menu.';
                $all_ok = false;
                break;
            }

            // Cek sudah review menu ini untuk order ini belum
            $cek = mysqli_fetch_assoc(mysqli_query($koneksi,"
                SELECT id_review FROM review_menu
                WHERE id_user='$id_user' AND id_menu='$id_menu' AND id_order='$id_order'
            "));

            if($cek){
                // Update
                mysqli_query($koneksi,"
                    UPDATE review_menu
                    SET bintang='$bintang', komentar='$komentar'
                    WHERE id_user='$id_user' AND id_menu='$id_menu' AND id_order='$id_order'
                ");
            } else {
                // Insert
                mysqli_query($koneksi,"
                    INSERT INTO review_menu (id_user, id_menu, id_order, bintang, komentar, created_at)
                    VALUES ('$id_user','$id_menu','$id_order','$bintang','$komentar', NOW())
                ");
            }
        }

        if($all_ok) $success = 'Review menu berhasil disimpan! Terima kasih 🙏';

    } elseif($type === 'restoran'){

        $bintang  = intval($_POST['bintang_resto'] ?? 0);
        $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar_resto'] ?? '');

        if($bintang < 1 || $bintang > 5){
            $error = 'Mohon beri rating bintang untuk restoran.';
        } else {
            // Cek sudah review restoran untuk order ini belum
            $cek = mysqli_fetch_assoc(mysqli_query($koneksi,"
                SELECT id_review FROM review_restoran
                WHERE id_user='$id_user' AND id_order='$id_order'
            "));

            if($cek){
                mysqli_query($koneksi,"
                    UPDATE review_restoran
                    SET bintang='$bintang', komentar='$komentar'
                    WHERE id_user='$id_user' AND id_order='$id_order'
                ");
            } else {
                mysqli_query($koneksi,"
                    INSERT INTO review_restoran (id_user, id_order, bintang, komentar, created_at)
                    VALUES ('$id_user','$id_order','$bintang','$komentar', NOW())
                ");
            }

            $success = 'Review restoran berhasil disimpan! Terima kasih 🙏';
        }
    }
}

// Ambil review yang sudah ada (jika ada)
$existing_resto = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM review_restoran WHERE id_user='$id_user' AND id_order='$id_order'
"));

$existing_menu = [];
foreach($menu_list as $menu){
    $id_menu = $menu['id_menu'];
    $row = mysqli_fetch_assoc(mysqli_query($koneksi,"
        SELECT * FROM review_menu WHERE id_user='$id_user' AND id_menu='$id_menu' AND id_order='$id_order'
    "));
    $existing_menu[$id_menu] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review - Seoullicious</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:#faf6f0;display:flex;height:100vh;overflow:hidden;}

/* ========== SIDEBAR ========== */
.sidebar{
  width:200px;min-width:200px;
  background:#1c0f00;
  display:flex;flex-direction:column;padding:24px 16px;
  height:100vh;position:relative;z-index:10;
  border-right:1px solid rgba(194,139,60,0.12);
  transition:margin-left 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
}
.sidebar.hide{margin-left:-200px;opacity:0;}
.sidebar-logo{color:#f5d080;font-size:19px;font-weight:600;margin-bottom:3px;padding-left:8px;letter-spacing:0.3px;font-family:'Cormorant Garamond',serif;}
.sidebar-logo-sub{font-size:9px;color:rgba(245,208,128,0.3);padding-left:8px;margin-bottom:32px;letter-spacing:1.5px;text-transform:uppercase;}
.nav-label{font-size:9px;color:rgba(255,240,200,0.25);letter-spacing:1.5px;text-transform:uppercase;padding-left:14px;margin-bottom:8px;}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;color:rgba(255,240,210,0.7);text-decoration:none;font-size:14px;margin-bottom:4px;transition:background 0.2s, color 0.2s, transform 0.15s;}
.nav-item:hover{background:rgba(255,255,255,0.1);color:#fff;transform:translateX(3px);}
.nav-item.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;box-shadow:0 4px 14px rgba(139,90,26,0.4);}
.nav-item i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}
.user-info{display:flex;align-items:center;gap:8px;padding:10px 12px;background:rgba(255,255,255,0.06);border-radius:12px;margin-bottom:8px;}
.user-av{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#8b5a1a,#c28b3c);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:800;flex-shrink:0;}
.user-av-name{font-size:12px;font-weight:600;color:#f5d080;}
.user-av-role{font-size:10px;color:rgba(255,240,200,0.4);}

/* ========== MAIN ========== */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}

/* ========== TOPBAR ========== */
.topbar{background:white;padding:16px 28px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e8ddd0;flex-shrink:0;box-shadow:0 2px 12px rgba(139,90,26,0.05);}
.topbar-left{display:flex;align-items:center;gap:14px;}
.toggle-btn{background:#f5f0e8;border:none;width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;}
.toggle-btn:hover{background:#ede4d4;}
.page-title-bar h2{font-size:18px;font-weight:700;color:#2d1a08;}
.page-title-bar p{font-size:12px;color:#a08060;margin-top:1px;}
.order-badge-top{background:#fff8ee;border:1px solid #f0d9a8;color:#8b5a1a;font-size:12px;font-weight:600;padding:6px 14px;border-radius:20px;}

/* ========== CONTENT ========== */
.content{flex:1;overflow-y:auto;padding:28px;display:flex;justify-content:center;}

/* ========== REVIEW CARD ========== */
.review-wrap{width:100%;max-width:480px;}

.review-header{
  background:linear-gradient(135deg,#1c0f00,#3d2008);
  border-radius:20px 20px 0 0;padding:28px;text-align:center;
  position:relative;overflow:hidden;
  border:1px solid rgba(194,139,60,0.2);
}
.review-header::before{content:'';position:absolute;right:-40px;top:-40px;width:160px;height:160px;background:radial-gradient(circle,rgba(194,139,60,0.2),transparent 70%);border-radius:50%;}
.review-header .rh-name{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:#f5d080;position:relative;z-index:1;}
.review-header .rh-sub{font-size:12px;color:rgba(255,240,200,0.5);margin-top:4px;position:relative;z-index:1;}
.review-header .rh-badge{display:inline-block;background:rgba(245,208,128,0.12);border:1px solid rgba(245,208,128,0.25);color:#f5d080;font-size:11px;font-weight:600;padding:5px 16px;border-radius:20px;margin-top:12px;position:relative;z-index:1;}

.review-body{background:white;border-radius:0 0 20px 20px;padding:24px;border:1px solid #ede0cc;border-top:none;}

/* ALERT */
.alert{padding:12px 16px;border-radius:12px;font-size:13px;margin-bottom:18px;font-weight:500;display:flex;align-items:center;gap:8px;}
.alert-success{background:#e8f9f0;color:#2e7d52;border-left:4px solid #34c975;}
.alert-error{background:#fdf0f0;color:#c0392b;border-left:4px solid #e74c3c;}

/* TABS */
.tabs{display:flex;background:#f5f0e8;border-radius:12px;padding:4px;margin-bottom:22px;gap:4px;}
.tab-btn{flex:1;padding:10px 5px;border:none;background:transparent;border-radius:9px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;color:#a08060;cursor:pointer;transition:all 0.25s;}
.tab-btn.active{background:white;color:#8b5a1a;box-shadow:0 2px 10px rgba(139,90,26,0.12);}

/* PANELS */
.tab-panel{display:none;}
.tab-panel.active{display:block;}

/* SECTION LABEL */
.section-label{font-size:13px;font-weight:600;color:#2d1a08;margin-bottom:14px;}

/* MENU REVIEW ITEM */
.menu-review-item{background:#faf6f0;border:1.5px solid #ede0cc;border-radius:14px;padding:16px;margin-bottom:14px;}
.menu-review-item .menu-name{font-size:14px;font-weight:700;color:#2d1a08;margin-bottom:10px;}

/* STAR RATING */
.star-group{display:flex;gap:6px;margin-bottom:10px;flex-direction:row-reverse;justify-content:flex-end;}
.star-group input[type="radio"]{display:none;}
.star-group label{font-size:26px;color:#ddd;cursor:pointer;transition:color 0.15s, transform 0.15s;user-select:none;}
.star-group label:hover,.star-group label:hover ~ label,.star-group input[type="radio"]:checked ~ label{color:#f5c518;}
.star-group label:hover{transform:scale(1.2);}

/* TEXTAREA */
textarea{width:100%;border:1.5px solid #e8ddd0;border-radius:10px;padding:10px 13px;font-family:'DM Sans',sans-serif;font-size:13px;resize:none;color:#2d1a08;outline:none;transition:border 0.2s;background:white;}
textarea:focus{border-color:#c28b3c;box-shadow:0 0 0 3px rgba(194,139,60,0.1);}
textarea::placeholder{color:#c0a880;}

/* SUBMIT BUTTON */
.btn-submit{width:100%;padding:13px;background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;border:none;border-radius:12px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;cursor:pointer;margin-top:6px;transition:opacity 0.2s, transform 0.15s;box-shadow:0 4px 16px rgba(139,90,26,0.3);}
.btn-submit:hover{opacity:0.88;transform:translateY(-2px);}

/* BACK LINK */
.back-link{display:block;text-align:center;margin-top:16px;font-size:13px;color:#a08060;text-decoration:none;transition:color 0.2s;}
.back-link:hover{color:#8b5a1a;}

/* DIVIDER */
.divider{border:none;border-top:1px dashed #e8ddd0;margin:18px 0;}

/* Already reviewed badge */
.reviewed-badge{display:inline-block;background:#e8f9f0;color:#2e7d52;font-size:11px;padding:3px 12px;border-radius:20px;font-weight:600;margin-bottom:10px;}

/* Resto block */
.resto-block{background:#faf6f0;border:1.5px solid #ede0cc;border-radius:14px;padding:16px;}
.resto-block .menu-name{font-size:14px;font-weight:700;color:#2d1a08;margin-bottom:10px;}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">Seoullicious</div>
  <div class="sidebar-logo-sub">Korean Food Experience</div>

  <div class="nav-label">Main Menu</div>
  <a href="home.php" class="nav-item"><i class="fas fa-house"></i> Home</a>
  <a href="pos.php" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
  <a href="history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> History</a>
  <a href="review_buka.php" class="nav-item active"><i class="fas fa-star"></i> Review</a>

  <div class="sidebar-bottom">
    <div class="user-info">
      <div class="user-av"><?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?></div>
      <div>
        <div class="user-av-name"><?php echo ucfirst($_SESSION['username'] ?? 'User'); ?></div>
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
      <button class="toggle-btn" id="toggleSidebar">&#9776;</button>
      <div class="page-title-bar">
        <h2>Tulis Review</h2>
        <p>Bagikan pengalaman Anda</p>
      </div>
    </div>
    <div class="order-badge-top">Order #<?= $id_order ?></div>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <div class="review-wrap">

      <!-- REVIEW HEADER CARD -->
      <div class="review-header">
        <div class="rh-name">Seoullicious</div>
        <div class="rh-sub">Cafe & Korean Food</div>
        <div class="rh-badge">Order #<?= $id_order ?></div>
      </div>

      <div class="review-body">

        <!-- ALERT -->
        <?php if($success): ?>
          <div class="alert alert-success"><i class="fas fa-circle-check"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if($error): ?>
          <div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> <?= $error ?></div>
        <?php endif; ?>

        <!-- TABS -->
        <div class="tabs">
          <button class="tab-btn active" onclick="switchTab('menu', this)">Review Menu</button>
          <button class="tab-btn" onclick="switchTab('restoran', this)">Review Restoran</button>
        </div>

        <!-- ===== TAB: REVIEW MENU ===== -->
        <div id="tab-menu" class="tab-panel active">
          <div class="section-label">Bagaimana makanan yang kamu pesan?</div>

          <form method="POST">
            <input type="hidden" name="review_type" value="menu">

            <?php foreach($menu_list as $menu):
              $id_menu = $menu['id_menu'];
              $exist   = $existing_menu[$id_menu] ?? null;
            ?>

            <div class="menu-review-item">
              <div class="menu-name">
                <?= htmlspecialchars($menu['nama_menu']) ?>
                <small style="color:#a08060;font-weight:400;"> &times;<?= $menu['qty'] ?></small>
              </div>

              <?php if($exist): ?>
                <div class="reviewed-badge">Sudah direview</div>
              <?php endif; ?>

              <div class="star-group">
                <?php for($s = 5; $s >= 1; $s--): ?>
                  <input type="radio" name="bintang_menu_<?= $id_menu ?>" id="star_<?= $id_menu ?>_<?= $s ?>" value="<?= $s ?>" <?= ($exist && $exist['bintang'] == $s) ? 'checked' : '' ?>>
                  <label for="star_<?= $id_menu ?>_<?= $s ?>">&#9733;</label>
                <?php endfor; ?>
              </div>

              <textarea name="komentar_menu_<?= $id_menu ?>" rows="2" placeholder="Tulis komentar... (opsional)"><?= htmlspecialchars($exist['komentar'] ?? '') ?></textarea>
            </div>

            <?php endforeach; ?>

            <button type="submit" class="btn-submit">Kirim Review Menu</button>
          </form>
        </div>

        <!-- ===== TAB: REVIEW RESTORAN ===== -->
        <div id="tab-restoran" class="tab-panel">
          <div class="section-label">Bagaimana pengalaman kamu di Seoullicious?</div>

          <form method="POST">
            <input type="hidden" name="review_type" value="restoran">

            <div class="resto-block">
              <div class="menu-name">Penilaian Keseluruhan Restoran</div>

              <?php if($existing_resto): ?>
                <div class="reviewed-badge">Sudah direview</div>
              <?php endif; ?>

              <div class="star-group">
                <?php for($s = 5; $s >= 1; $s--): ?>
                  <input type="radio" name="bintang_resto" id="star_resto_<?= $s ?>" value="<?= $s ?>" <?= ($existing_resto && $existing_resto['bintang'] == $s) ? 'checked' : '' ?>>
                  <label for="star_resto_<?= $s ?>">&#9733;</label>
                <?php endfor; ?>
              </div>

              <textarea name="komentar_resto" rows="3" placeholder="Ceritakan pengalamanmu... (opsional)"><?= htmlspecialchars($existing_resto['komentar'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-submit" style="margin-top:16px;">Kirim Review Restoran</button>
          </form>
        </div>

        <hr class="divider">
        <a href="receipt.php?id=<?= $id_order ?>" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Receipt</a>

      </div><!-- /review-body -->
    </div><!-- /review-wrap -->
  </div><!-- /content -->
</div><!-- /main -->

<script>
document.getElementById("toggleSidebar").onclick = function(){
  document.getElementById("sidebar").classList.toggle("hide");
};

function switchTab(name, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
}
</script>

</body>
</html>