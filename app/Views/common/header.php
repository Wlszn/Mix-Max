<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$cartCount = count($_SESSION['cart'] ?? []);
$user      = $_SESSION['user'] ?? null;
$basePath  = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME : '';

$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
?>

<header class="sticky top-0 z-50 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo + Nav -->
            <div class="flex items-center gap-8">
                <a href="<?= $basePath ?>/" class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-2xl font-bold text-gray-900">Mix Max</span>
                </a>

                <nav class="hidden md:flex items-center gap-6">
                    <a href="<?= $basePath ?>/events" class="text-gray-700 hover:text-blue-600 transition-colors">Events</a>
                    <a href="<?= $basePath ?>/events?category=concert" class="text-gray-700 hover:text-blue-600 transition-colors">Concerts</a>
                    <a href="<?= $basePath ?>/events?category=sports" class="text-gray-700 hover:text-blue-600 transition-colors">Sports</a>
                    <a href="<?= $basePath ?>/events?category=theater" class="text-gray-700 hover:text-blue-600 transition-colors">Theater</a>
                    <a href="<?= $basePath ?>/events?category=comedy" class="text-gray-700 hover:text-blue-600 transition-colors">Comedy</a>
                </nav>
            </div>

            <!-- Search -->
            <form action="<?= $basePath ?>/events" method="get" class="hidden lg:block flex-1 max-w-lg mx-8">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                        <path d="m21 21-4.35-4.35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search events, artists, venues…"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </form>

            <!-- Right side: cart + auth -->
            <div class="flex items-center gap-4">

                <!-- Cart -->
                <a href="<?= $basePath ?>/cart" class="relative">
                    <svg class="w-6 h-6 text-gray-700 hover:text-blue-600 transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="21" r="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                        <circle cx="20" cy="21" r="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <?= (int)$cartCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Auth links -->
                <?php if ($user): ?>
                    <!-- Logged-in user menu -->
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                            <span class="hidden md:inline"><?= htmlspecialchars($user['username']) ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Signed in as</p>
                                <p class="text-sm font-semibold text-gray-900 truncate"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                                <a href="<?= $basePath ?>/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Admin Dashboard
                                </a>
                            <?php endif; ?>
                            <a href="<?= $basePath ?>/logout"
                               class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Sign Out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= $basePath ?>/login"
                       class="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                        Sign In
                    </a>
                    <a href="<?= $basePath ?>/register"
                       class="text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Register
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>

<?php if (!empty($flashSuccess)): ?>
    <div id="flash-banner" class="bg-green-50 border-b border-green-200 text-green-800 text-sm text-center py-2 px-4">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
    <script>
        setTimeout(() => {
            const el = document.getElementById('flash-banner');
            if (el) el.style.display = 'none';
        }, 4000);
    </script>
<?php endif; ?>