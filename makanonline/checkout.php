<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['alamat'])) {
    echo "Alamat pengiriman wajib diisi.";
    exit;
}

$alamat = $conn->real_escape_string($_POST['alamat']);

$query = "SELECT k.*, m.harga, m.seller_id 
          FROM keranjang k 
          JOIN makanan m ON k.makanan_id = m.id 
          WHERE k.user_id = $user_id";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("INSERT INTO pesanan (user_id, seller_id, makanan_id, total, alamat) VALUES (?, ?, ?, ?, ?)");

    while ($item = $result->fetch_assoc()) {
        $makanan_id = $item['makanan_id'];
        $jumlah     = $item['jumlah'];
        $harga      = $item['harga'];
        $seller_id  = $item['seller_id'];
        $total      = $jumlah * $harga;

        $stmt->bind_param("iiiis", $user_id, $seller_id, $makanan_id, $total, $alamat);
        $stmt->execute();
    }

    $stmt->close();
    $conn->query("DELETE FROM keranjang WHERE user_id = $user_id");
}

header("Location: orders.php");
exit;
?>
