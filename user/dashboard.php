<?php
require_once '../config/functions.php'; require_role('user');
$uid = $_SESSION['user_id'];
$orders = $koneksi->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC"); $orders->bind_param('i',$uid); $orders->execute(); $orders = $orders->get_result();
?>
<!doctype html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Gokost</a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-secondary">logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
  <h4>Selamat datang, <?php echo e($_SESSION['username']); ?></h4>
  <div class="row">
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Pesan Makanan</h5>
        <form action="../order_submit.php" method="post">
          <input type="hidden" name="type" value="makanan">
          <div class="mb-2"><input name="item_name" class="form-control" placeholder="Nama makanan"></div>
          <div class="mb-2"><input name="pickup_location" class="form-control" placeholder="Lokasi pesanan"></div>
          <div class="mb-2"><input name="delivery_location" class="form-control" placeholder="Lokasi pemesan"></div>
          <div class="mb-2"><textarea name="description" class="form-control" placeholder="Deskripsi"></textarea></div>
          <div class="mb-2"><input name="amount" class="form-control" placeholder="Jumlah (Rp)"></div>
          <button class="btn btn-primary">Pesan</button>
        </form>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3">
        <h5>Pesan Barang / Express</h5>
        <form action="../order_submit.php" method="post">
          <input type="hidden" name="type" value="barang">
          <div class="mb-2"><input name="item_name" class="form-control" placeholder="Nama barang"></div>
          <div class="mb-2"><input name="pickup_location" class="form-control" placeholder="Lokasi pengambilan"></div>
          <div class="mb-2"><input name="delivery_location" class="form-control" placeholder="Lokasi pengantaran"></div>
          <div class="mb-2"><textarea name="description" class="form-control" placeholder="Deskripsi"></textarea></div>
          <div class="mb-2"><input name="amount" class="form-control" placeholder="Jumlah Pesanan"></div>
          <button class="btn btn-primary">Pesan</button>
        </form>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <h5>Notifikasi & Perkembangan Pesanan</h5>
    <table class="table">
    <thead><tr><th>#</th><th>Item</th><th>Status</th><th>Dibuat</th></tr></thead>
    <tbody>
    <?php while($o = $orders->fetch_assoc()): ?>
      <tr><td><?php echo $o['id']; ?></td><td><?php echo e($o['item_name']); ?></td><td><?php echo e($o['status']); ?></td><td><?php echo e($o['created_at']); ?></td></tr>
    <?php endwhile; ?>
    </tbody>
    </table>
  </div>
</div>
</body>
</html>