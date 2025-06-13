<?php
session_start();
require 'config/db.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - MakanOnline</title>
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
        .hero-section {
            background-image: url('https://source.unsplash.com/random/1200x600/?food,restaurant'); 
            background-size: cover;
            background-position: center;
            position: relative;
            z-index: 0;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
    </style>
</head>
<body class="text-white">

    <?php include 'partials/navbar.php'; ?>

    <header class="hero-section text-white py-20 md:py-32 flex-grow flex items-center justify-center">
        <div class="container mx-auto px-4 text-center max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mb-4 drop-shadow-lg">
                Selamat Datang di <span class="text-yellow-300">MakanOnline</span>
            </h1>
            <p class="text-lg md:text-2xl mb-8 opacity-90 drop-shadow-sm">
                Jelajahi berbagai hidangan lezat dan pesan favoritmu dengan mudah!
            </p>
            <div class="mt-6 flex justify-center">
                <?php if (!isset($_SESSION['user'])): ?>
                    <button onclick="showCustomAlert()" class="bg-white text-red-600 hover:bg-gray-100 px-6 py-3 rounded-full shadow-lg font-bold text-base md:text-lg transition duration-300 transform hover:scale-105">
                        Login untuk Memulai
                    </button>
                <?php else: ?>
                    <a href="menu.php" class="inline-block bg-white text-red-600 hover:bg-gray-100 px-6 py-3 rounded-full shadow-lg font-bold text-base md:text-lg transition duration-300 transform hover:scale-105">
                        Lihat Menu Sekarang!
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Modal Alert Custom -->
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
