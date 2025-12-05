<?php
require_once '../config/functions.php'; require_role('admin');
if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    $u = $koneksi->prepare("SELECT status FROM users WHERE id=?"); $u->bind_param('i',$id); $u->execute(); $r = $u->get_result()->fetch_assoc();
    $new = $r['status']==='active' ? 'inactive' : 'active';
    $up = $koneksi->prepare("UPDATE users SET status=? WHERE id=?"); $up->bind_param('si', $new, $id); $up->execute();
    header('Location: manage_users.php'); exit;
}
$users = $koneksi->query("SELECT id,username,fullname,email,phone,role,status,created_at FROM users ORDER BY created_at DESC");
?>
<!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container mt-4">
<h4>Daftar Pengguna</h4>
<table class="table table-striped"><thead><tr><th>#</th><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php while($row = $users->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo e($row['username']); ?></td>
<td><?php echo e($row['fullname']); ?></td>
<td><?php echo e($row['email']); ?></td>
<td><?php echo e($row['role']); ?></td>
<td><?php echo e($row['status']); ?></td>
<td><a href="?toggle=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Toggle Status</a></td>
</tr>
<?php endwhile; ?></tbody></table>
</div></body></html>