<?php
require_once 'config/functions.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') header('Location: reset_password.php');
$identifier = e($_POST['identifier']);
$otp = e($_POST['otp']);
$now = date('Y-m-d H:i:s');
$stmt = $koneksi->prepare("SELECT id, used, expires_at FROM otps WHERE user_identifier=? AND otp_code=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('ss',$identifier,$otp);
$stmt->execute(); $res = $stmt->get_result();
if($row = $res->fetch_assoc()){
    if($row['used']){ $err='OTP sudah digunakan.'; }
    elseif($row['expires_at'] < $now) $err='OTP kedaluwarsa.';
    else{
        $up = $koneksi->prepare("UPDATE otps SET used=1 WHERE id=?");
        $up->bind_param('i', $row['id']); $up->execute();
        $_SESSION['reset_identifier'] = $identifier;
        header('Location: new_password.php'); exit;
    }
} else $err='OTP tidak valid.';
?>
<!doctype html>
<html><body>
<?php if(isset($err)) echo '<p>'.$err.'</p><a href="reset_password.php">Kembali</a>'; ?>
</body></html>