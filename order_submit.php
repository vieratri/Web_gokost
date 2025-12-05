<?php
require_once 'config/functions.php';
if(!is_logged()) header('Location: login.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $uid = $_SESSION['user_id'];
    $type = e($_POST['type']);
    $item = e($_POST['item_name']);
    $pickup = e($_POST['pickup_location']);
    $delivery = e($_POST['delivery_location']);
    $desc = e($_POST['description']);
    $amount = floatval($_POST['amount']);
    $ins = $koneksi->prepare("INSERT INTO orders (user_id,type,item_name,pickup_location,delivery_location,description,amount) VALUES (?,?,?,?,?,?,?)");
    $ins->bind_param('isssssd',$uid,$type,$item,$pickup,$delivery,$desc,$amount);
    if($ins->execute()){
        $order_id = $ins->insert_id;
        $tr = $koneksi->prepare("INSERT INTO transactions (order_id,user_id,method,amount) VALUES (?,?,?,?)");
        $method = 'WA'; $tr->bind_param('iisd',$order_id,$uid,$method,$amount); $tr->execute();
        $adminPhone = '+6285269683206';
        $text = urlencode("Order ID: $order_id\nUser: {$_SESSION['username']}\nItem: $item\nAmount: Rp $amount\nPickup: $pickup\nDelivery: $delivery\nDesc: $desc");
        header('Location: https://wa.me/'.$adminPhone.'?text='.$text);
        exit;
    } else {
        echo 'Gagal membuat order';
    }
}
?>