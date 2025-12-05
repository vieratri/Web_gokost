<?php
require_once 'config/functions.php';
$sent=false; $msg='';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $identifier = e($_POST['identifier']); // email or phone
    if(filter_var($identifier, FILTER_VALIDATE_EMAIL)){
        $stmt = $koneksi->prepare("SELECT id,email FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s',$identifier);
    } else {
        $stmt = $koneksi->prepare("SELECT id,phone FROM users WHERE phone=? LIMIT 1");
        $stmt->bind_param('s',$identifier);
    }
    $stmt->execute(); $res = $stmt->get_result();
    if($user = $res->fetch_assoc()){
        $otp = generate_otp();
        $expires = date('Y-m-d H:i:s', time() + 10*60); // 10 minutes
        $ins = $koneksi->prepare("INSERT INTO otps (user_identifier, otp_code, expires_at) VALUES (?,?,?)");
        $ins->bind_param('sss', $identifier, $otp, $expires);
        if($ins->execute()){
            if(filter_var($identifier, FILTER_VALIDATE_EMAIL)){
                send_email_otp($identifier, $otp);
            } else {
                // try SMS via Twilio if configured
                send_sms_otp($identifier, $otp);
            }
            $sent = true;
            $msg = 'Kode OTP telah dikirim ke ' . $identifier;
        }
    } else {
        $msg = 'Email atau nomor tidak ditemukan.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password - Gokost</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Reset Password</h4>
          <?php if($msg): ?><div class="alert alert-info"><?php echo e($msg); ?></div><?php endif; ?>
          <?php if(!$sent): ?>
          <form method="post">
            <div class="mb-2"><input name="identifier" class="form-control" placeholder="Email atau No. Telp"></div>
            <div class="d-flex justify-content-between">
              <a href="login.php" class="btn btn-outline-secondary">Kembali</a>
              <button type="submit" class="btn btn-warning">Kirim Kode OTP</button>
            </div>
          </form>
          <?php else: ?>
            <hr>
            <form action="verify_otp.php" method="post">
              <input type="hidden" name="identifier" value="<?php echo e($identifier); ?>">
              <div class="mb-2"><input name="otp" class="form-control" placeholder="Masukkan kode OTP"></div>
              <button class="btn btn-primary">Verifikasi OTP</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>