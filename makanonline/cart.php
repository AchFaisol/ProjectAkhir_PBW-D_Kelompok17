<?php
session_start();
include 'config/db.php';
include 'partials/navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Tambah item ke keranjang
if (isset($_GET['add'])) {
    $makanan_id = intval($_GET['add']);
    $check = $conn->query("SELECT * FROM keranjang WHERE user_id=$user_id AND makanan_id=$makanan_id");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE keranjang SET jumlah = jumlah + 1 WHERE user_id=$user_id AND makanan_id=$makanan_id");
    } else {
        $conn->query("INSERT INTO keranjang (user_id, makanan_id, jumlah) VALUES ($user_id, $makanan_id, 1)");
    }
    header("Location: cart.php");
    exit;
}

// Hapus item dari keranjang
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    $conn->query("DELETE FROM keranjang WHERE id=$id AND user_id=$user_id");
    header("Location: cart.php");
    exit;
}

// Update jumlah item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $item_id = intval($_POST['item_id']);
    $jumlah_baru = max(1, intval($_POST['jumlah_baru']));
    $conn->query("UPDATE keranjang SET jumlah = $jumlah_baru WHERE id = $item_id AND user_id = $user_id");
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang - MakanOnline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom right, #fde68a, #fca5a5, #ef4444);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body class="text-gray-800">

<main class="container mx-auto px-4 py-10 flex-grow">
    <h2 class="text-3xl font-bold text-white text-center mb-10 drop-shadow-lg">Keranjang Saya</h2>

    <?php
    $items = $conn->query("SELECT k.id, m.nama, m.harga, k.jumlah FROM keranjang k JOIN makanan m ON k.makanan_id = m.id WHERE k.user_id = $user_id");
    $total = 0;
    ?>

    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-x-auto">
        <table class="w-full text-sm text-center min-w-[600px]">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2">Makanan</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($item = $items->fetch_assoc()):
                $subtotal = $item['harga'] * $item['jumlah'];
                $total += $subtotal;
            ?>
                <tr class="border-b hover:bg-gray-50">
                    <form method="post" class="contents">
                        <td class="py-2"><?= htmlspecialchars($item['nama']) ?></td>
                        <td>Rp<?= number_format($item['harga']) ?></td>
                        <td>
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="number" name="jumlah_baru" value="<?= $item['jumlah'] ?>" min="1"
                                class="w-20 text-center border border-gray-300 rounded-md px-2 py-1">
                        </td>
                        <td>Rp<?= number_format($subtotal) ?></td>
                        <td class="space-x-2">
                            <button type="submit" name="update" class="text-blue-600 hover:underline">Update</button>
                            <a href="?remove=<?= $item['id'] ?>"
                               onclick="return confirm('Yakin ingin menghapus item ini?')"
                               class="text-red-500 hover:underline">Hapus</a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php
    $items = $conn->query("SELECT k.id, m.nama, m.harga, k.jumlah FROM keranjang k JOIN makanan m ON k.makanan_id = m.id WHERE k.user_id = $user_id");
    ?>
    <form action="checkout.php" method="post" class="bg-white shadow-lg rounded-xl mt-8 p-6 space-y-4">
        <h3 class="text-xl font-bold mb-2 text-gray-800">Rincian Pemesanan</h3>
        <ul class="space-y-2">
            <?php while ($item = $items->fetch_assoc()):
                $subtotal = $item['harga'] * $item['jumlah'];
            ?>
                <li class="flex flex-col sm:flex-row justify-between border-b pb-2">
                    <div>
                        <p class="font-medium"><?= htmlspecialchars($item['nama']) ?> × <?= $item['jumlah'] ?></p>
                        <p class="text-sm text-gray-500">Rp<?= number_format($item['harga']) ?> / pcs</p>
                    </div>
                    <div class="text-right font-semibold text-gray-700">
                        Rp<?= number_format($subtotal) ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="mt-4">
            <label for="alamat" class="block font-semibold">Alamat Pengiriman</label>
            <textarea name="alamat" id="alamat" rows="3" required
                      class="w-full border border-gray-300 rounded-md p-3"
                      placeholder="Masukkan alamat lengkap pengiriman..."></textarea>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t">
            <div class="text-lg font-bold text-gray-700">Total: Rp<?= number_format($total) ?></div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-semibold w-full sm:w-auto">
                    Checkout
                </button>
                <a href="menu.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold w-full sm:w-auto text-center">
                    Tambah Pesanan
                </a>
            </div>
        </div>
    </form>
</main>