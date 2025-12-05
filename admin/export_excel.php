<?php
require_once __DIR__ . '/../config/functions.php';
require_role('admin'); // pastikan fungsi ini berjalan

require_once __DIR__ . '/../config/koneksi.php'; // pastikan koneksi tersedia

require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// CLEAR BUFFER agar file XLSX tidak corrupt
if (ob_get_length()) ob_end_clean();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'Order ID');
$sheet->setCellValue('B1', 'User');
$sheet->setCellValue('C1', 'Item');
$sheet->setCellValue('D1', 'Amount');
$sheet->setCellValue('E1', 'Status');

// Query data
$query = "
    SELECT o.id, u.username, o.item_name, o.amount, o.status 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
";
$res = $koneksi->query($query);

// Cek error SQL
if (!$res) {
    die("Query Error: " . $koneksi->error);
}

// Isi data ke Excel
$row = 2;
while ($r = $res->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $r['id']);
    $sheet->setCellValue('B' . $row, $r['username']);
    $sheet->setCellValue('C' . $row, $r['item_name']);
    $sheet->setCellValue('D' . $row, $r['amount']);
    $sheet->setCellValue('E' . $row, $r['status']);
    $row++;
}

$writer = new Xlsx ($spreadsheet);

// Nama file
$filename = 'orders_' . date('Ymd_His') . '.xlsx';

// Header download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output file
$writer->save('php://output');
exit;
