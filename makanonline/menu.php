<?php
session_start();
include 'config/db.php';
include 'partials/navbar.php';
$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

if ($user_id && $_SESSION['user']['role'] === 'penjual') {
    $menu = $conn->query("SELECT makanan.*, users.username AS username 
                          FROM makanan 
                          JOIN users ON makanan.seller_id = users.id 
                          WHERE makanan.seller_id != $user_id");
} else {
    $menu = $conn->query("SELECT makanan.*, users.username AS username 
                          FROM makanan 
                          JOIN users ON makanan.seller_id = users.id");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu Makanan - MakanOnline</title>
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
        <h2 class="text-3xl font-bold text-white text-center mb-10 drop-shadow-lg">Menu Makanan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <?php while ($row = $menu->fetch_assoc()): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>"
                         class="h-48 w-full object-cover"
                         alt="<?= htmlspecialchars($row['nama']) ?>">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-red-600"><?= htmlspecialchars($row['nama']) ?></h3>
                        <p class="text-sm text-gray-500 mb-1">Penjual: <?= htmlspecialchars($row['username']) ?></p>
                        <p class="text-gray-700 mb-3">Rp<?= number_format($row['harga']) ?></p>

                        <?php if (!isset($_SESSION['user'])): ?>
                            <button onclick="showCustomAlert()" class="bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded w-full transition">Tambah</button>
                        <?php else: ?>
                            <form method="GET" action="cart.php">
                                <input type="hidden" name="add" value="<?= $row['id'] ?>">
                                <button class="bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded w-full transition">Tambah ke Keranjang</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <div id="customAlert" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl text-center">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Butuh Login</h2>
            <p class="text-gray-600 mb-6">Anda harus login terlebih dahulu untuk memesan makanan. Lanjutkan ke halaman login?</p>
            <div class="flex justify-center space-x-4">
                <button onclick="redirectToLogin()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Ya, Login</button>
                <button onclick="closeAlert()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">Batal</button>
            </div>
        </div>
    </div>

    <!-- Script untuk Modal -->
    <script>
        function showCustomAlert() {
            document.getElementById('customAlert').classList.remove('hidden');
        }

        function redirectToLogin() {
            window.location.href = "login.php";
        }

        function closeAlert() {
            document.getElementById('customAlert').classList.add('hidden');
        }
    </script>
</body>
</html>
