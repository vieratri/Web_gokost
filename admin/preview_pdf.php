<?php
require_once __DIR__ . '/../config/functions.php'; require_role('admin');
require __DIR__ . '/../vendor/autoload.php';

use Mpdf\Mpdf;

$start = isset($_GET['start_date']) ? e($_GET['start_date']) : null;
$end = isset($_GET['end_date']) ? e($_GET['end_date']) : null;
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if($order_id){
    $stmt = $koneksi->prepare("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=? LIMIT 1");
    $stmt->bind_param('i', $order_id); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
    if(!$row) die('Order tidak ditemukan');
    $html = '<h3>Detail Order #'.e($row['id']).'</h3>';
    $html .= '<table width="100%" border="1" cellpadding="6" cellspacing="0">';
    $html .= '<tr><td><strong>User</strong></td><td>'.e($row['username']).'</td></tr>';
    $html .= '<tr><td><strong>Item</strong></td><td>'.e($row['item_name']).'</td></tr>';
    $html .= '<tr><td><strong>Pickup</strong></td><td>'.e($row['pickup_location']).'</td></tr>';
    $html .= '<tr><td><strong>Delivery</strong></td><td>'.e($row['delivery_location']).'</td></tr>';
    $html .= '<tr><td><strong>Amount</strong></td><td>Rp '.number_format($row['amount'],0,',','.').'</td></tr>';
    $html .= '<tr><td><strong>Status</strong></td><td>'.e($row['status']).'</td></tr>';
    $html .= '</table>';
} else {
    $where = [];
    $params = [];
    $types = '';
    if($start){ $where[] = "o.created_at >= ?"; $params[] = $start.' 00:00:00'; $types .= 's'; }
    if($end){ $where[] = "o.created_at <= ?"; $params[] = $end.' 23:59:59'; $types .= 's'; }
    $where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
    $sql = "SELECT o.id, u.username, o.item_name, o.amount, o.status, o.created_at FROM orders o JOIN users u ON o.user_id=u.id $where_sql ORDER BY o.created_at DESC";
    $stmt = $koneksi->prepare($sql);
    if($params) $stmt->bind_param($types, ...$params);
    $stmt->execute(); $res = $stmt->get_result();
    $html = '<h3>Daftar Pesanan</h3>'; $html .= '<table width="100%" border="1" cellpadding="6" cellspacing="0">';
    $html .= '<tr><th>ID</th><th>User</th><th>Item</th><th>Amount</th><th>Status</th><th>Created</th></tr>';
    while($r = $res->fetch_assoc()){
        $html .= '<tr><td>'.e($r['id']).'</td><td>'.e($r['username']).'</td><td>'.e($r['item_name']).'</td><td>Rp '.number_format($r['amount'],0,',','.').'</td><td>'.e($r['status']).'</td><td>'.e($r['created_at']).'</td></tr>';
    }
    $html .= '</table>';
}

$mpdf = new Mpdf(['tempDir' => __DIR__ . '/../tmp']);
$mpdf->WriteHTML('<style>table{border-collapse: collapse;}td,th{border:1px solid #ddd;padding:6px}</style>'.$html);
$mpdf->Output();
?>