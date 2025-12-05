<?php
// config/db.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'gokost_db';

$koneksi = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if($koneksi->connect_error){
    die('Connection error: '.$koneksi->connect_error);
}
$koneksi->set_charset('utf8mb4');
?>