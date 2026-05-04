<?php
session_start();
include "../config/koneksi.php";

if(isset($_POST['register'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    $allowed_roles = ['user', 'admin'];
    if(!in_array($role, $allowed_roles)){
        $error = "Role tidak valid.";
    } else {

        // Pakai kolom username (bukan id)
        $cek = mysqli_prepare($koneksi, "SELECT username FROM user WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if(mysqli_stmt_num_rows($cek) > 0){
            $error = "Username sudah digunakan.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan role ke kolom status
            $insert = mysqli_prepare($koneksi,
                "INSERT INTO user (username, password, status) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($insert, "sss", $username, $hashed_password, $role);

            if(mysqli_stmt_execute($insert)){
                $success = "Register berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Register POS</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>

body {
    height: 100vh;
    display: flex;
    font-family: 'Segoe UI', sans-serif;
}

.left {
    width: 50%;
    background: url('../upload/login_food.jpg');
    background-size: cover;
    background-position: center;
}

.right {
    width: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f5f0;
}

.box {
    width: 380px;
}

.title {
    font-size: 32px;
    font-weight: bold;
    color: #c28b3c;
}

.btn-main {
    background: #c28b3c;
    border: none;
}

.btn-main:hover {
    background: #a8732c;
}

.role-badge {
    display: inline-block;
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 6px;
    vertical-align: middle;
}
.badge-user  { background: #d4edda; color: #155724; }
.badge-admin { background: #f8d7da; color: #721c24; }

</style>

</head>

<body>

<div class="left"></div>

<div class="right">

    <div class="box">

        <h2 class="title">Register</h2>
        <p class="text-muted">Buat akun baru untuk POS</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Minimal 6 karakter" minlength="6" required>
            </div>

            <div class="form-group">
                <label>Daftar Sebagai</label>
                <select name="role" class="form-control">
                    <option value="user">👤 User</option>
                    <option value="admin">⚙️ Admin</option>
                </select>
                <small class="form-text text-muted mt-1">
                    <span class="role-badge badge-user">User</span>
                    <span class="role-badge badge-admin">Admin</span>
                </small>
            </div>

            <button name="register" class="btn btn-main btn-block text-white mt-2">
                Daftar Sekarang
            </button>

            <p class="mt-3 text-center">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>

        </form>

    </div>

</div>

</body>
</html>