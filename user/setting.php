<?php
include "../config/koneksi.php";
session_start();

$user_id = $_SESSION['user_id'] ?? 1;

// ambil user (pakai id_user)
$user = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT * FROM user WHERE id_user='$user_id'")
) ?? [];

// ambil setting user
$settings = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT * FROM user_settings WHERE user_id='$user_id'")
) ?? [];

// aman dari null
$username = $user['username'] ?? '';

$theme  = $settings['theme'] ?? 'light';
$color  = $settings['color'] ?? 'gold';
$lang   = $settings['language'] ?? 'id';

$notif_order   = $settings['notif_order'] ?? 1;
$notif_payment = $settings['notif_payment'] ?? 1;
$sound         = $settings['sound'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Settings</title>

<style>
body{
font-family:Poppins;
background:#f4f6f9;
margin:0;
display:flex;
}

/* SIDEBAR */
.sidebar{
width:240px;
background:#b79055;
color:white;
padding:25px;
height:100vh;
}

.sidebar a{
display:block;
padding:12px;
color:white;
text-decoration:none;
border-radius:10px;
}

.sidebar a.active{
background:rgba(255,255,255,0.2);
}

/* CONTENT */
.content{
padding:30px;
flex:1;
}

/* CARD */
.card{
background:white;
padding:25px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

/* INPUT */
input, select{
width:100%;
padding:10px;
margin-top:8px;
margin-bottom:15px;
border-radius:8px;
border:1px solid #ddd;
}

/* BUTTON */
button{
background:#b79055;
color:white;
padding:12px 25px;
border:none;
border-radius:10px;
cursor:pointer;
font-weight:500;
}

/* TOGGLE */
.switch {
position: relative;
display: inline-block;
width: 50px;
height: 25px;
}

.switch input {
display:none;
}

.slider {
position: absolute;
cursor: pointer;
background-color: #ccc;
border-radius: 25px;
top: 0;
left: 0;
right: 0;
bottom: 0;
transition: .3s;
}

.slider:before {
position: absolute;
content: "";
height: 19px;
width: 19px;
left: 3px;
bottom: 3px;
background: white;
border-radius: 50%;
transition: .3s;
}

input:checked + .slider {
background-color: #b79055;
}

input:checked + .slider:before {
transform: translateX(24px);
}

/* FLEX ROW */
.row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
}
</style>
</head>

<body>

<div class="sidebar">
<h2>Seoullicious</h2>

<a href="pos.php">Menu</a>
<a href="history.php">History</a>
<a href="setting.php" class="active">Setting</a>
<a href="../auth/logout.php">Logout</a>
</div>

<div class="content">

<h1>⚙️ User Settings</h1>

<form action="../process/update_user_setting.php" method="POST">

<!-- ACCOUNT -->
<div class="card">
<h3>👤 Account</h3>

<label>Username</label>
<input type="text" name="username" value="<?= $username ?>">

<label>New Password</label>
<input type="password" name="password" placeholder="Kosongkan jika tidak diganti">
</div>

<!-- NOTIFICATION -->
<div class="card">
<h3>🔔 Notifications</h3>

<div class="row">
<span>Order Notification</span>
<label class="switch">
<input type="checkbox" name="notif_order" <?= $notif_order ? 'checked' : '' ?>>
<span class="slider"></span>
</label>
</div>

<div class="row">
<span>Payment Notification</span>
<label class="switch">
<input type="checkbox" name="notif_payment" <?= $notif_payment ? 'checked' : '' ?>>
<span class="slider"></span>
</label>
</div>

<div class="row">
<span>Sound Alert</span>
<label class="switch">
<input type="checkbox" name="sound" <?= $sound ? 'checked' : '' ?>>
<span class="slider"></span>
</label>
</div>

</div>

<!-- THEME -->
<div class="card">
<h3>🎨 Appearance</h3>

<label>Theme Mode</label>
<select name="theme">
<option value="light" <?= $theme=='light'?'selected':'' ?>>Light</option>
<option value="dark" <?= $theme=='dark'?'selected':'' ?>>Dark</option>
</select>

<label>Primary Color</label>
<select name="color">
<option value="gold" <?= $color=='gold'?'selected':'' ?>>Gold</option>
<option value="blue" <?= $color=='blue'?'selected':'' ?>>Blue</option>
<option value="green" <?= $color=='green'?'selected':'' ?>>Green</option>
</select>

</div>

<!-- LANGUAGE -->
<div class="card">
<h3>🌐 Language</h3>

<select name="language">
<option value="id" <?= $lang=='id'?'selected':'' ?>>Indonesia</option>
<option value="en" <?= $lang=='en'?'selected':'' ?>>English</option>
</select>

</div>

<button type="submit" name="save">💾 Save Settings</button>

</form>

</div>

</body>
</html>