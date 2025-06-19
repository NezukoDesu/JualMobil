<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$activeLinkClass = "text-blue-600 font-semibold border-b-2 border-blue-600";
$normalLinkClass = "text-gray-600 hover:text-blue-500 hover:border-b-2 hover:border-blue-500 transition-all duration-200";
?>

<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center px-6 py-3">
            <a href="/JualMobil/Index.php" class="flex items-center space-x-2 w-48">
                <i class="fas fa-car text-2xl text-blue-600"></i>
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    JualMobil
                </span>
            </a>

            <div class="flex-1 flex justify-center items-center">
                <div class="flex items-center space-x-6">
                    <a href="/JualMobil/Index.php" 
                       class="py-2 px-3 <?= $currentPage === 'Index.php' ? $activeLinkClass : $normalLinkClass ?>">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>

                    <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                        <a href="/JualMobil/Manager/DataMobil.php" 
                           class="py-2 px-3 <?= $currentPage === 'DataMobil.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-database mr-2"></i>Data Mobil
                        </a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'Super Admin' || $_SESSION['role'] === 'Manager'): ?>
                        <a href="/JualMobil/SuperAdmin/DataAdmin.php" 
                           class="py-2 px-3 <?= $currentPage === 'DataAdmin.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-users mr-2"></i>Data Admin
                        </a>
                        <a href="/JualMobil/SuperAdmin/Laporan.php" 
                           class="py-2 px-3 <?= $currentPage === 'Laporan.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-chart-bar mr-2"></i>Laporan
                        </a>
                        <a href="/JualMobil/SuperAdmin/Pesanan.php" 
                           class="py-2 px-3 <?= $currentPage === 'Pesanan.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-chart-bar mr-2"></i>Pesanan
                        </a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'Manager'): ?>
                        <a href="/JualMobil/Manager/Chat.php" 
                           class="py-2 px-3 <?= $currentPage === 'Chat.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-comments mr-2"></i>Contact Sales
                        </a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'Sales'): ?>
                        <a href="/JualMobil/Sales/Chat.php" 
                           class="py-2 px-3 <?= $currentPage === 'Chat.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-comments mr-2"></i>Chat Customer
                        </a>
              
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'Customer'): ?>
                        <a href="/JualMobil/Cust/Request.php" 
                           class="py-2 px-3 <?= $currentPage === 'Request.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-shopping-cart mr-2"></i>Pesanan
                        </a>
                        <a href="/JualMobil/Cust/Chat.php" 
                           class="py-2 px-3 <?= $currentPage === 'Chat.php' ? $activeLinkClass : $normalLinkClass ?>">
                            <i class="fas fa-comments mr-2"></i>Contact Sales
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="w-48 flex justify-end">
                <div class="relative group">
                    <button class="flex items-center space-x-3 p-2 rounded-lg group-hover:bg-gray-100 transition-all duration-200">
                        <?php
                        $profilePhoto = !empty($_SESSION['foto']) && file_exists('Uploads/' . $_SESSION['foto'])
                            ? 'Uploads/' . htmlspecialchars($_SESSION['foto'])
                            : 'Uploads/Foto/Default.png';
                        ?>
                        <img src="/JualMobil/<?= $profilePhoto ?>" 
                             alt="Profile" 
                             class="w-9 h-9 rounded-full object-cover border-2 border-blue-200 group-hover:border-blue-400 transition-all duration-200">
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-700 group-hover:text-blue-600">
                                <?= htmlspecialchars($_SESSION['username']) ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= htmlspecialchars($_SESSION['role']) ?>
                            </p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 ml-2"></i>
                    </button>

                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200">
                        <a href="/JualMobil/Profile.php" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <hr class="my-1">
                        <a href="/JualMobil/Logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
