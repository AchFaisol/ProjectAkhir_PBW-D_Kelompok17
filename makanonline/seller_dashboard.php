<?php
session_start();
include 'config/db.php';
include 'partials/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'penjual') {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user']['id'];

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $resultCheck = $conn->query("SELECT COUNT(*) as jumlah FROM pesanan WHERE makanan_id = $delete_id");
    $rowCheck = $resultCheck->fetch_assoc();

    if ($rowCheck['jumlah'] > 0) {
        echo "<script>alert('Menu ini tidak bisa dihapus karena masih ada pesanan terkait.');</script>";
    } else {
        $resultDel = $conn->query("SELECT gambar FROM makanan WHERE id = $delete_id AND seller_id = $id");
        if ($resultDel && $resultDel->num_rows > 0) {
            $rowDel = $resultDel->fetch_assoc();
            if ($rowDel['gambar'] && file_exists('uploads/' . $rowDel['gambar'])) {
                unlink('uploads/' . $rowDel['gambar']);
            }
            $conn->query("DELETE FROM makanan WHERE id = $delete_id AND seller_id = $id");
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $gambar = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $newFileName = md5(time() . $fileName) . '.' . $ext;
            $uploadDir = './uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
                $gambar = $newFileName;
            } else {
                echo "<script>alert('Gagal upload gambar');</script>";
            }
        } else {
            echo "<script>alert('Format file tidak didukung');</script>";
        }
    }

    if ($nama && $harga && $gambar) {
        $stmt = $conn->prepare("INSERT INTO makanan (nama, harga, gambar, seller_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $nama, $harga, $gambar, $id);
        $stmt->execute();
        $stmt->close();
    }
}

$menu = $conn->query("SELECT * FROM makanan WHERE seller_id = $id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penjual - MakanOnline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom right, #facc15, #f87171, #ef4444);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-gray-800">

<main class="container mx-auto px-4 py-10 flex-grow">
    <h2 class="text-3xl font-bold text-white text-center mb-10 drop-shadow-lg">Kelola Menu Makanan</h2>

    <div class="bg-white p-6 rounded-xl shadow mb-8 max-w-2xl mx-auto">
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Nama Makanan</label>
                <input name="nama" required class="w-full border p-2 rounded" placeholder="Contoh: Nasi Goreng">
            </div>
            <div>
                <label class="block font-semibold mb-1">Harga</label>
                <input name="harga" type="number" required class="w-full border p-2 rounded" placeholder="Contoh: 15000">
            </div>
            <div>
                <label class="block font-semibold mb-1">Gambar</label>
                <input type="file" name="gambar" accept="image/*" required class="w-full border p-2 rounded">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Menu</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $menu->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td>Rp<?= number_format($row['harga']) ?></td>
                    <td>
                        <?php if ($row['gambar']): ?>
                            <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="h-16 mx-auto rounded shadow">
                        <?php else: ?>
                            <span class="text-gray-400 italic">Tidak ada gambar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button onclick="showConfirmModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')" 
                                class="bg-red-600 text-white px-4 py-2 rounded-xl shadow hover:bg-red-700 hover:scale-105 transform transition duration-300">
                            🗑 Hapus
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Konfirmasi -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Hapus</h2>
        <p id="confirmMessage" class="text-gray-600 mb-6">Yakin ingin menghapus menu ini?</p>
        <div class="flex justify-center gap-4">
            <a id="confirmYes" href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Ya, Hapus</a>
            <button onclick="closeConfirmModal()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">Batal</button>
        </div>
    </div>
</div>

<script>
    function showConfirmModal(id, nama) {
        const modal = document.getElementById('confirmModal');
        const confirmLink = document.getElementById('confirmYes');
        const message = document.getElementById('confirmMessage');

        confirmLink.href = '?delete=' + id;
        message.textContent = `Yakin ingin menghapus menu "${nama}"?`;
        modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }
</script>

</body>
</html>
