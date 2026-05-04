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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    background: #f4f6f9;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 30px 15px;
}

.card {
    width: 100%;
    max-width: 420px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* HEADER */
.card-header {
    background: linear-gradient(135deg, #b79055, #d4aa6e);
    padding: 24px 25px 20px;
    text-align: center;
}

.card-header .logo {
    font-size: 20px;
    font-weight: 700;
    color: white;
    letter-spacing: 1px;
}

.card-header .subtitle {
    font-size: 12px;
    color: rgba(255,255,255,0.8);
    margin-top: 2px;
}

.card-header .order-badge {
    display: inline-block;
    margin-top: 10px;
    background: rgba(255,255,255,0.2);
    color: white;
    font-size: 12px;
    padding: 4px 14px;
    border-radius: 20px;
    font-weight: 500;
}

.card-body {
    padding: 22px 25px 28px;
}

/* ALERT */
.alert {
    padding: 12px 15px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 18px;
    font-weight: 500;
}

.alert-success {
    background: #e8f9f0;
    color: #2e7d52;
    border-left: 4px solid #34c975;
}

.alert-error {
    background: #fdf0f0;
    color: #c0392b;
    border-left: 4px solid #e74c3c;
}

/* TABS */
.tabs {
    display: flex;
    background: #f4f6f9;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 22px;
    gap: 4px;
}

.tab-btn {
    flex: 1;
    padding: 9px 5px;
    border: none;
    background: transparent;
    border-radius: 9px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: #888;
    cursor: pointer;
    transition: all 0.25s;
}

.tab-btn.active {
    background: white;
    color: #b79055;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}

/* PANELS */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* SECTION LABEL */
.section-label {
    font-size: 13px;
    font-weight: 600;
    color: #444;
    margin-bottom: 14px;
}

/* MENU REVIEW ITEM */
.menu-review-item {
    background: #fafafa;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 14px;
}

.menu-review-item .menu-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

/* STAR RATING */
.star-group {
    display: flex;
    gap: 6px;
    margin-bottom: 10px;
}

.star-group input[type="radio"] {
    display: none;
}

.star-group label {
    font-size: 26px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.15s, transform 0.15s;
    user-select: none;
}

.star-group label:hover,
.star-group label:hover ~ label,
.star-group input[type="radio"]:checked ~ label {
    color: #f5c518;
}

/* Reverse trick for star rating */
.star-group {
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.star-group label:hover,
.star-group label:hover ~ label {
    color: #f5c518;
}

.star-group input[type="radio"]:checked ~ label {
    color: #f5c518;
}

.star-group label:hover {
    transform: scale(1.2);
}

/* TEXTAREA */
textarea {
    width: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 10px 13px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    resize: none;
    color: #444;
    outline: none;
    transition: border 0.2s;
    background: white;
}

textarea:focus {
    border-color: #b79055;
}

textarea::placeholder {
    color: #bbb;
}

/* SUBMIT BUTTON */
.btn-submit {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #b79055, #d4aa6e);
    color: white;
    border: none;
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 6px;
    transition: opacity 0.2s, transform 0.15s;
}

.btn-submit:hover {
    opacity: 0.92;
    transform: translateY(-1px);
}

.btn-submit:active {
    transform: translateY(0);
}

/* BACK LINK */
.back-link {
    display: block;
    text-align: center;
    margin-top: 16px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
    transition: color 0.2s;
}

.back-link:hover { color: #b79055; }

/* DIVIDER */
.divider {
    border: none;
    border-top: 1px dashed #eee;
    margin: 18px 0;
}

/* Already reviewed badge */
.reviewed-badge {
    display: inline-block;
    background: #e8f9f0;
    color: #2e7d52;
    font-size: 11px;
    padding: 2px 10px;
    border-radius: 20px;
    font-weight: 600;
    margin-bottom: 10px;
}

/* Resto review single block */
.resto-block {
    background: #fafafa;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 16px;
}

.resto-block .menu-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<div class="card">

    <!-- HEADER -->
    <div class="card-header">
        <div class="logo">SEOULLICIOUS</div>
        <div class="subtitle">Cafe & Korean Food</div>
        <div class="order-badge">Order #<?= $id_order ?></div>
    </div>

    <div class="card-body">

        <!-- ALERT -->
        <?php if($success): ?>
            <div class="alert alert-success">✅ <?= $success ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-error">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <!-- TABS -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('menu', this)">🍽️ Review Menu</button>
            <button class="tab-btn" onclick="switchTab('restoran', this)">🏪 Review Restoran</button>
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
                        <small style="color:#aaa;font-weight:400;"> &times;<?= $menu['qty'] ?></small>
                    </div>

                    <?php if($exist): ?>
                        <div class="reviewed-badge">✔ Sudah direview</div>
                    <?php endif; ?>

                    <!-- STAR RATING -->
                    <div class="star-group">
                        <?php for($s = 5; $s >= 1; $s--): ?>
                            <input
                                type="radio"
                                name="bintang_menu_<?= $id_menu ?>"
                                id="star_<?= $id_menu ?>_<?= $s ?>"
                                value="<?= $s ?>"
                                <?= ($exist && $exist['bintang'] == $s) ? 'checked' : '' ?>
                            >
                            <label for="star_<?= $id_menu ?>_<?= $s ?>">★</label>
                        <?php endfor; ?>
                    </div>

                    <!-- KOMENTAR -->
                    <textarea
                        name="komentar_menu_<?= $id_menu ?>"
                        rows="2"
                        placeholder="Tulis komentar... (opsional)"
                    ><?= htmlspecialchars($exist['komentar'] ?? '') ?></textarea>
                </div>

                <?php endforeach; ?>

                <button type="submit" class="btn-submit">⭐ Kirim Review Menu</button>
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
                        <div class="reviewed-badge">✔ Sudah direview</div>
                    <?php endif; ?>

                    <!-- STAR RATING RESTO -->
                    <div class="star-group">
                        <?php for($s = 5; $s >= 1; $s--): ?>
                            <input
                                type="radio"
                                name="bintang_resto"
                                id="star_resto_<?= $s ?>"
                                value="<?= $s ?>"
                                <?= ($existing_resto && $existing_resto['bintang'] == $s) ? 'checked' : '' ?>
                            >
                            <label for="star_resto_<?= $s ?>">★</label>
                        <?php endfor; ?>
                    </div>

                    <textarea
                        name="komentar_resto"
                        rows="3"
                        placeholder="Ceritakan pengalamanmu... (opsional)"
                    ><?= htmlspecialchars($existing_resto['komentar'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn-submit" style="margin-top:16px;">⭐ Kirim Review Restoran</button>
            </form>
        </div>

        <hr class="divider">

        <a href="receipt.php?id=<?= $id_order ?>" class="back-link">← Kembali ke Receipt</a>

    </div>
</div>

<script>
function switchTab(name, btn) {
    // Sembunyikan semua panel
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    // Tampilkan panel yang dipilih
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}
</script>

</body>
</html>