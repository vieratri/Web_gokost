<?php
// config/functions.php
require_once __DIR__ . '/db.php';
session_start();

function e($s){ return htmlspecialchars(trim($s), ENT_QUOTES); }

function send_email_otp($to, $otp){
    $subject = "Gokost - OTP Code";
    $message = "Kode OTP Anda: $otp";
    $headers = "From: no-reply@gokost.local" . "\r\n";
    return mail($to, $subject, $message, $headers);
}

function generate_otp(){
    return rand(100000,999999);
}

function is_logged(){
    return isset($_SESSION['user_id']);
}

function require_role($role){
    if(!is_logged() || $_SESSION['role'] !== $role){
        header('Location: /login.php'); exit;
    }
}

// Twilio SMS helper (requires twilio/sdk via composer)
function send_sms_otp($to, $otp){
    $twilioFile = __DIR__ . '/twilio.php';
    if(!file_exists($twilioFile)) return false;
    $cfg = require $twilioFile;
    if(empty($cfg['sid']) || empty($cfg['token']) || empty($cfg['from'])) return false;
    try{
        require_once __DIR__ . '/../vendor/autoload.php';
        $client = new \Twilio\RestClient($cfg['sid'], $cfg['token']);
        $client->messages->create($to, [
            'from' => $cfg['from'],
            'body' => "Kode OTP Gokost: $otp"
        ]);
        return true;
    } catch(Exception $e){
        error_log('Twilio error: '.$e->getMessage());
        return false;
    }
}
?>