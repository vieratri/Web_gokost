<?php
require_once '../config/functions.php';
require_role('admin');
$totUsers = $koneksi->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$ordersRunning = $koneksi->query("SELECT COUNT(*) as c FROM orders WHERE status='onprogress'")->fetch_assoc()['c'];
$totOrders = $koneksi->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$result = $koneksi->query("SELECT DATE(created_at) as d, SUM(amount) as s FROM orders GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC LIMIT 30");
$salesData = [];
while($r = $result->fetch_assoc()) $salesData[] = $r;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar navbar-expand bg-dark navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Gokost Admin</a>
    <div class="d-flex">
      <a class="navbar-brand" href="manage_users.php">Data User</a>
      <a class="navbar-brand" href="payments.php">Pembayaran</a>
      <a class="navbar-brand" href="reports.php">Laporan</a>
      <a class="navbar-brand" href="settings.php">Pengaturan</a>
      
      <a class="btn btn-outline-warning" href="../logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container my-4">
  <div class="row">
    <div class="col-md-3"><div class="card p-3">Total User Aktif<div class="h3"><?php echo $totUsers; ?></div></div></div>
    <div class="col-md-3"><div class="card p-3">Order Berjalan<div class="h3"><?php echo $ordersRunning; ?></div></div></div>
    <div class="col-md-3"><div class="card p-3">Total Order<div class="h3"><?php echo $totOrders; ?></div></div></div>
  </div>
  <div class="mt-4 card p-3">
    <h5>Grafik Penjualan</h5>
    <canvas id="salesChart" height="100"></canvas>
  </div>
</div>
<script>
const sales = <?php echo json_encode($salesData); ?>;
const labels = sales.map(s => s.d).reverse();
const data = sales.map(s => parseFloat(s.s));
const ctx = document.getElementById('salesChart');
new Chart(ctx, {type:'line', data:{labels: labels, datasets:[{label:'Penjualan', data: data, fill:false}]}});
</script>
</body>
</html>