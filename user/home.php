<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username  = $_SESSION['username'] ?? 'User';
$id_user   = $_SESSION['id_user'] ?? 0;

// ── Language dari DB (user_settings) ──────────────────────────────────────
$set_row = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM user_settings WHERE user_id='$id_user' LIMIT 1"
));
$lang = $set_row['language'] ?? 'id';
// Override jika ada POST language switch
if(isset($_POST['switch_lang'])){
    $nl = $_POST['switch_lang'] === 'en' ? 'en' : 'id';
    if($set_row){
        mysqli_query($koneksi,"UPDATE user_settings SET language='$nl' WHERE user_id='$id_user'");
    } else {
        mysqli_query($koneksi,"INSERT INTO user_settings (user_id,language) VALUES ('$id_user','$nl')");
    }
    $lang = $nl;
}

// ── i18n strings ───────────────────────────────────────────────────────────
$t = $lang === 'en' ? [
    'page_title'   => 'Seoullicious — Home',
    'main_menu'    => 'Main Menu',
    'nav_home'     => 'Home',
    'nav_menu'     => 'Menu',
    'nav_history'  => 'History',
    'nav_review'   => 'Review',
    'nav_logout'   => 'Logout',
    'search_ph'    => 'Search menu...',
    'new_order'    => '+ New Order',
    'tag_line'     => 'Korean Food Experience',
    'hero_line1'   => 'Taste the',
    'hero_line2'   => 'Authentic',
    'hero_line3'   => 'Korean Flavors',
    'hero_desc'    => 'Enjoy the taste of real Korean cuisine — from spicy tteokbokki to warm ramen that keeps you coming back for more.',
    'view_menu'    => 'View Menu',
    'order_hist'   => 'Order History',
    'stat_menu'    => 'Menu Items',
    'stat_cust'    => 'Happy Customers',
    'stat_rating'  => 'Rating',
    'badge_today'  => '#1 Today',
    'best_seller'  => 'Best Seller',
    'fast_order'   => 'Fast Order',
    'ready_min'    => 'Ready in 5 min',
    'card1_badge'  => 'Since 2019',
    'card1_name'   => 'Our Story',
    'card1_desc'   => 'Born from a deep love for Korean culture, Seoullicious brings authentic Seoul flavors to your table',
    'card2_badge'  => 'Original Recipe',
    'card2_name'   => 'Imported Korean Spices',
    'card2_desc'   => 'Every dish uses premium ingredients directly from Korea — gochujang, doenjang, and traditional spices',
    'card3_badge'  => '10 Years Experience',
    'card3_name'   => 'Trained Chefs',
    'card3_desc'   => 'Our chefs trained directly in Seoul, bringing authentic culinary mastery passed down through generations',
    'card4_badge'  => '500+ Five-Star Reviews',
    'card4_name'   => 'Loved by Customers',
    'card4_desc'   => 'Over 500 loyal customers have made Seoullicious their favourite Korean food destination in the city',
    'best_sellers' => 'Best',
    'best_sell2'   => 'Sellers',
    'see_all'      => 'All',
    'add_order'    => '+ Add to Order',
    'cta_title'    => 'Ready to experience Korean Food?',
    'cta_sub'      => 'Start ordering now — fast, easy, and delicious!',
    'cta_btn'      => 'Start Ordering Now',
    'edit_profile' => 'Edit Profile',
    'display_name' => 'Display Name',
    'upload_photo' => 'Upload Photo',
    'save'         => 'Save Changes',
    'cancel'       => 'Cancel',
    'lang_label'   => 'Language',
    'profile_btn'  => 'Edit Profile',
] : [
    'page_title'   => 'Seoullicious — Beranda',
    'main_menu'    => 'Menu Utama',
    'nav_home'     => 'Beranda',
    'nav_menu'     => 'Menu',
    'nav_history'  => 'Riwayat',
    'nav_review'   => 'Ulasan',
    'nav_logout'   => 'Keluar',
    'search_ph'    => 'Cari menu...',
    'new_order'    => '+ Pesanan Baru',
    'tag_line'     => 'Pengalaman Makanan Korea',
    'hero_line1'   => 'Rasakan',
    'hero_line2'   => 'Kelezatan',
    'hero_line3'   => 'Masakan Korea',
    'hero_desc'    => 'Nikmati cita rasa masakan Korea yang sesungguhnya — dari tteokbokki pedas hingga ramen hangat yang bikin ketagihan.',
    'view_menu'    => 'Lihat Menu',
    'order_hist'   => 'Riwayat Pesanan',
    'stat_menu'    => 'Pilihan Menu',
    'stat_cust'    => 'Pelanggan Puas',
    'stat_rating'  => 'Rating',
    'badge_today'  => '#1 Hari Ini',
    'best_seller'  => 'Terlaris',
    'fast_order'   => 'Pesanan Cepat',
    'ready_min'    => 'Siap dalam 5 menit',
    'card1_badge'  => 'Sejak 2019',
    'card1_name'   => 'Cerita Kami',
    'card1_desc'   => 'Lahir dari kecintaan mendalam terhadap budaya Korea, Seoullicious hadir membawa cita rasa autentik Seoul ke meja makan Anda',
    'card2_badge'  => 'Resep Asli',
    'card2_name'   => 'Bumbu Impor Korea',
    'card2_desc'   => 'Setiap hidangan menggunakan bahan pilihan langsung dari Korea — gochujang, doenjang, dan rempah tradisional asli',
    'card3_badge'  => 'Pengalaman 10 Tahun',
    'card3_name'   => 'Chef Terlatih',
    'card3_desc'   => 'Tim chef kami terlatih langsung di Seoul, membawa keahlian memasak autentik yang diturunkan dari generasi ke generasi',
    'card4_badge'  => '500+ Ulasan Bintang 5',
    'card4_name'   => 'Dicintai Pelanggan',
    'card4_desc'   => 'Lebih dari 500 pelanggan setia menjadikan Seoullicious destinasi kuliner Korea favorit mereka di kota ini',
    'best_sellers' => 'Menu',
    'best_sell2'   => 'Terlaris',
    'see_all'      => 'Lihat Semua',
    'add_order'    => '+ Tambah Pesanan',
    'cta_title'    => 'Siap menikmati Makanan Korea?',
    'cta_sub'      => 'Mulai pesan sekarang — cepat, mudah, dan lezat!',
    'cta_btn'      => 'Mulai Pesan Sekarang',
    'edit_profile' => 'Edit Profil',
    'display_name' => 'Nama Tampilan',
    'upload_photo' => 'Unggah Foto',
    'save'         => 'Simpan Perubahan',
    'cancel'       => 'Batal',
    'lang_label'   => 'Bahasa',
    'profile_btn'  => 'Edit Profil',
];

// ── Best-seller query ──────────────────────────────────────────────────────
$top_query = mysqli_query($koneksi, "
    SELECT m.*, COUNT(oi.id_item) as total_order
    FROM menu m
    JOIN order_items oi ON m.id_menu = oi.id_menu
    GROUP BY m.id_menu
    ORDER BY total_order DESC
    LIMIT 4
");
$top_menus = [];
while($row = mysqli_fetch_assoc($top_query)) { $top_menus[] = $row; }

// ── Greeting (time-based + special days) ──────────────────────────────────
$jam     = (int)date('H');
$today   = date('m-d');
$special = null;

$special_days = [
    '01-01' => ($lang==='en') ? 'Happy New Year!' : 'Selamat Tahun Baru!',
    '08-17' => ($lang==='en') ? 'Happy Indonesian Independence Day!' : 'Selamat HUT RI!',
    '12-25' => ($lang==='en') ? 'Merry Christmas!' : 'Selamat Natal!',
    '12-31' => ($lang==='en') ? 'Happy New Year\'s Eve!' : 'Selamat Malam Tahun Baru!',
    '02-14' => ($lang==='en') ? 'Happy Valentine\'s Day!' : 'Selamat Hari Valentine!',
];
if(isset($special_days[$today])) $special = $special_days[$today];

if($lang === 'en'){
    if($jam>=5&&$jam<12)      $greeting = "Good Morning";
    elseif($jam>=12&&$jam<17) $greeting = "Good Afternoon";
    elseif($jam>=17&&$jam<21) $greeting = "Good Evening";
    else                       $greeting = "Good Night";
} else {
    if($jam>=5&&$jam<12)      $greeting = "Selamat Pagi";
    elseif($jam>=12&&$jam<17) $greeting = "Selamat Siang";
    elseif($jam>=17&&$jam<21) $greeting = "Selamat Sore";
    else                       $greeting = "Selamat Malam";
}

// ── Load saved profile photo + display name ────────────────────────────────
// We store display_name and photo_path in user_settings extra columns.
// If columns don't exist yet we gracefully fall back.
$display_name  = $set_row['display_name'] ?? $username;
$photo_path    = $set_row['photo_path']   ?? '';
if(empty($display_name)) $display_name = $username;

// ── Handle AJAX profile-save (POST) ───────────────────────────────────────
if(isset($_POST['action']) && $_POST['action']==='save_profile'){
    header('Content-Type: application/json');
    if(!$id_user){ echo json_encode(['ok'=>false,'msg'=>'not logged in']); exit; }

    $new_name = mysqli_real_escape_string($koneksi, trim($_POST['display_name'] ?? ''));
    if(empty($new_name)) $new_name = mysqli_real_escape_string($koneksi, $username);
    $photo_saved = $photo_path;

    // Ensure columns exist (safe for MySQL 5.x and 8.x)
    $cols = mysqli_query($koneksi,"SHOW COLUMNS FROM user_settings");
    $existing = [];
    while($c = mysqli_fetch_assoc($cols)) $existing[] = $c['Field'];
    if(!in_array('display_name',$existing))
        mysqli_query($koneksi,"ALTER TABLE user_settings ADD COLUMN display_name VARCHAR(80) DEFAULT NULL");
    if(!in_array('photo_path',$existing))
        mysqli_query($koneksi,"ALTER TABLE user_settings ADD COLUMN photo_path VARCHAR(200) DEFAULT NULL");

    // Upload photo
    if(!empty($_FILES['profile_photo']['tmp_name']) && $_FILES['profile_photo']['error']===0){
        $ext  = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if(in_array($ext,$allowed)){
            $fname = "profile_{$id_user}_".time().".$ext";
            $dir   = __DIR__."/../upload/profiles/";
            if(!is_dir($dir)) mkdir($dir,0755,true);
            if(move_uploaded_file($_FILES['profile_photo']['tmp_name'], $dir.$fname)){
                $photo_saved = "profiles/$fname";
            }
        }
    }

    // Check again if row exists (re-query after potential INSERT by lang switch)
    $chk = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT id FROM user_settings WHERE user_id='$id_user' LIMIT 1"));
    if($chk){
        mysqli_query($koneksi,"UPDATE user_settings SET display_name='$new_name', photo_path='$photo_saved' WHERE user_id='$id_user'");
    } else {
        mysqli_query($koneksi,"INSERT INTO user_settings (user_id,language,display_name,photo_path) VALUES ('$id_user','$lang','$new_name','$photo_saved')");
    }

    $display_name = $new_name;
    $photo_path   = $photo_saved;
    echo json_encode(['ok'=>true,'name'=>$display_name,'photo'=>$photo_path]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $t['page_title'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/seoullicious_shared.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:#faf6f0;display:flex;height:100vh;overflow:hidden;}

/* ════════════════ SIDEBAR ════════════════ */
.sidebar{
  width:200px;min-width:200px;background:#1c0f00;
  display:flex;flex-direction:column;padding:24px 16px;
  height:100vh;position:relative;z-index:10;
  border-right:1px solid rgba(194,139,60,0.12);
  transition:margin-left 0.35s cubic-bezier(0.4,0,0.2,1),opacity 0.3s ease;
}
.sidebar.hide{margin-left:-200px;opacity:0;}
.sidebar-logo{color:#f5d080;font-size:19px;font-weight:600;margin-bottom:3px;padding-left:8px;font-family:'Cormorant Garamond',serif;}
.sidebar-logo-sub{font-size:9px;color:rgba(245,208,128,0.3);padding-left:8px;margin-bottom:32px;letter-spacing:1.5px;text-transform:uppercase;}
.nav-label{font-size:9px;color:rgba(255,240,200,0.25);letter-spacing:1.5px;text-transform:uppercase;padding-left:14px;margin-bottom:8px;}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;color:rgba(255,240,210,0.7);text-decoration:none;font-size:14px;margin-bottom:4px;transition:background 0.2s,color 0.2s,transform 0.15s;}
.nav-item:hover{background:rgba(255,255,255,0.1);color:#fff;transform:translateX(3px);}
.nav-item.active{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;box-shadow:0 4px 14px rgba(139,90,26,0.4);}
.nav-item i{width:18px;text-align:center;}
.sidebar-bottom{margin-top:auto;}

/* User card — clickable */
.user-info{
  display:flex;align-items:center;gap:8px;
  padding:10px 12px;background:rgba(255,255,255,0.06);
  border-radius:12px;margin-bottom:8px;
  cursor:pointer;transition:background 0.2s;position:relative;
}
.user-info:hover{background:rgba(255,255,255,0.12);}
.user-info-edit{
  position:absolute;right:8px;top:50%;transform:translateY(-50%);
  color:rgba(245,208,128,0.35);font-size:11px;transition:color 0.2s;
}
.user-info:hover .user-info-edit{color:#f5d080;}
.user-av{
  width:34px;height:34px;border-radius:10px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  display:flex;align-items:center;justify-content:center;
  color:white;font-size:13px;font-weight:800;flex-shrink:0;
  overflow:hidden;
}
.user-av img{width:100%;height:100%;object-fit:cover;border-radius:10px;}
.user-av-name{font-size:12px;font-weight:600;color:#f5d080;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

/* ════════════════ MAIN ════════════════ */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}

/* ════════════════ TOPBAR ════════════════ */
.topbar{background:white;padding:16px 28px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e8ddd0;flex-shrink:0;animation:fadeDown 0.5s ease both;box-shadow:0 2px 12px rgba(139,90,26,0.05);}
@keyframes fadeDown{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);}}
.topbar-left{display:flex;align-items:center;gap:14px;}
.toggle-btn{background:#f5f0e8;border:none;width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:background 0.2s,transform 0.15s;}
.toggle-btn:hover{background:#ede4d4;transform:scale(1.05);}
.greeting h2{font-size:18px;font-weight:700;color:#2d1a08;}
.greeting p{font-size:12px;color:#a08060;margin-top:1px;}
.special-day{font-size:11px;color:#c28b3c;font-weight:600;margin-top:2px;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.search-box{display:flex;align-items:center;gap:8px;background:#f5f0e8;border-radius:10px;padding:8px 14px;border:1.5px solid transparent;transition:border-color 0.2s,box-shadow 0.2s;}
.search-box:focus-within{border-color:#c28b3c;box-shadow:0 0 0 3px rgba(194,139,60,0.12);}
.search-box input{border:none;background:transparent;outline:none;font-size:14px;width:160px;color:#2d1a08;font-family:inherit;}
.search-box i{color:#a08060;font-size:13px;}

/* Language switcher */
.lang-switcher{display:flex;background:#f5f0e8;border-radius:10px;padding:3px;gap:2px;border:1px solid #e0d0bc;}
.lang-btn{background:transparent;border:none;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;color:#a08060;cursor:pointer;transition:all 0.2s;font-family:inherit;}
.lang-btn.active{background:white;color:#8b5a1a;box-shadow:0 1px 4px rgba(139,90,26,0.15);}

.order-btn{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;position:relative;overflow:hidden;transition:opacity 0.2s,transform 0.15s;}
.order-btn:hover{opacity:0.88;transform:translateY(-1px);}

/* ════════════════ CONTENT ════════════════ */
.content{flex:1;overflow-y:auto;padding:24px 28px;scroll-behavior:smooth;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

/* ════════════════ HERO ════════════════ */
.hero{display:flex;align-items:stretch;margin-bottom:28px;animation:fadeUp 0.6s ease 0.15s both;border-radius:24px;overflow:hidden;border:1px solid #ded0b8;min-height:300px;}
.hero-left{flex:1;position:relative;padding:36px;background-image:url('../assets/bg-pos.jpg');background-size:cover;background-position:center;display:flex;flex-direction:column;justify-content:center;}
.hero-left::before{content:'';position:absolute;inset:0;background:rgba(250,246,240,0.83);backdrop-filter:blur(1px);pointer-events:none;z-index:0;}
.hero-left>*{position:relative;z-index:1;}
.hero-tag{display:inline-flex;align-items:center;gap:6px;background:#fff8ee;border:1px solid #f0d9a8;border-radius:20px;padding:5px 14px;font-size:11px;font-weight:600;color:#8b5a1a;margin-bottom:18px;}
.hero-tag-dot{width:7px;height:7px;border-radius:50%;background:#c28b3c;animation:pulse 1.8s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(0.7);}}
.hero-title{font-family:'Cormorant Garamond',serif;font-size:40px;font-weight:900;color:#2d1a08;line-height:1.1;margin-bottom:14px;}
.hero-title .accent{color:#c28b3c;}
.hero-title .dark{color:#8b5a1a;}
.hero-desc{font-size:14px;color:#a08060;line-height:1.75;margin-bottom:24px;max-width:380px;}
.hero-btns{display:flex;align-items:center;gap:12px;margin-bottom:28px;}
.btn-primary{background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;border:none;border-radius:12px;padding:13px 26px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 6px 20px rgba(139,90,26,0.35);transition:transform 0.2s,box-shadow 0.2s;font-family:inherit;}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(139,90,26,0.45);}
.btn-outline{background:rgba(255,255,255,0.75);color:#8b5a1a;border:1.5px solid #c28b3c;border-radius:12px;padding:12px 22px;font-size:14px;font-weight:600;cursor:pointer;transition:background 0.25s,color 0.25s,transform 0.2s;font-family:inherit;backdrop-filter:blur(4px);}
.btn-outline:hover{background:#8b5a1a;color:white;transform:translateY(-3px);}
.hero-stats{display:flex;align-items:center;}
.hstat{text-align:center;padding:0 20px;}
.hstat:first-child{padding-left:0;}
.hstat-num{font-size:26px;font-weight:800;color:#2d1a08;line-height:1;}
.hstat-label{font-size:11px;color:#a08060;margin-top:4px;font-weight:500;}
.hstat-div{width:1px;height:36px;background:#e8ddd0;}
.hero-right{position:relative;flex-shrink:0;width:320px;background:linear-gradient(145deg,#1c0f00,#3d2008,#5a3010);display:flex;align-items:center;justify-content:center;overflow:hidden;}
.hero-right::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 50% 55%,rgba(194,139,60,0.22) 0%,transparent 65%);z-index:0;}
.hero-right-inner{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:32px;}
.food-circle{width:220px;height:220px;border-radius:50%;background:linear-gradient(135deg,#1c0f00,#5a3010);display:flex;align-items:center;justify-content:center;position:relative;box-shadow:0 20px 50px rgba(0,0,0,0.5),0 0 0 6px rgba(194,139,60,0.15);animation:float-main 3.5s ease-in-out infinite;}
@keyframes float-main{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.food-circle::before{content:'';position:absolute;inset:-10px;border-radius:50%;border:2px dashed rgba(194,139,60,0.3);animation:spin 22s linear infinite;}
@keyframes spin{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
.badge-hot{position:absolute;top:16px;right:16px;background:linear-gradient(135deg,#ef4444,#f97316);color:white;font-size:10px;font-weight:800;padding:8px 14px;border-radius:12px;box-shadow:0 4px 14px rgba(239,68,68,0.45);z-index:2;animation:badge-pop 0.5s cubic-bezier(0.34,1.56,0.64,1) 1s both;}
@keyframes badge-pop{from{opacity:0;transform:scale(0.5);}to{opacity:1;transform:scale(1);}}
.float-card{position:absolute;background:white;border-radius:14px;padding:10px 14px;box-shadow:0 8px 28px rgba(0,0,0,0.2);display:flex;align-items:center;gap:8px;z-index:2;}
.float-card.left{bottom:24px;left:16px;animation:badge-pop 0.5s cubic-bezier(0.34,1.56,0.64,1) 1.2s both,float-card-l 3.5s ease-in-out 1.7s infinite;}
.float-card.top{top:24px;left:16px;animation:badge-pop 0.5s cubic-bezier(0.34,1.56,0.64,1) 1.4s both,float-card-t 3.5s ease-in-out 1.9s infinite;}
@keyframes float-card-l{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
@keyframes float-card-t{0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);}}
.fc-icon{font-size:20px;}
.fc-b{font-size:12px;font-weight:700;color:#2d1a08;display:block;}
.fc-s{font-size:10px;color:#a08060;}
.fc-dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:#22c55e;margin-right:4px;animation:pulse 1.5s ease-in-out infinite;}

/* ════════════════ PROMO CARDS ════════════════ */
.feats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;}
.feat{background:white;border-radius:18px;border:1.5px solid #ede0cc;text-align:left;animation:fadeUp 0.5s ease both;cursor:default;transition:transform 0.22s ease,box-shadow 0.22s ease,border-color 0.22s ease;position:relative;overflow:hidden;}
.feat:nth-child(1){animation-delay:0.4s;}
.feat:nth-child(2){animation-delay:0.52s;}
.feat:nth-child(3){animation-delay:0.64s;}
.feat:nth-child(4){animation-delay:0.76s;}
.feat:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(139,90,26,0.15);border-color:#c28b3c;}
.feat-img-wrap{width:100%;height:105px;overflow:hidden;}
.feat-img{width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.4s ease;}
.feat:hover .feat-img{transform:scale(1.06);}

/* Pattern cards (2,3,4) */
.feat-pattern{
  width:100%;height:105px;
  display:flex;align-items:center;justify-content:center;
  position:relative;overflow:hidden;
}
/* Card 2 — warm terracotta + subtle tile */
.feat-pattern.p2{background:linear-gradient(145deg,#4a1400,#8b3200);}
.feat-pattern.p2::before{
  content:'';position:absolute;inset:0;opacity:0.18;
  background-image:repeating-linear-gradient(45deg,#f5d080 0,#f5d080 1px,transparent 0,transparent 50%);
  background-size:18px 18px;
}
/* Card 3 — navy ink + lattice */
.feat-pattern.p3{background:linear-gradient(145deg,#0a1628,#1a3a6e);}
.feat-pattern.p3::before{
  content:'';position:absolute;inset:0;opacity:0.1;
  background-image:repeating-linear-gradient(0deg,#60a5fa 0,#60a5fa 1px,transparent 0,transparent 22px),repeating-linear-gradient(90deg,#60a5fa 0,#60a5fa 1px,transparent 0,transparent 22px);
}
/* Card 4 — forest deep + diamond */
.feat-pattern.p4{background:linear-gradient(145deg,#0a2218,#1a5535);}
.feat-pattern.p4::before{
  content:'';position:absolute;inset:0;opacity:0.12;
  background-image:repeating-linear-gradient(60deg,#34d399 0,#34d399 1px,transparent 0,transparent 26px),repeating-linear-gradient(-60deg,#34d399 0,#34d399 1px,transparent 0,transparent 26px);
}

.feat-pattern-icon{
  font-size:0;/* hide native emoji */
  width:52px;height:52px;border-radius:14px;
  position:relative;z-index:1;
  display:flex;align-items:center;justify-content:center;
}
/* SVG-style icons via CSS shapes */
.icon-spice{
  background:rgba(255,255,255,0.1);
  backdrop-filter:blur(6px);
  border:1px solid rgba(255,255,255,0.15);
}
.icon-spice::after{
  content:'';display:block;
  width:28px;height:36px;
  background:linear-gradient(180deg,#ef4444,#f97316 60%,#fbbf24);
  clip-path:polygon(50% 0%,80% 30%,65% 55%,55% 75%,50% 100%,45% 75%,35% 55%,20% 30%);
  filter:drop-shadow(0 2px 6px rgba(249,115,22,0.6));
}
.icon-chef{
  background:rgba(255,255,255,0.1);
  backdrop-filter:blur(6px);
  border:1px solid rgba(255,255,255,0.15);
}
.icon-chef::before{
  content:'';display:block;
  width:22px;height:22px;border-radius:50%;
  background:rgba(255,255,255,0.85);
  box-shadow:0 -6px 0 0 rgba(255,255,255,0.85),0 -12px 0 -2px rgba(255,255,255,0.7);
}
.icon-heart{
  background:rgba(255,255,255,0.1);
  backdrop-filter:blur(6px);
  border:1px solid rgba(255,255,255,0.15);
}
.icon-heart::after{
  content:'';display:block;
  width:28px;height:26px;
  background:#f43f5e;
  clip-path:path('M14 22 C14 22 2 14 2 7 C2 3.5 5 1 8 1 C10.5 1 12.5 2.5 14 4.5 C15.5 2.5 17.5 1 20 1 C23 1 26 3.5 26 7 C26 14 14 22 14 22Z');
  filter:drop-shadow(0 2px 6px rgba(244,63,94,0.6));
}

.feat-badge{display:inline-block;background:#fff8ee;border:1px solid #f0d9a8;color:#8b5a1a;font-size:10px;font-weight:700;letter-spacing:0.5px;border-radius:20px;padding:3px 10px;margin:12px 14px 6px;}
.feat-name{font-size:13px;font-weight:700;color:#2d1a08;margin-bottom:5px;padding:0 14px;}
.feat-desc{font-size:11px;color:#a08060;line-height:1.65;padding:0 14px 16px;}

/* ════════════════ SECTION HEAD ════════════════ */
.section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.section-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:900;color:#2d1a08;}
.section-title span{color:#c28b3c;}
.see-all{font-size:12px;color:#8b5a1a;font-weight:600;cursor:pointer;text-decoration:none;padding:5px 12px;border-radius:8px;border:1px solid #e0c99a;transition:background 0.2s,color 0.2s;}
.see-all:hover{background:#8b5a1a;color:white;border-color:#8b5a1a;}

/* ════════════════ MENU GRID ════════════════ */
.menu-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;}
.skeleton-card{background:white;border-radius:16px;overflow:hidden;border:1px solid #ede0cc;}
.skeleton-img{height:100px;background:linear-gradient(90deg,#f0e8dc 25%,#faf3e8 50%,#f0e8dc 75%);background-size:400% 100%;animation:shimmer 1.4s ease-in-out infinite;}
.skeleton-body{padding:12px 14px;display:flex;flex-direction:column;gap:8px;}
.skeleton-line{height:12px;border-radius:6px;background:linear-gradient(90deg,#f0e8dc 25%,#faf3e8 50%,#f0e8dc 75%);background-size:400% 100%;animation:shimmer 1.4s ease-in-out infinite;}
.skeleton-line.short{width:60%;}
.skeleton-btn{height:30px;border-radius:8px;margin-top:4px;background:linear-gradient(90deg,#f0e8dc 25%,#faf3e8 50%,#f0e8dc 75%);background-size:400% 100%;animation:shimmer 1.4s ease-in-out infinite;}
@keyframes shimmer{0%{background-position:100% 0;}100%{background-position:-100% 0;}}
.mc{background:white;border-radius:16px;overflow:hidden;border:1.5px solid #ede0cc;cursor:pointer;transition:transform 0.25s cubic-bezier(0.34,1.3,0.64,1),box-shadow 0.25s ease,border-color 0.25s ease;animation:fadeUp 0.5s ease both;}
.mc:hover{transform:translateY(-6px) scale(1.02);box-shadow:0 16px 36px rgba(139,90,26,0.2);border-color:#c28b3c;}
.mc-img{height:110px;display:flex;align-items:center;justify-content:center;background:#fff8ee;position:relative;overflow:hidden;}
.mc-img img{transition:transform 0.35s ease;}
.mc:hover .mc-img img{transform:scale(1.08);}
.mc-body{padding:12px 14px;}
.mc-name{font-size:13px;font-weight:700;color:#2d1a08;margin-bottom:3px;transition:color 0.2s;}
.mc:hover .mc-name{color:#8b5a1a;}
.mc-price{font-size:14px;color:#c28b3c;font-weight:800;margin-bottom:10px;}
.mc-btn{width:100%;background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;border:none;border-radius:8px;padding:8px;font-size:12px;font-weight:600;cursor:pointer;transition:opacity 0.15s,transform 0.15s;font-family:inherit;}
.mc-btn:hover{opacity:0.88;}
.mc-btn:active{transform:scale(0.96);}

/* ════════════════ CTA ════════════════ */
.cta{background:linear-gradient(135deg,#1c0f00,#3d2000);border-radius:22px;padding:28px 36px;display:flex;align-items:center;justify-content:space-between;animation:fadeUp 0.6s ease 1s both;position:relative;overflow:hidden;border:1px solid rgba(194,139,60,0.2);}
.cta::before{content:'';position:absolute;right:-60px;top:-60px;width:240px;height:240px;background:radial-gradient(circle,rgba(194,139,60,0.2),transparent 70%);border-radius:50%;}
.cta-left{position:relative;z-index:1;}
.cta-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:900;color:white;margin-bottom:6px;}
.cta-sub{font-size:12px;color:rgba(255,240,200,0.5);line-height:1.6;}
.cta-btn{background:linear-gradient(135deg,#c28b3c,#f5d080);color:#2d1a08;border:none;border-radius:12px;padding:13px 26px;font-size:13px;font-weight:700;cursor:pointer;z-index:1;position:relative;white-space:nowrap;transition:transform 0.2s,box-shadow 0.2s;font-family:inherit;box-shadow:0 6px 20px rgba(194,139,60,0.35);}
.cta-btn:hover{transform:scale(1.05) translateY(-2px);box-shadow:0 12px 28px rgba(194,139,60,0.45);}

/* ════════════════ PROFILE MODAL ════════════════ */
.modal-overlay{position:fixed;inset:0;background:rgba(15,8,0,0.55);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal{background:white;border-radius:24px;padding:32px;width:360px;max-width:90vw;animation:modal-in 0.35s cubic-bezier(0.34,1.3,0.64,1) both;position:relative;}
@keyframes modal-in{from{opacity:0;transform:scale(0.88) translateY(16px);}to{opacity:1;transform:scale(1) translateY(0);}}
.modal-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:#2d1a08;margin-bottom:22px;}
.modal-close{position:absolute;top:20px;right:20px;background:#f5f0e8;border:none;width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;color:#a08060;transition:background 0.2s;}
.modal-close:hover{background:#ede4d4;color:#2d1a08;}

/* Photo picker */
.photo-pick{display:flex;flex-direction:column;align-items:center;margin-bottom:22px;}
.photo-preview{
  width:80px;height:80px;border-radius:20px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  display:flex;align-items:center;justify-content:center;
  color:white;font-size:28px;font-weight:800;
  overflow:hidden;margin-bottom:10px;
  border:3px solid #f0d9a8;
  cursor:pointer;position:relative;
}
.photo-preview img{width:100%;height:100%;object-fit:cover;}
.photo-preview-overlay{position:absolute;inset:0;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;border-radius:17px;}
.photo-preview:hover .photo-preview-overlay{opacity:1;}
.photo-preview-overlay i{color:white;font-size:18px;}
.photo-pick small{font-size:11px;color:#a08060;}
#photoInput{display:none;}

/* Form fields */
.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:12px;font-weight:600;color:#5a3a18;margin-bottom:6px;}
.form-input{width:100%;border:1.5px solid #e8ddd0;border-radius:12px;padding:11px 14px;font-family:inherit;font-size:14px;color:#2d1a08;outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
.form-input:focus{border-color:#c28b3c;box-shadow:0 0 0 3px rgba(194,139,60,0.12);}
.modal-actions{display:flex;gap:10px;margin-top:6px;}
.btn-save{flex:1;background:linear-gradient(135deg,#8b5a1a,#c28b3c);color:white;border:none;border-radius:12px;padding:12px;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;transition:opacity 0.2s;}
.btn-save:hover{opacity:0.88;}
.btn-cancel{flex:1;background:#f5f0e8;color:#8b5a1a;border:1.5px solid #e0d0bc;border-radius:12px;padding:12px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;transition:background 0.2s;}
.btn-cancel:hover{background:#ede4d4;}

/* Save feedback */
.save-toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(20px);background:#2d1a08;color:#f5d080;padding:12px 24px;border-radius:14px;font-size:13px;font-weight:600;opacity:0;transition:all 0.35s cubic-bezier(0.34,1.3,0.64,1);pointer-events:none;z-index:2000;}
.save-toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ════════ SIDEBAR ════════ -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">Seoullicious</div>
  <div class="sidebar-logo-sub">Korean Food Experience</div>

  <div class="nav-label"><?= $t['main_menu'] ?></div>
  <a href="home.php"       class="nav-item active"><i class="fas fa-house"></i> <?= $t['nav_home'] ?></a>
  <a href="pos.php"        class="nav-item"><i class="fas fa-utensils"></i> <?= $t['nav_menu'] ?></a>
  <a href="history.php"    class="nav-item"><i class="fas fa-clock-rotate-left"></i> <?= $t['nav_history'] ?></a>
  <a href="review_buka.php" class="nav-item"><i class="fas fa-star"></i> <?= $t['nav_review'] ?></a>

  <div class="sidebar-bottom">
    <!-- Clickable user card → opens profile modal -->
    <div class="user-info" id="openProfileBtn" title="<?= $t['edit_profile'] ?>">
      <div class="user-av" id="sidebarAv">
        <?php if($photo_path): ?>
          <img src="../upload/<?= htmlspecialchars($photo_path) ?>" id="sidebarPhoto" alt="photo">
        <?php else: ?>
          <span id="sidebarInitial"><?= strtoupper(substr($display_name,0,1)) ?></span>
        <?php endif; ?>
      </div>
      <div>
        <div class="user-av-name" id="sidebarName"><?= htmlspecialchars($display_name) ?></div>
      </div>
      <i class="fas fa-pen user-info-edit"></i>
    </div>
    <a href="../auth/logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> <?= $t['nav_logout'] ?></a>
  </div>
</div>

<!-- ════════ MAIN ════════ -->
<div class="main" id="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" id="toggleSidebar">☰</button>
      <div class="greeting">
        <h2><?= $greeting ?>, <?= htmlspecialchars($display_name) ?>!</h2>
        <p><?= ($lang==='en') ? date('l, d F Y') : date('l, d F Y') ?></p>
        <?php if($special): ?><div class="special-day"><?= $special ?></div><?php endif; ?>
      </div>
    </div>
    <div class="topbar-right">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="<?= $t['search_ph'] ?>" id="searchInput">
      </div>

      <!-- Language switcher -->
      <div class="lang-switcher">
        <form method="POST" style="display:contents;">
          <input type="hidden" name="switch_lang" value="id">
          <button type="submit" class="lang-btn <?= $lang==='id'?'active':'' ?>">ID</button>
        </form>
        <form method="POST" style="display:contents;">
          <input type="hidden" name="switch_lang" value="en">
          <button type="submit" class="lang-btn <?= $lang==='en'?'active':'' ?>">EN</button>
        </form>
      </div>

      <a href="pos.php"><button class="order-btn"><?= $t['new_order'] ?></button></a>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- HERO -->
    <div class="hero">
      <div class="hero-left">
        <div class="hero-tag">
          <div class="hero-tag-dot"></div> <?= $t['tag_line'] ?>
        </div>
        <div class="hero-title">
          <?= $t['hero_line1'] ?><br>
          <span class="accent"><?= $t['hero_line2'] ?></span><br>
          <span class="dark"><?= $t['hero_line3'] ?></span>
        </div>
        <div class="hero-desc"><?= $t['hero_desc'] ?></div>
        <div class="hero-btns">
          <a href="pos.php"><button class="btn-primary"><?= $t['view_menu'] ?></button></a>
          <a href="history.php"><button class="btn-outline"><?= $t['order_hist'] ?></button></a>
        </div>
        <div class="hero-stats">
          <div class="hstat">
            <div class="hstat-num" data-target="20" data-suffix="+">0</div>
            <div class="hstat-label"><?= $t['stat_menu'] ?></div>
          </div>
          <div class="hstat-div"></div>
          <div class="hstat">
            <div class="hstat-num" data-target="500" data-suffix="+">0</div>
            <div class="hstat-label"><?= $t['stat_cust'] ?></div>
          </div>
          <div class="hstat-div"></div>
          <div class="hstat">
            <div class="hstat-num" data-target="4.9" data-suffix="" data-decimal="1">0</div>
            <div class="hstat-label"><?= $t['stat_rating'] ?></div>
          </div>
        </div>
      </div>
      <div class="hero-right">
        <div class="badge-hot"><i class="fas fa-fire"></i> <?= $t['badge_today'] ?></div>
        <div class="float-card left">
          <div class="fc-icon"><i class="fas fa-star" style="color:#c28b3c;font-size:16px;"></i></div>
          <div><span class="fc-b"><?= $t['best_seller'] ?></span><span class="fc-s">Tteokbokki</span></div>
        </div>
        <div class="float-card top">
          <div class="fc-icon"><i class="fas fa-clock" style="color:#8b5a1a;font-size:16px;"></i></div>
          <div>
            <span class="fc-b"><i class="fas fa-circle" style="color:#22c55e;font-size:7px;margin-right:4px;"></i><?= $t['fast_order'] ?></span>
            <span class="fc-s"><?= $t['ready_min'] ?></span>
          </div>
        </div>
        <div class="hero-right-inner">
          <div class="food-circle">
            <img src="../assets/tteokbokki.avif" loading="lazy" style="width:175px;height:175px;object-fit:cover;border-radius:50%;" onerror="this.style.display='none'">
          </div>
        </div>
      </div>
    </div>

   <!-- PROMO CARDS -->
<div class="feats">
  <!-- Card 1 -->
  <div class="feat card-lift">
    <div class="feat-img-wrap">
      <img src="../assets/bg-korean.jpg" loading="lazy" class="feat-img" onerror="this.style.display='none'">
    </div>
    <div class="feat-badge"><?= $t['card1_badge'] ?></div>
    <div class="feat-name"><?= $t['card1_name'] ?></div>
    <div class="feat-desc"><?= $t['card1_desc'] ?></div>
  </div>

  <!-- Card 2 -->
  <div class="feat card-lift">
    <div class="feat-img-wrap">
      <img src="../assets/impor.avif" loading="lazy" class="feat-img" onerror="this.style.display='none'">
    </div>
    <div class="feat-badge"><?= $t['card2_badge'] ?></div>
    <div class="feat-name"><?= $t['card2_name'] ?></div>
    <div class="feat-desc"><?= $t['card2_desc'] ?></div>
  </div>

  <!-- Card 3 -->
  <div class="feat card-lift">
    <div class="feat-img-wrap">
      <img src="../assets/chef.jpg" loading="lazy" class="feat-img" onerror="this.style.display='none'">
    </div>
    <div class="feat-badge"><?= $t['card3_badge'] ?></div>
    <div class="feat-name"><?= $t['card3_name'] ?></div>
    <div class="feat-desc"><?= $t['card3_desc'] ?></div>
  </div>

  <!-- Card 4 -->
  <div class="feat card-lift">
    <div class="feat-img-wrap">
      <img src="../assets/pelanggan.jpg" loading="lazy" class="feat-img" onerror="this.style.display='none'">
    </div>
    <div class="feat-badge"><?= $t['card4_badge'] ?></div>
    <div class="feat-name"><?= $t['card4_name'] ?></div>
    <div class="feat-desc"><?= $t['card4_desc'] ?></div>
  </div>
</div>

    <!-- BEST SELLERS -->
    <div class="section-head">
      <div class="section-title"><?= $t['best_sellers'] ?> <span><?= $t['best_sell2'] ?></span></div>
      <a href="pos.php" class="see-all"><?= $t['see_all'] ?></a>
    </div>
    <div class="menu-grid" id="menuGrid">
      <?php for($s=0;$s<4;$s++): ?>
      <div class="skeleton-card">
        <div class="skeleton-img"></div>
        <div class="skeleton-body">
          <div class="skeleton-line"></div>
          <div class="skeleton-line short"></div>
          <div class="skeleton-btn"></div>
        </div>
      </div>
      <?php endfor; ?>
    </div>

    <!-- CTA -->
    <div class="cta">
      <div class="cta-left">
        <div class="cta-title"><?= $t['cta_title'] ?></div>
        <div class="cta-sub"><?= $t['cta_sub'] ?></div>
      </div>
      <a href="pos.php"><button class="cta-btn"><?= $t['cta_btn'] ?></button></a>
    </div>

  </div><!-- /content -->
</div><!-- /main -->

<!-- ════════ PROFILE MODAL ════════ -->
<div class="modal-overlay" id="profileModal">
  <div class="modal">
    <button class="modal-close" id="closeModal"><i class="fas fa-xmark"></i></button>
    <div class="modal-title"><?= $t['edit_profile'] ?></div>

    <!-- Photo picker -->
    <div class="photo-pick">
      <div class="photo-preview" id="photoPreview" onclick="document.getElementById('photoInput').click()">
        <?php if($photo_path): ?>
          <img src="../upload/<?= htmlspecialchars($photo_path) ?>" id="previewImg" alt="preview">
        <?php else: ?>
          <span id="previewInitial"><?= strtoupper(substr($display_name,0,1)) ?></span>
        <?php endif; ?>
        <div class="photo-preview-overlay"><i class="fas fa-camera"></i></div>
      </div>
      <small><?= $t['upload_photo'] ?></small>
      <input type="file" id="photoInput" accept="image/*">
    </div>

    <!-- Name field -->
    <div class="form-group">
      <label class="form-label"><?= $t['display_name'] ?></label>
      <input type="text" class="form-input" id="displayNameInput" value="<?= htmlspecialchars($display_name) ?>" maxlength="50">
    </div>

    <div class="modal-actions">
      <button class="btn-cancel" id="cancelModal"><?= $t['cancel'] ?></button>
      <button class="btn-save" id="saveProfile"><?= $t['save'] ?></button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="save-toast" id="saveToast"><i class="fas fa-check"></i> <?= ($lang==='en') ? 'Profile updated!' : 'Profil berhasil disimpan!' ?></div>

<!-- JS data from PHP -->
<script>
const MENU_DATA = <?= json_encode($top_menus) ?>;
const ADD_LABEL = <?= json_encode($t['add_order']) ?>;
const CURR_LANG = <?= json_encode($lang) ?>;
const UPLOAD_PATH = '../upload/';
</script>

<script>
/* ── Sidebar toggle (desktop hide / mobile slide) ── */
const _sb  = document.getElementById('sidebar');
const _ov  = document.getElementById('sidebarOverlay');
document.getElementById('toggleSidebar').onclick = function(){
  if(window.innerWidth <= 768){
    _sb.classList.toggle('mobile-open');
    _ov.classList.toggle('show');
  } else {
    _sb.classList.toggle('hide');
  }
};
_ov.onclick = function(){ _sb.classList.remove('mobile-open'); _ov.classList.remove('show'); };

/* ── Count-up ── */
function animateCount(el){
  const target=parseFloat(el.dataset.target), suffix=el.dataset.suffix||'', dec=parseInt(el.dataset.decimal)||0;
  const dur=1400, start=performance.now();
  function step(now){
    const e=Math.min((now-start)/dur,1), v=(1-Math.pow(1-e,3))*target;
    el.textContent=dec>0?v.toFixed(dec)+suffix:Math.floor(v)+suffix;
    if(e<1)requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
}
new IntersectionObserver((ents)=>ents.forEach(e=>{
  if(e.isIntersecting&&!e.target.dataset.animated){e.target.dataset.animated='1';animateCount(e.target);}
}),{threshold:0.5}).observe(document.querySelector('.hstat-num'));
document.querySelectorAll('.hstat-num[data-target]').forEach(el=>{
  new IntersectionObserver((ents)=>ents.forEach(e=>{
    if(e.isIntersecting&&!e.target.dataset.animated){e.target.dataset.animated='1';animateCount(e.target);}
  }),{threshold:0.5}).observe(el);
});

/* ── Render best-sellers ── */
function escHtml(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function formatRp(n){return n.toLocaleString('id-ID');}
function renderMenuCards(){
  const grid=document.getElementById('menuGrid');
  if(!MENU_DATA||!MENU_DATA.length)return;
  const html=MENU_DATA.map((item,i)=>{
    const nama=item.nama_menu||item.nama||'Menu', harga=parseInt(item.harga)||0, gambar=item.gambar||'';
    const delay=(0.6+i*0.12).toFixed(2);
    const imgH=gambar
      ?`<img src="../assets/${escHtml(gambar)}" style="width:86px;height:86px;object-fit:cover;border-radius:12px;" onerror="this.style.display='none'">`
      :``;
    return`<div class="mc" style="animation-delay:${delay}s">
      <div class="mc-img" style="background:#fff8ee;">${imgH}</div>
      <div class="mc-body">
        <div class="mc-name">${escHtml(nama)}</div>
        <div class="mc-price">Rp ${formatRp(harga)}</div>
        <a href="pos.php"><button class="mc-btn">${escHtml(ADD_LABEL)}</button></a>
      </div></div>`;
  }).join('');
  setTimeout(()=>grid.innerHTML=html, 600);
}
renderMenuCards();

/* ── Search ── */
document.getElementById('searchInput').addEventListener('input',function(){
  const q=this.value.toLowerCase().trim();
  document.querySelectorAll('.mc').forEach(c=>{
    c.style.display=((c.querySelector('.mc-name')?.textContent||'').toLowerCase().includes(q))?'':'none';
  });
});

/* ── Profile Modal ── */
const modal      = document.getElementById('profileModal');
const openBtn    = document.getElementById('openProfileBtn');
const closeBtn   = document.getElementById('closeModal');
const cancelBtn  = document.getElementById('cancelModal');
const saveBtn    = document.getElementById('saveProfile');
const photoInput = document.getElementById('photoInput');
const previewDiv = document.getElementById('photoPreview');
const nameInput  = document.getElementById('displayNameInput');
const toast      = document.getElementById('saveToast');

openBtn.onclick  = ()=> modal.classList.add('open');
closeBtn.onclick = ()=> modal.classList.remove('open');
cancelBtn.onclick= ()=> modal.classList.remove('open');
modal.addEventListener('click', e=> { if(e.target===modal) modal.classList.remove('open'); });

// Photo preview
let newPhotoFile = null;
photoInput.addEventListener('change', function(){
  if(!this.files[0]) return;
  newPhotoFile = this.files[0];
  const url = URL.createObjectURL(newPhotoFile);
  // show in modal preview
  let img = previewDiv.querySelector('img');
  if(!img){ img=document.createElement('img'); previewDiv.prepend(img); }
  img.src = url;
  const span = previewDiv.querySelector('span');
  if(span) span.style.display='none';
});

// Save via AJAX FormData
saveBtn.onclick = function(){
  const fd = new FormData();
  fd.append('action','save_profile');
  fd.append('display_name', nameInput.value.trim());
  if(newPhotoFile) fd.append('profile_photo', newPhotoFile);

  fetch('home.php', {method:'POST', body:fd})
    .then(r=>r.json())
    .then(data=>{
      if(!data.ok) return;
      // Update sidebar name
      document.getElementById('sidebarName').textContent = data.name;
      document.querySelector('.greeting h2').textContent =
        document.querySelector('.greeting h2').textContent.replace(/,.*/, ', '+data.name+'!');
      // Update sidebar avatar
      const av = document.getElementById('sidebarAv');
      if(data.photo){
        let img = av.querySelector('img');
        if(!img){ img=document.createElement('img'); img.id='sidebarPhoto'; av.innerHTML=''; av.appendChild(img); }
        img.src = UPLOAD_PATH + data.photo + '?t=' + Date.now();
        img.style.cssText='width:100%;height:100%;object-fit:cover;border-radius:10px;';
      } else {
        av.innerHTML = `<span>${data.name[0].toUpperCase()}</span>`;
      }
      modal.classList.remove('open');
      toast.classList.add('show');
      setTimeout(()=>toast.classList.remove('show'), 2800);
    })
    .catch(()=>{});
};
</script>
</body>
</html>
