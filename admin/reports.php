<?php
require_once '../config/functions.php'; require_role('admin');
$start = isset($_GET['start_date']) ? e($_GET['start_date']) : '';
$end = isset($_GET['end_date']) ? e($_GET['end_date']) : '';
$page = isset($_GET['page']) ? max(1,intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(5,intval($_GET['per_page'])) : 10;
$offset = ($page-1)*$per_page;

$where = [];
$params = [];
$types = '';
if($start){ $where[] = "o.created_at >= ?"; $params[] = $start . ' 00:00:00'; $types .= 's'; }
if($end){ $where[] = "o.created_at <= ?"; $params[] = $end . ' 23:59:59'; $types .= 's'; }
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmtCount = $koneksi->prepare("SELECT COUNT(*) as c FROM orders o JOIN users u ON o.user_id=u.id $where_sql");
if($params){ $stmtCount->bind_param($types, ...$params); }
$stmtCount->execute(); $total = $stmtCount->get_result()->fetch_assoc()['c'];

$sql = "SELECT o.id, u.username, o.item_name, o.amount, o.status, o.created_at FROM orders o JOIN users u ON o.user_id=u.id $where_sql ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($sql);
if($params){
    $bind_types = $types . 'ii';
    $bind_vals = array_merge($params, [$per_page, $offset]);
    $stmt->bind_param($bind_types, ...$bind_vals);
} else {
    $stmt->bind_param('ii', $per_page, $offset);
}
$stmt->execute(); $res = $stmt->get_result();
$total_pages = max(1, ceil($total / $per_page));

function url_for($overrides=[]){
    $q = array_merge($_GET, $overrides);
    return '?'.http_build_query($q);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<title>Admin - Laporan Pesanan</title>
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Laporan Pesanan</h4>
    <div>
      <a href="export_excel.php?<?php echo http_build_query($_GET);?>" class="btn btn-success">Export Excel</a>
      <a href="export_pdf.php?<?php echo http_build_query($_GET);?>" class="btn btn-danger">Export PDF</a>
    </div>
  </div>

  <form class="row g-2 mb-3">
    <div class="col-auto">
      <input type="date" name="start_date" class="form-control" value="<?php echo e($start); ?>" placeholder="Start date">
    </div>
    <div class="col-auto">
      <input type="date" name="end_date" class="form-control" value="<?php echo e($end); ?>" placeholder="End date">
    </div>
    <div class="col-auto">
      <select name="per_page" class="form-select">
        <?php foreach([5,10,20,50] as $pp): ?>
          <option value="<?php echo $pp;?>" <?php if($per_page==$pp) echo 'selected';?>><?php echo $pp;?> per halaman</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Filter</button>
      <a href="reports.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
  <table class="table table-striped">
    <thead><tr><th>#</th><th>User</th><th>Item</th><th>Amount</th><th>Status</th><th>Created</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php while($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo e($r['id']);?></td>
        <td><?php echo e($r['username']);?></td>
        <td><?php echo e($r['item_name']);?></td>
        <td>Rp <?php echo number_format($r['amount'],0,',','.');?></td>
        <td><?php echo e($r['status']);?></td>
        <td><?php echo e($r['created_at']);?></td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick="previewPDF(<?php echo $r['id']; ?>)">Preview PDF</button>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

  <div class="d-flex justify-content-between align-items-center">
    <div>Menampilkan halaman <?php echo $page;?> dari <?php echo $total_pages;?> — total <?php echo $total;?> data</div>
    <nav>
      <ul class="pagination mb-0">
        <?php for($p=1;$p<=$total_pages;$p++): ?>
          <li class="page-item <?php if($p==$page) echo 'active'; ?>"><a class="page-link" href="<?php echo url_for(['page'=>$p]);?>"><?php echo $p;?></a></li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>
</div>

<div class="modal fade" id="pdfModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Preview PDF</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-0"><iframe id="pdfFrame" style="width:100%;height:80vh;border:0"></iframe></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewPDF(orderId){
  const params = new URLSearchParams(window.location.search);
  params.set('order_id', orderId);
  const url = 'preview_pdf.php?' + params.toString();
  document.getElementById('pdfFrame').src = url;
  const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
  modal.show();
}
</script>
</body>
</html>