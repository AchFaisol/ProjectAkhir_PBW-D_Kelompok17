<?php
session_start();
include 'config/db.php';

$err = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (strlen($username) < 4 || strlen($password) < 6) {
        $err = "Username minimal 4 karakter dan password minimal 6 karakter.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                if ($user['role'] == 'penjual') {
                    header("Location: seller_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $err = "Password salah.";
            }
        } else {
            $err = "Username tidak ditemukan. Silakan daftar terlebih dahulu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MakanOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right bottom, #fde68a, #fca5a5, #ef4444);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg space-y-6 transition-all duration-300 transform hover:scale-[1.02]">
        <h2 class="text-3xl font-extrabold text-center text-gray-800">Masuk ke Akun Anda</h2>

        <?php if ($err): ?>
            <p class="text-red-600 bg-red-100 p-3 rounded-md text-center text-sm">
                <?= $err ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-gray-700 text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" id="username" required minlength="4" placeholder="Masukkan username Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400 transition duration-200">
            </div>
            <div>
                <label for="password" class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" id="password" required minlength="6" placeholder="Masukkan password Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400 transition duration-200">
            </div>
            <button type="submit"
                class="w-full bg-red-600 text-white py-2.5 rounded-md font-semibold hover:bg-red-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                Login
            </button>
        </form>

        <p class="text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="register.php" class="text-blue-600 font-medium hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username');
            const password = document.getElementById('password');

            if (username.value.length < 4) {
                alert("Username minimal 4 karakter.");
                e.preventDefault();
            } else if (password.value.length < 6) {
                alert("Password minimal 6 karakter.");
                e.preventDefault();
            }
        });
    </script>

</body>
</html>
