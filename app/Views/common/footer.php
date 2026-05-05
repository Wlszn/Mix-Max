<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';
?>

<footer class="bg-[#050816] text-slate-300 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Brand -->
            <div class="md:col-span-1">
                <a href="<?= $basePath ?>/" class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-white/10 text-white flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">Mix Max</span>
                </a>

                <p class="text-sm leading-6 text-slate-400">
                    Discover concerts, sports, theater, comedy, and local events.
                    Buy tickets or host your own event in one place.
                </p>
            </div>

            <!-- Marketplace -->
            <div>
                <h3 class="text-white font-semibold mb-4">Marketplace</h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="<?= $basePath ?>/events" class="hover:text-blue-400 transition-colors">
                            Browse Events
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>/events/create" class="hover:text-blue-400 transition-colors">
                            Host an Event
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>/events?category=concert" class="hover:text-blue-400 transition-colors">
                            Categories
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-white font-semibold mb-4">Support</h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="#" class="hover:text-blue-400 transition-colors">
                            Help Center
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-blue-400 transition-colors">
                            Trust & Safety
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-blue-400 transition-colors">
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-white font-semibold mb-4">Legal</h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="#" class="hover:text-blue-400 transition-colors">
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-blue-400 transition-colors">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-blue-400 transition-colors">
                            Cookie Policy
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-400">
                © 2026 Mix Max. All rights reserved.
            </p>

            <p class="text-sm text-slate-400 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 11c0-1.105.895-2 2-2s2 .895 2 2-.895 2-2 2-2-.895-2-2zm0 0V7m0 4v6m-7-6a7 7 0 1 1 14 0c0 5-7 10-7 10S5 16 5 11z" />
                </svg>
                Vanier College · E-Commerce Winter 2026
            </p>
        </div>
    </div>
</footer>