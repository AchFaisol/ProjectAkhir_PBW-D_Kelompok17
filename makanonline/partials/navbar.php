<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="bg-white shadow p-4 relative z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-xl font-bold text-yellow-500">MakanOnline</a>

        <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="hidden md:flex space-x-6 text-sm">
            <a href="menu.php" class="text-gray-700 hover:text-yellow-500">Menu</a>
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] == 'penjual'): ?>
                    <a href="cart.php" class="text-gray-700 hover:text-yellow-500">Keranjang</a>
                    <a href="seller_dashboard.php" class="text-gray-700 hover:text-yellow-500">Dashboard Penjual</a>
                    <a href="seller_orders.php" class="text-gray-700 hover:text-yellow-500">Pesanan Masuk</a>
                    <a href="orders.php" class="text-gray-700 hover:text-yellow-500">Pesanan Saya</a>
                <?php else: ?>
                    <a href="cart.php" class="text-gray-700 hover:text-yellow-500">Keranjang</a>
                    <a href="orders.php" class="text-gray-700 hover:text-yellow-500">Pesanan Saya</a>
                <?php endif; ?>
                <a href="logout.php" class="text-red-500 hover:text-red-600">Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-gray-700 hover:text-yellow-500">Login</a>
                <a href="register.php" class="text-gray-700 hover:text-yellow-500">Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="menu" class="hidden absolute top-16 right-4 bg-white shadow-xl rounded-xl px-6 py-4 w-64 space-y-3 md:hidden">
        <a href="menu.php" class="block font-bold text-yellow-500 hover:text-yellow-600">Menu</a>
        <?php if (isset($_SESSION['user'])): ?>
            <?php if ($_SESSION['user']['role'] == 'penjual'): ?>
                <a href="cart.php" class="block text-gray-800 hover:text-yellow-500">Keranjang</a>
                <a href="seller_dashboard.php" class="block text-gray-800 hover:text-yellow-500">Dashboard Penjual</a>
                <a href="seller_orders.php" class="block text-gray-800 hover:text-yellow-500">Pesanan Masuk</a>
                <a href="orders.php" class="block text-gray-800 hover:text-yellow-500">Pesanan Saya</a>
            <?php else: ?>
                <a href="cart.php" class="block text-gray-800 hover:text-yellow-500">Keranjang</a>
                <a href="orders.php" class="block text-gray-800 hover:text-yellow-500">Pesanan Saya</a>
            <?php endif; ?>
            <a href="logout.php" class="block text-red-500 hover:text-red-600">Logout</a>
        <?php else: ?>
            <a href="login.php" class="block text-gray-800 hover:text-yellow-500">Login</a>
            <a href="register.php" class="block text-gray-800 hover:text-yellow-500">Daftar</a>
        <?php endif; ?>
    </div>

    <script>
        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');
        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</nav>
