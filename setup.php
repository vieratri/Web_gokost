<?php
// setup.php - jalankan sekali dari CLI: php setup.php
echo "Setup script: import SQL dan buat folder tmp/\n";
$dbfile = __DIR__ . '/gokost.sql';
require __DIR__ . '/config/db.php'; // gunakan credentials di config
$sql = file_get_contents($dbfile);
if(!$sql) die("gokost.sql tidak ditemukan\n");

$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
foreach($statements as $stmt){
    if(trim($stmt)==='') continue;
    if(!$koneksi->query($stmt)){
        echo "Gagal menjalankan statement: ". $koneksi->error . "\n";
    }
}

@mkdir(__DIR__ . '/tmp', 0777, true);
@chmod(__DIR__ . '/tmp', 0777);

echo "Selesai. Pastikan Anda menjalankan: composer install\n";
?>