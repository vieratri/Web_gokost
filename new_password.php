<?php
require_once 'config/functions.php';
if(empty($_SESSION['reset_identifier'])) header('Location: reset_password.php');
$identifier = $_SESSION['reset_identifier'];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $pw = $_POST['password']; $conf = $_POST['confirm_password'];
    if($pw !== $conf) $err='Konfirmasi password tidak sama.';
    else{
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        if(filter_var($identifier, FILTER_VALIDATE_EMAIL)){
            $up = $koneksi->prepare("UPDATE users SET password=? WHERE email=?");
            $up->bind_param('ss',$hash,$identifier);
        } else {
            $up = $koneksi->prepare("UPDATE users SET password=? WHERE phone=?");
            $up->bind_param('ss',$hash,$identifier);
        }
        if($up->execute()){
            unset($_SESSION['reset_identifier']);
            header('Location: login.php?reset=1'); exit;
        } else $err='Gagal menyimpan password baru.';
    }
}
?>
<!doctype html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-5"><div class="card"><div class="card-body">
<h5>Set Password Baru</h5>
<?php if(!empty($err)) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post">
  <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Password baru"></div>
  <div class="mb-2"><input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password"></div>
  <button class="btn btn-primary">Reset Password</button>
</form>
</div></div></div></div></div>
</body></html>