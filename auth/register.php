<?php
session_start();
include "../config/koneksi.php";
$success = $error = "";
// Kode akses admin — ganti sesuai kebutuhan
define('ADMIN_SECRET', 'SEOUL2024');

if(isset($_POST['register'])){
    $username   = trim($_POST['username']);
    $nama       = trim($_POST['nama_lengkap']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $admin_code = trim($_POST['admin_code'] ?? '');

    // Tentukan status berdasarkan kode akses
    if($admin_code !== '' && $admin_code === ADMIN_SECRET){
        $status = 'admin';
    } elseif($admin_code !== '' && $admin_code !== ADMIN_SECRET){
        $error = "Kode akses tidak valid.";
    } else {
        $status = ''; // customer biasa
    }

    if(empty($error)){
        $stmtCek = mysqli_prepare($koneksi, "SELECT id_user FROM user WHERE username = ?");
        mysqli_stmt_bind_param($stmtCek, "s", $username);
        mysqli_stmt_execute($stmtCek);
        mysqli_stmt_store_result($stmtCek);
        if(mysqli_stmt_num_rows($stmtCek) > 0){
            $error = "Username sudah digunakan.";
        } else {
            $stmtIns = mysqli_prepare($koneksi, "INSERT INTO user (username, nama_lengkap, password, status) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtIns, "ssss", $username, $nama, $password, $status);
            if(mysqli_stmt_execute($stmtIns)){
                $success = $status === 'admin'
                    ? "Akun admin berhasil dibuat! Silakan masuk."
                    : "Akun berhasil dibuat! Silakan masuk.";
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
            mysqli_stmt_close($stmtIns);
        }
        mysqli_stmt_close($stmtCek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seoullicious — Daftar</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --ink:#1c0f00; --paper:#faf6f0; --gold:#c28b3c;
  --gold-light:#f5d080; --muted:#8b6a40; --border:#e8ddd0; --dark:#1c0f00;
}
body{min-height:100vh;background:var(--paper);font-family:'DM Sans',sans-serif;display:flex;align-items:stretch}

/* ── Left panel (mirrored from login) ── */
.panel-left{
  width:46%;background:var(--dark);position:relative;
  display:flex;flex-direction:column;justify-content:flex-end;
  padding:52px 48px;overflow:hidden;
}
.panel-left::before{
  content:'';position:absolute;inset:0;
  background:url('../assets/bg-navigasi.jpg') center/cover no-repeat;opacity:0.2;
}
.brand-stripe{
  position:absolute;top:0;left:0;bottom:0;width:4px;
  background:linear-gradient(180deg,transparent,var(--gold) 40%,var(--gold-light) 60%,transparent);
}
.panel-logo{
  position:absolute;top:44px;left:48px;right:48px;
  display:flex;align-items:center;gap:14px;z-index:2;
}
.logo-mark{
  width:40px;height:40px;border:1.5px solid rgba(194,139,60,0.55);
  border-radius:10px;display:flex;align-items:center;justify-content:center;
  font-size:18px;color:var(--gold-light);font-family:'Cormorant Garamond',serif;font-weight:600;
}
.logo-text{font-family:'Cormorant Garamond',serif;font-weight:600;color:var(--gold-light);font-size:18px;letter-spacing:0.5px}
.panel-editorial{position:relative;z-index:2}
.edition-tag{
  display:inline-block;border:1px solid rgba(194,139,60,0.45);
  color:var(--gold);font-size:10px;letter-spacing:2.5px;text-transform:uppercase;
  padding:5px 12px;border-radius:3px;margin-bottom:22px;font-weight:500;
}
.panel-headline{
  font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:700;
  line-height:1.05;color:#f5ede0;margin-bottom:18px;
}
.panel-headline em{color:var(--gold-light);font-style:italic}
.panel-sub{font-size:13px;color:rgba(232,210,170,0.5);line-height:1.75;max-width:300px;margin-bottom:36px}
.cuisine-tags{display:flex;gap:8px;flex-wrap:wrap}
.cuisine-tag{
  padding:6px 16px;border:1px solid rgba(194,139,60,0.25);
  border-radius:20px;font-size:11px;color:rgba(232,210,170,0.55);
}
.rule{width:40px;height:1px;background:var(--gold);margin-bottom:22px}

/* ── Right panel ── */
.panel-right{flex:1;display:flex;align-items:center;justify-content:center;padding:60px 56px}
.form-container{width:100%;max-width:360px}
.form-eyebrow{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:10px}
.form-title{font-family:'Cormorant Garamond',serif;font-size:34px;font-weight:700;color:var(--ink);line-height:1.12;margin-bottom:6px}
.form-subtitle{font-size:13px;color:var(--muted);margin-bottom:32px}
.alert-success{background:#eaf6ef;border-left:3px solid #2a6641;color:#2a6641;padding:11px 16px;border-radius:0 6px 6px 0;font-size:13px;margin-bottom:20px}
.alert-error{background:#fdf0ec;border-left:3px solid #c0704a;color:#8b3a1a;padding:11px 16px;border-radius:0 6px 6px 0;font-size:13px;margin-bottom:20px}
.field{margin-bottom:18px}
.field label{display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
.field input{
  width:100%;padding:13px 18px;border:1.5px solid var(--border);border-radius:8px;
  background:#fff;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--ink);
  outline:none;transition:border-color .2s,box-shadow .2s;
}
.field input::placeholder{color:#c4b89a}
.field input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(194,139,60,0.12)}
.submit-btn{
  width:100%;padding:14px;background:var(--ink);color:var(--gold-light);
  border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:14px;
  font-weight:600;cursor:pointer;margin-top:8px;letter-spacing:0.3px;
  transition:background .2s,transform .15s;
}
.submit-btn:hover{background:#2d1a08;transform:translateY(-1px)}
.login-link{text-align:center;margin-top:22px;font-size:13px;color:var(--muted)}
.login-link a{color:var(--gold);font-weight:600;text-decoration:none}
.form-footnote{margin-top:32px;padding-top:20px;border-top:1px solid #e8dfd0;font-size:11px;color:#b4a890;text-align:center;line-height:1.6}

@media(max-width:768px){
  .panel-left{display:none}
  .panel-right{padding:40px 28px}
}
</style>
</head>
<body>
<div class="panel-left">
  <div class="brand-stripe"></div>
  <div class="panel-logo">
    <div class="logo-mark">S</div>
    <span class="logo-text">Seoullicious</span>
  </div>
  <div class="panel-editorial">
    <div class="edition-tag">Korean Kitchen · Est. 2020</div>
    <div class="rule"></div>
    <h1 class="panel-headline">Join the<br><em>Seoul</em><br>Experience</h1>
    <p class="panel-sub">Buat akun dan mulai nikmati kemudahan memesan makanan Korea autentik.</p>
    <div class="cuisine-tags">
      <span class="cuisine-tag">Authentic Korean</span>
      <span class="cuisine-tag">Halal Certified</span>
      <span class="cuisine-tag">Since 2020</span>
    </div>
  </div>
</div>
<div class="panel-right">
  <div class="form-container">
    <div class="form-eyebrow">Buat Akun Baru</div>
    <h2 class="form-title">Daftar<br>Sekarang</h2>
    <p class="form-subtitle">Lengkapi data untuk membuat akun</p>
    <?php if($success): ?><div class="alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="field">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" placeholder="Nama lengkap Anda" required>
      </div>
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="nama.pengguna" required autocomplete="username">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required autocomplete="new-password">
      </div>
      <div class="field">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirm" placeholder="••••••••" required autocomplete="new-password">
      </div>
      <button type="submit" name="register" class="submit-btn">Buat Akun</button>
    </form>
    <p class="login-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    <div class="form-footnote">Dengan mendaftar, Anda bergabung sebagai pelanggan Seoullicious.</div>
  </div>
</div>
<script>
// Client-side password confirmation check
document.querySelector('form').addEventListener('submit', function(e){
  const p1 = this.querySelector('[name="password"]').value;
  const p2 = this.querySelector('[name="password_confirm"]').value;
  if(p1 !== p2){
    e.preventDefault();
    alert('Password dan konfirmasi password tidak cocok.');
  }
});
</script>
<script>
const toggleBtn = document.getElementById('toggleAdminCode');
const adminGroup = document.getElementById('adminCodeGroup');
let adminVisible = false;
if(toggleBtn){
  toggleBtn.onclick = function(){
    adminVisible = !adminVisible;
    adminGroup.style.display = adminVisible ? 'block' : 'none';
    toggleBtn.textContent = adminVisible ? 'Daftar sebagai pelanggan?' : 'Daftar sebagai admin?';
  };
}
</script>
</body>
</html>
