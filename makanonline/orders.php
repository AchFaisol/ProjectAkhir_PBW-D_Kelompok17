<?php
session_start();
include 'config/db.php';
include 'partials/navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Soft delete: tandai pesanan sebagai dihapus oleh pembeli
if (isset($_GET['hapus'])) {
    $id_pesanan = intval($_GET['hapus']);
    $cek = $conn->query("SELECT * FROM pesanan WHERE id = $id_pesanan AND user_id = $user_id LIMIT 1");
    if ($cek && $cek->num_rows > 0) {
        $pesanan = $cek->fetch_assoc();
        if (strtolower($pesanan['status']) === 'selesai') {
            $conn->query("UPDATE pesanan SET is_deleted_pembeli = 1 WHERE id = $id_pesanan AND user_id = $user_id");
        }
    }
    header("Location: orders.php");
    exit;
}

// Hanya tampilkan pesanan yang belum dihapus oleh pembeli
$orders = $conn->query("
    SELECT p.*, m.nama AS nama_makanan 
    FROM pesanan p 
    JOIN makanan m ON p.makanan_id = m.id 
    WHERE p.user_id = $user_id AND (p.is_deleted_pembeli IS NULL OR p.is_deleted_pembeli = 0)
    ORDER BY p.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - MakanOnline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom right, #fcd34d, #f87171, #ef4444);
            font-family: ui-sans-serif, system-ui;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body class="text-gray-800">

<main class="container mx-auto px-4 py-10 flex-grow">
    <h2 class="text-3xl font-bold text-white text-center mb-8 drop-shadow-lg">Riwayat Pesanan Saya</h2>

    <div class="bg-white rounded-xl shadow-lg p-6 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2">ID</th>
                    <th>Tanggal</th>
                    <th>Makanan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($o = $orders->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2"><?= $o['id'] ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($o['created_at'])) ?></td>
                        <td><?= htmlspecialchars($o['nama_makanan']) ?></td>
                        <td>Rp<?= number_format($o['total']) ?></td>
                        <td>
                            <span class="px-2 py-1 rounded text-white
                                <?= strtolower($o['status']) === 'selesai' ? 'bg-green-500' : 
                                     (strtolower($o['status']) === 'diproses' ? 'bg-yellow-500' : 'bg-gray-400') ?>">
                                <?= htmlspecialchars(ucfirst($o['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (strtolower($o['status']) === 'selesai'): ?>
                                <a href="?hapus=<?= $o['id'] ?>"
                                   onclick="return confirm('Yakin ingin menghapus pesanan ini?')"
                                   class="text-red-600 hover:underline font-medium">Hapus</a>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Tidak Bisa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

</body> 
</html>
