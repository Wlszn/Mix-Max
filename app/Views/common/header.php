<?php
$cartCount = $_SESSION['cart_count'] ?? 0;
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';
?>

<header class="sticky top-0 z-50 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="<?= $basePath ?>/"
                   class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-2xl font-bold text-gray-900">Mix Max</span>
                </a>

                <nav class="flex items-center gap-6">
                    <a href="<?= $basePath ?>/events" class="text-gray-700 hover:text-blue-600 transition-colors">
                        Events
                    </a>
                    <a href="<?= $basePath ?>/events?category=concert" class="text-gray-700 hover:text-blue-600 transition-colors">
                        Concerts
                    </a>
                    <a href="<?= $basePath ?>/events?category=sports" class="text-gray-700 hover:text-blue-600 transition-colors">
                        Sports
                    </a>
                    <a href="<?= $basePath ?>/events?category=theater" class="text-gray-700 hover:text-blue-600 transition-colors">
                        Theater
                    </a>
                    <a href="<?= $basePath ?>/events?category=comedy" class="text-gray-700 hover:text-blue-600 transition-colors">
                        Comedy
                    </a>
                </nav>
            </div>

            <form action="<?= $basePath ?>/events" method="get" class="flex-1 max-w-lg mx-8">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                        <path d="m21 21-4.35-4.35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search for events, artists, venues..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </form>

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
        </div>
    </div>
</header>