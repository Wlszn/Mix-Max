<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = count($_SESSION['cart'] ?? []);
$user = $_SESSION['user'] ?? null;

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
?>

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-6">

            <!-- Left: Logo -->
            <a href="<?= $basePath ?>/" class="flex items-center gap-2 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-slate-950 text-white flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-slate-950">Mix Max</span>
            </a>

            <!-- Middle: Search -->
            <form action="<?= $basePath ?>/events" method="get" class="hidden md:block flex-1 max-w-xl">
                <div class="relative" id="live-search-wrapper">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
                        <path d="m21 21-4.35-4.35" stroke-width="2" stroke-linecap="round"></path>
                    </svg>

                    <input type="text" name="search" id="live-search-input" autocomplete="off"
                        placeholder="Search events..."
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">

                    <button type="button" id="clear-live-search"
                        class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                        ×
                    </button>

                    <div id="live-search-results"
                        class="hidden absolute left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-50">
                    </div>
                </div>
            </form>

            <!-- Right: Simple nav -->
            <nav class="flex items-center gap-5 text-sm">
                <a href="<?= $basePath ?>/events" class="text-slate-700 hover:text-blue-600 transition-colors">
                    Browse
                </a>

                <?php if ($user): ?>
                    <a href="<?= $basePath ?>/events/create" class="text-slate-700 hover:text-blue-600 transition-colors">
                        Host
                    </a>
                <?php endif; ?>

                <a href="<?= $basePath ?>/cart" class="relative text-slate-700 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="21" r="1" stroke-width="2"></circle>
                        <circle cx="20" cy="21" r="1" stroke-width="2"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>

                    <?php if ($cartCount > 0): ?>
                        <span
                            class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <?= (int) $cartCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if ($user): ?>
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-slate-700 hover:text-blue-600">
                            <div
                                class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                            <span class="hidden lg:inline"><?= htmlspecialchars($user['username']) ?></span>
                        </button>

                        <div
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs text-slate-500">Signed in as</p>
                                <p class="text-sm font-semibold text-slate-900 truncate">
                                    <?= htmlspecialchars($user['email']) ?>
                                </p>
                            </div>
 
                            <a href="<?= $basePath ?>/profile"
                                class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                My Profile
                            </a>
 
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                                <a href="<?= $basePath ?>/admin"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    Admin Dashboard
                                </a>
                            <?php endif; ?>
 
                            <a href="<?= $basePath ?>/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Sign Out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= $basePath ?>/login" class="text-slate-700 hover:text-blue-600 transition-colors">
                        Log In
                    </a>

                    <a href="<?= $basePath ?>/register"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                        Sign Up
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<?php if (!empty($flashSuccess)): ?>
    <div class="max-w-7xl mx-auto px-6 mt-4">
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl">
            <?= htmlspecialchars($flashSuccess) ?>
        </div>
    </div>
<?php endif; ?>

<body class="bg-slate-50 min-h-screen flex flex-col">
    <div id="page-loader"
        class="fixed inset-0 z-[9999] bg-slate-950 flex items-center justify-center transition-opacity duration-300">
        <div class="text-center">
            <div class="mx-auto mb-4 h-12 w-12 rounded-full border-4 border-blue-500 border-t-transparent animate-spin">
            </div>
            <p class="text-white font-semibold">Loading Mix Max Experience</p>
        </div>
    </div>
</body>