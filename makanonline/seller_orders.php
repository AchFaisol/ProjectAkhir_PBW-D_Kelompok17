<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'penjual') {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['user']['id'];

// Soft delete pesanan
if (isset($_POST['delete_order'])) {
    $orderId = $_POST['order_id'];
    $stmt = $conn->prepare("UPDATE pesanan SET is_deleted = 1 WHERE id = ? AND seller_id = ? AND status = 'selesai'");
    $stmt->bind_param("ii", $orderId, $seller_id);
    $stmt->execute();
    $stmt->close();
}

// Update status pesanan
if (isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    $stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("sii", $newStatus, $orderId, $seller_id);
    $stmt->execute();
    $stmt->close();
}

// Ambil pesanan masuk (yang belum dihapus)
$query = "
    SELECT p.id, u.username, m.nama AS nama_makanan, p.total, p.status, p.created_at 
    FROM pesanan p
    JOIN users u ON p.user_id = u.id
    JOIN makanan m ON p.makanan_id = m.id
    WHERE p.seller_id = ? AND p.is_deleted = 0
    ORDER BY p.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - MakanOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom right, #fde68a, #fca5a5, #ef4444);
            font-family: ui-sans-serif, system-ui;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body class="text-gray-800">

<?php include 'partials/navbar.php'; ?>

<main class="container mx-auto px-4 py-10 flex-grow">
    <h2 class="text-3xl font-bold text-white text-center mb-8 drop-shadow-lg">Pesanan Masuk</h2>

    <div class="w-full overflow-x-auto">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 inline-block min-w-full align-middle">
            <table class="min-w-[700px] w-full text-xs sm:text-sm text-center">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2">ID</th>
                        <th>Pembeli</th>
                        <th>Makanan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2"><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['nama_makanan']) ?></td>
                        <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td>
                            <form method="POST" class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <select name="status" class="border px-2 py-1 rounded text-sm bg-white">
                                    <?php
                                    $statuses = ['menunggu', 'Diproses', 'Dikirim', 'selesai'];
                                    foreach ($statuses as $status) {
                                        $selected = $row['status'] === $status ? 'selected' : '';
                                        echo "<option value='$status' $selected>" . ucfirst($status) . "</option>";
                                    }
                                    ?>
                                </select>
                        </td>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <div class="flex flex-wrap gap-2 justify-center items-center mt-2 sm:mt-0">
                                <button type="submit" name="update_status"
                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-all duration-200">
                                    Simpan
                                </button>
                            </form>

                            <?php if ($row['status'] === 'selesai'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_order"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-all duration-200"
                                    onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                                    🔚 Hapus
                                </button>
                            </form>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
