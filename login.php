<?php
require_once 'config/functions.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $identity = e($_POST['identity']);
    $password = $_POST['password'];
    $stmt = $koneksi->prepare("SELECT id,username,email,password,role,status FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param('ss',$identity,$identity);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        if($row['status'] !== 'active'){ $err='Akun tidak aktif.'; }
        elseif(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            if($row['role'] === 'admin') header('Location: admin/dashboard.php');
            else header('Location: user/dashboard.php');
            exit;
        } else $err = 'Kredensial salah.';
    } else $err = 'User tidak ditemukan.';
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Gokost</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Login </h4>
          <?php if(!empty($err)): ?>
            <div class="alert alert-danger"><?php echo e($err); ?></div>
          <?php endif; ?>

          <?php if(isset($_GET['registered'])): ?>
            <div class="alert alert-success">Pendaftaran berhasil. Silakan login.</div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-2"><input name="identity" class="form-control" placeholder="Username atau Email"></div>
            <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Password"></div>
            <div class="d-flex justify-content-between">
              <a href="register.php" class="btn btn-outline-secondary">Register</a>
              <a href="reset_password.php" class="btn btn-outline-warning">Reset Password</a>
              <button type="submit" class="btn btn-primary">Login</button>
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