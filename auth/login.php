<?php
session_start();
include "../config/koneksi.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    $cek   = mysqli_num_rows($query);
    $data  = mysqli_fetch_assoc($query);

    if($cek > 0 && password_verify($password, $data['password'])){
        $_SESSION['id_user']      = $data['id_user'];
        $_SESSION['username']     = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['status']       = $data['status'];

        $status = trim($data['status']);

        if($status == "admin"){
            header("Location: ../admin/dashboard.php");
        } elseif($status == "kasir"){
            header("Location: ../kasir/home.php");
        } else {
            header("Location: ../user/home.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login POS</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#e8d5b8;font-family:'Segoe UI',sans-serif;}
.wrap{display:flex;width:820px;min-height:500px;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.2);border:1px solid #c8a97a;}
.left{
  position:relative;width:52%;background:#2d1a08;
  overflow:hidden;display:flex;align-items:center;justify-content:center;
}
.blob1{position:absolute;width:300px;height:300px;background:#8b5a1a;border-radius:50%;top:-70px;left:-70px;opacity:0.35;}
.blob2{position:absolute;width:240px;height:240px;background:#4a7c3f;border-radius:50%;bottom:-50px;right:-50px;opacity:0.25;}
.blob3{position:absolute;width:160px;height:160px;background:#c28b3c;border-radius:50%;bottom:90px;left:50px;opacity:0.2;}
.dots{position:absolute;inset:0;}
.dot{position:absolute;border-radius:50%;}
.food-card{position:relative;z-index:2;text-align:center;color:white;padding:32px 28px;}
.food-icon{
  width:88px;height:88px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  margin:0 auto 18px;font-size:34px;
  box-shadow:0 0 0 8px rgba(139,90,26,0.2),0 0 0 16px rgba(139,90,26,0.1);
}
.food-card h2{font-size:28px;font-weight:800;letter-spacing:1px;color:#fff8ef;}
.food-card p{font-size:13px;color:rgba(255,240,210,0.65);margin-top:8px;font-style:italic;}
.tags{display:flex;gap:8px;justify-content:center;margin-top:18px;flex-wrap:wrap;}
.tag{padding:5px 14px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.5px;}
.tag-gold{background:rgba(194,139,60,0.3);color:#f5d080;border:1px solid rgba(245,208,128,0.35);}
.tag-green{background:rgba(74,124,63,0.3);color:#a8d89a;border:1px solid rgba(168,216,154,0.35);}
.tag-cream{background:rgba(210,180,140,0.2);color:#e8d0a8;border:1px solid rgba(232,208,168,0.3);}
.wave{position:absolute;right:-1px;top:0;bottom:0;width:68px;z-index:3;}
.right{
  position:relative;width:48%;overflow:hidden;
  display:flex;align-items:center;justify-content:center;padding:40px 36px;
}
.bg-photo{
  position:absolute;inset:0;
  background:url('../upload/login_food.jpg') center/cover no-repeat;
  z-index:0;
}
.bg-overlay{
  position:absolute;inset:0;
  background:rgba(255,245,230,0.82);
  z-index:1;
}
.form-box{position:relative;z-index:2;width:100%;max-width:300px;}
.brand{display:flex;align-items:center;gap:10px;margin-bottom:26px;}
.brand-dot{
  width:36px;height:36px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  border-radius:10px;display:flex;align-items:center;justify-content:center;
  color:white;font-weight:800;font-size:16px;
}
.brand-name{font-size:17px;font-weight:800;color:#3b1f08;}
.form-box h1{font-size:22px;font-weight:800;color:#3b1f08;margin-bottom:4px;}
.form-box .sub{font-size:13px;color:#8a6040;margin-bottom:22px;}
.error-msg{background:#fff0e8;border:1px solid #e8b090;color:#8b3a1a;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;}
.input-wrap{position:relative;margin-bottom:15px;}
.input-wrap label{display:block;font-size:11px;font-weight:700;color:#6b4020;margin-bottom:6px;letter-spacing:0.5px;text-transform:uppercase;}
.input-wrap input{
  width:100%;padding:11px 16px 11px 40px;
  border:1.5px solid #d4b08a;border-radius:12px;font-size:14px;
  background:rgba(255,252,245,0.88);color:#3b1f08;outline:none;
  transition:border-color 0.2s,box-shadow 0.2s;
}
.input-wrap input:focus{border-color:#8b5a1a;box-shadow:0 0 0 3px rgba(139,90,26,0.15);}
.input-wrap input::placeholder{color:#b89070;}
.ico{position:absolute;left:13px;bottom:11px;font-size:15px;}
.btn{
  width:100%;padding:12px;
  background:linear-gradient(135deg,#8b5a1a,#c28b3c);
  color:white;border:none;border-radius:12px;
  font-size:15px;font-weight:700;cursor:pointer;margin-top:6px;
  transition:opacity 0.2s;
}
.btn:hover{opacity:0.88;}
.divider{display:flex;align-items:center;gap:10px;margin:16px 0 14px;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#d4b890;}
.divider span{font-size:12px;color:#a07850;}
.reg{text-align:center;font-size:13px;color:#8a6040;}
.reg a{color:#8b5a1a;font-weight:700;text-decoration:none;}
.sparkle-row{display:flex;justify-content:center;gap:6px;margin-top:18px;}
.sp{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;}
</style>
</head>
<body>
<div class="wrap">
  <div class="left">
    <div class="blob1"></div>
    <div class="blob2"></div>
    <div class="blob3"></div>
    <div class="dots" id="dots"></div>
    <div class="food-card">
      <div class="food-icon">🍜</div>
      <h2>Seullicious</h2>
      <p>Taste of Korea, Heart of Seoul</p>
      <div class="tags">
        <span class="tag tag-gold">Authentic Korean</span>
        <span class="tag tag-green">Since 2020</span>
        <span class="tag tag-cream">Halal Certified</span>
      </div>
    </div>
    <svg class="wave" viewBox="0 0 68 500" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M68,0 C28,80 48,165 26,250 C8,335 40,420 68,500 L68,0 Z" fill="rgba(255,245,230,0.82)"/>
    </svg>
  </div>
  <div class="right">
    <div class="bg-photo"></div>
    <div class="bg-overlay"></div>
    <div class="form-box">
      <div class="brand">
        <div class="brand-dot">S</div>
        <span class="brand-name">Seullicious</span>
      </div>
      <h1>Welcome back!</h1>
      <p class="sub">Masuk ke sistem kasir restoran</p>
      <?php if(isset($error)): ?>
      <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="input-wrap">
          <label>Username</label>
          <span class="ico">👤</span>
          <input type="text" name="username" placeholder="Masukkan username..." required>
        </div>
        <div class="input-wrap">
          <label>Password</label>
          <span class="ico">🔒</span>
          <input type="password" name="password" placeholder="Masukkan password..." required>
        </div>
        <button type="submit" name="login" class="btn">Masuk Sekarang</button>
      </form>
      <div class="divider"><span>atau</span></div>
      <p class="reg">Belum punya akun? <a href="register.php">Register</a></p>
      <div class="sparkle-row">
        <div class="sp" style="background:rgba(139,90,26,0.12);">🍱</div>
        <div class="sp" style="background:rgba(74,124,63,0.12);">🥢</div>
        <div class="sp" style="background:rgba(194,139,60,0.12);">🍵</div>
        <div class="sp" style="background:rgba(210,180,140,0.15);">🌸</div>
      </div>
    </div>
  </div>
</div>
<script>
const d=document.getElementById('dots');
const c=['#f5d080','#c8a97a','#a8d89a','#e8c878','#d4b890','#f0e0c0'];
for(let i=0;i<35;i++){
  const el=document.createElement('div');
  el.className='dot';
  const s=Math.random()*5+2;
  el.style.cssText=`width:${s}px;height:${s}px;left:${Math.random()*100}%;top:${Math.random()*100}%;background:${c[Math.floor(Math.random()*c.length)]};opacity:${Math.random()*0.45+0.15};`;
  d.appendChild(el);
}
</script>
</body>
</html>