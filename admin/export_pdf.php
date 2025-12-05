<?php
require_once __DIR__ . '/../config/functions.php'; require_role('admin');
require __DIR__ . '/../vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$html = '<h2>Laporan Pesanan</h2><table border="1" width="100%"><tr><th>ID</th><th>User</th><th>Item</th><th>Amount</th><th>Status</th></tr>';
$res = $koneksi->query("SELECT o.id, u.username, o.item_name, o.amount, o.status FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC");
while($r = $res->fetch_assoc()){
    $html .= '<tr><td>'.e($r['id']).'</td><td>'.e($r['username']).'</td><td>'.e($r['item_name']).'</td><td>'.e($r['amount']).'</td><td>'.e($r['status']).'</td></tr>';
}
$html .= '</table>';
$mpdf->WriteHTML($html);
$mpdf->Output('laporan_pesanan_'.date('Ymd_His').'.pdf','D');
exit;
?>