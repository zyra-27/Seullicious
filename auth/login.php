<?php
session_start();
include "../config/koneksi.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // FIX: prepared statement — no more SQL injection
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cek    = mysqli_num_rows($result);
    $data   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if($cek > 0 && password_verify($password, $data['password'])){
        $_SESSION['id_user']      = $data['id_user'];
        $_SESSION['username']     = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['status']       = $data['status'];
        $status = trim($data['status']);
        if($status == "admin")       header("Location: ../admin/dashboard.php");
        elseif($status == "kasir")   header("Location: ../kasir/home.php");
        else                         header("Location: ../user/home.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seoullicious — Masuk</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --ink:#1c0f00; --paper:#faf6f0; --gold:#c28b3c;
  --gold-light:#f5d080; --muted:#8b6a40; --border:#e8ddd0; --dark:#1c0f00;
}
body{min-height:100vh;background:var(--paper);font-family:'DM Sans',sans-serif;display:flex;align-items:stretch}

/* ── Left panel ── */
.panel-left{
  width:46%;background:var(--dark);position:relative;
  display:flex;flex-direction:column;justify-content:flex-end;
  padding:52px 48px;overflow:hidden;
}
.panel-left::before{
  content:'';position:absolute;inset:0;
  background:url('../assets/bg-korean.jpg') center/cover no-repeat;opacity:0.18;
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
  padding:5px 12px;border-radius:3px;margin-bottom:22px;font-weight:500;font-family:'DM Sans',sans-serif;
}
.panel-headline{
  font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:700;
  line-height:1.05;color:#f5ede0;margin-bottom:18px;
}
.panel-headline em{color:var(--gold-light);font-style:italic}
.panel-sub{font-size:13px;color:rgba(232,210,170,0.5);line-height:1.75;max-width:300px;margin-bottom:36px;font-family:'DM Sans',sans-serif}
.cuisine-tags{display:flex;gap:8px;flex-wrap:wrap}
.cuisine-tag{
  padding:6px 16px;border:1px solid rgba(194,139,60,0.25);
  border-radius:20px;font-size:11px;color:rgba(232,210,170,0.55);font-family:'DM Sans',sans-serif;
}
.rule{width:40px;height:1px;background:var(--gold);margin-bottom:22px}

/* ── Right panel ── */
.panel-right{flex:1;display:flex;align-items:center;justify-content:center;padding:60px 56px}
.form-container{width:100%;max-width:360px}
.form-eyebrow{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:10px;font-family:'DM Sans',sans-serif}
.form-title{font-family:'Cormorant Garamond',serif;font-size:34px;font-weight:700;color:var(--ink);line-height:1.12;margin-bottom:6px}
.form-subtitle{font-size:13px;color:var(--muted);margin-bottom:36px}
.error-block{
  background:#fdf0ec;border-left:3px solid #c0704a;padding:11px 16px;
  border-radius:0 6px 6px 0;font-size:13px;color:#8b3a1a;margin-bottom:22px;
}
.field{margin-bottom:20px}
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
.divider{display:flex;align-items:center;gap:14px;margin:24px 0 20px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#e2d9c8}
.divider span{font-size:11px;color:#b4a890}
.register-link{text-align:center;font-size:13px;color:var(--muted)}
.register-link a{color:var(--gold);font-weight:600;text-decoration:none}
.form-footnote{margin-top:40px;padding-top:24px;border-top:1px solid #e8dfd0;display:flex;gap:16px}
.footnote-item{display:flex;align-items:center;gap:8px}
.footnote-dot{width:6px;height:6px;border-radius:50%;background:var(--gold);opacity:0.5}
.footnote-text{font-size:11px;color:#b4a890}

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
    <div class="edition-tag">Masakan Korea Autentik · Sejak 2020</div>
    <div class="rule"></div>
    <h1 class="panel-headline">Cita rasa<br><em>Seoul</em><br>di tengah<br>kotamu</h1>
    <p class="panel-sub">Dari tteokbokki yang pedas hingga ramen hangat — setiap suapan membawa kamu lebih dekat ke Seoul.</p>
    <div class="cuisine-tags">
      <span class="cuisine-tag">Autentik Korea</span>
      <span class="cuisine-tag">Halal</span>
      <span class="cuisine-tag">Sejak 2020</span>
    </div>
  </div>
</div>
<div class="panel-right">
  <div class="form-container">
    <div class="form-eyebrow">Selamat Datang</div>
    <h2 class="form-title">Mau makan<br>apa hari ini?</h2>
    <p class="form-subtitle">Masuk dulu, nanti kita carikan yang paling enak buat kamu</p>
    <?php if(isset($error)): ?>
    <div class="error-block"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="nama.pengguna" required autocomplete="username">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" name="login" class="submit-btn">Masuk</button>
    </form>
    <div class="divider"><span>atau</span></div>
    <p class="register-link">Baru pertama ke sini? <a href="register.php">Buat akun gratis</a></p>
    <div class="form-footnote">
      <div class="footnote-item"><div class="footnote-dot"></div><span class="footnote-text">100% aman</span></div>
      <div class="footnote-item"><div class="footnote-dot"></div><span class="footnote-text">Gratis daftar</span></div>
      <div class="footnote-item"><div class="footnote-dot"></div><span class="footnote-text">Pesan mudah</span></div>
    </div>
  </div>
</div>
</body>
</html>