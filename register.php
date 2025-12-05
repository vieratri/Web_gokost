<?php
require_once 'config/functions.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = e($_POST['username']);
    $fullname = e($_POST['fullname']);
    $email = e($_POST['email']);
    $phone = e($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'user';

    $errors = [];
    if(!$username || !$email || !$password) $errors[] = 'Username, email dan password wajib diisi.';
    if($password !== $confirm) $errors[] = 'Password dan konfirmasi tidak sama.';

    if(empty($errors)){
        $stmt = $koneksi->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param('ss',$username,$email);
        $stmt->execute(); $stmt->store_result();
        if($stmt->num_rows>0){ $errors[]='Username atau email sudah terdaftar.'; }
        else{
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $koneksi->prepare("INSERT INTO users (username,fullname,email,phone,password,role) VALUES (?,?,?,?,?,?)");
            $ins->bind_param('ssssss',$username,$fullname,$email,$phone,$hash,$role);
            if($ins->execute()){
                header('Location: login.php?registered=1'); exit;
            } else $errors[] = 'Gagal menyimpan data.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Gokost</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Daftar Gokost</h4>
          <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach($errors as $er) echo '<li>'.e($er).'</li>'; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-2"><input name="username" class="form-control" placeholder="Username"></div>
            <div class="mb-2"><input name="fullname" class="form-control" placeholder="Nama lengkap"></div>
            <div class="mb-2"><input name="email" class="form-control" placeholder="Email"></div>
            <div class="mb-2"><input name="phone" class="form-control" placeholder="No. Telp"></div>
            <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Password"></div>
            <div class="mb-2"><input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password"></div>
            <div class="mb-2">
              <select name="role" class="form-select">
                <option value="user">Daftar sebagai: User</option>
                <option value="admin">Daftar sebagai: Admin</option>
              </select>
            </div>
            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary">Daftar</button>
              <a href="login.php" class="btn btn-outline-secondary">Sudah punya akun? Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>