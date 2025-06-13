<?php
session_start();
include 'config/db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'];

    if (strlen($username) < 4 || strlen($password) < 6) {
        $error = "Username minimal 4 karakter dan password minimal 6 karakter.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan login.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
        $check->close(); 
    }
}
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MakanOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right bottom, #fde68a, #fca5a5, #ef4444);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md transform transition-all duration-300 hover:scale-105">
        <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-6">Daftar Akun Baru</h2>
        <?php if ($error): ?>
            <p class="text-red-600 bg-red-100 p-3 rounded-md mb-4 text-center text-sm">
                <?= $error ?>
            </p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="text-green-600 bg-green-100 p-3 rounded-md mb-4 text-center text-sm">
                <?= $success_message ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" id="username" placeholder="Buat username Anda"
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition duration-200"
                       required minlength="4">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" id="password" placeholder="Buat password Anda"
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition duration-200"
                       required minlength="6">
            </div>
            <div class="mb-6">
                <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Daftar sebagai</label>
                <select name="role" id="role"
                        class="w-full p-3 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition duration-200">
                    <option value="pembeli">Pembeli</option>
                    <option value="penjual">Penjual</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-red-600 text-white p-3 rounded-md font-semibold text-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-300 ease-in-out">Daftar</button>
        </form>
        <p class="mt-6 text-center text-gray-600 text-sm">Sudah punya akun?
            <a href="login.php" class="text-blue-600 hover:text-blue-800 font-semibold transition duration-200">Login di sini</a>
        </p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');

            if (usernameInput.value.length < 4) {
                alert("Username minimal 4 karakter.");
                e.preventDefault();
            } else if (passwordInput.value.length < 6) {
                alert("Password minimal 6 karakter.");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>