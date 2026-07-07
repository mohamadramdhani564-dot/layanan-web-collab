<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login Admin</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      height: 100vh;
      background: url('assets/img/bg-login.jpg') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      width: 380px;
      padding: 40px;
      border-radius: 15px;
      background: rgba(0, 0, 0, 0.65);
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      text-align: center;
      color: #fff;
    }

    .login-box h2 {
      margin-bottom: 20px;
      letter-spacing: 2px;
    }

    .line {
      width: 100%;
      height: 2px;
      background: linear-gradient(to right, #8e2de2, #4a00e0);
      margin-bottom: 25px;
    }

    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }

    .input-group input {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: none;
      outline: none;
    }

    .btn-login {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 20px;
      background: linear-gradient(to right, #8e2de2, #4a00e0);
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    .register {
      margin-top: 15px;
      font-size: 13px;
    }

    .register a {
      color: #ccc;
      text-decoration: underline;
    }
  </style>
</head>

<body>

<div class="login-box">
  <h2>MASUK</h2>
  <div class="line"></div>

  <form method="POST">
    <div class="input-group">
      <input type="text" name="username" placeholder="Nama unik" required>
    </div>

    <div class="input-group">
      <input type="password" name="password" placeholder="Kata sandi" required>
    </div>

    <button name="login" class="btn-login">Masuk</button>
  </form>

<?php
session_start();
include 'config/koneksi.php';

if(isset($_POST['login'])){
  $u = mysqli_real_escape_string($conn,$_POST['username']);
  $p = md5($_POST['password']);

  $cek = mysqli_query($conn,"
  SELECT * FROM admin
  WHERE username='$u'
  AND password='$p'
  ");

  if(mysqli_num_rows($cek) > 0){
    $_SESSION['login'] = true;

    echo "<script>
      Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
        text: 'Selamat datang admin',
        timer: 1500,
        showConfirmButton: false
      }).then(()=>{
        window.location='dashboard.php';
      });
    </script>";

  } else {
    echo "<script>
      Swal.fire({
        icon: 'error',
        title: 'Login Gagal!',
        text: 'Username atau Password salah'
      });
    </script>";
  }
}
?>

</body>
</html>