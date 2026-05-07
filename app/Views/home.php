<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$featuredEvents = $featuredEvents ?? [];
$categories = [
    ['name' => 'Concerts', 'icon' => '♪', 'link' => $basePath . '/events?category=concert'],
    ['name' => 'Sports', 'icon' => '◉', 'link' => $basePath . '/events?category=sports'],
    ['name' => 'Theater', 'icon' => '▣', 'link' => $basePath . '/events?category=theater'],
    ['name' => 'Comedy', 'icon' => '☺', 'link' => $basePath . '/events?category=comedy']
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mix Max - Discover Amazing Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php require __DIR__ . '/common/header.php'; ?>

<div class="min-h-screen bg-gray-50">
    <section class="relative overflow-hidden bg-[#0b1020] text-white">
    <!-- Background glow -->
    <div class="absolute inset-0">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-600 rounded-full blur-3xl opacity-25"></div>
        <div class="absolute top-16 right-10 w-[520px] h-[520px] bg-purple-700 rounded-full blur-3xl opacity-25"></div>
        <div class="absolute bottom-0 left-1/3 w-80 h-80 bg-pink-500 rounded-full blur-3xl opacity-10"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- Left content -->
            <div>
                <p class="text-blue-300 font-semibold mb-4">
                    Live events. Local shows. Real tickets.
                </p>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6">
                    Discover Events.
                    <span class="block">Create Moments.</span>
                </h1>

                <p class="text-lg md:text-xl text-slate-300 mb-8 max-w-xl leading-relaxed">
                    Browse concerts, sports, theater, comedy nights, and community events.
                    Buy tickets or host your own event with Mix Max.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-10">
                    <a href="<?= $basePath ?>/events"
                       class="inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white px-7 py-3 rounded-xl font-semibold transition-colors shadow-lg shadow-blue-900/30">
                        Browse Events →
                    </a>

                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="<?= $basePath ?>/events/create"
                           class="inline-flex justify-center items-center border border-white/40 text-white hover:bg-white hover:text-slate-950 px-7 py-3 rounded-xl font-semibold transition-colors">
                            Host an Event
                        </a>
                    <?php else: ?>
                        <a href="<?= $basePath ?>/register"
                           class="inline-flex justify-center items-center border border-white/40 text-white hover:bg-white hover:text-slate-950 px-7 py-3 rounded-xl font-semibold transition-colors">
                            Start Hosting
                        </a>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-3 gap-6 max-w-md">
                    <div>
                        <p class="text-3xl font-bold text-blue-400">500+</p>
                        <p class="text-sm text-slate-400">Events Listed</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-purple-400">10K+</p>
                        <p class="text-sm text-slate-400">Tickets Booked</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-pink-400">24/7</p>
                        <p class="text-sm text-slate-400">Online Access</p>
                    </div>
                </div>
            </div>

            <!-- Right visual -->
            <div class="relative hidden lg:block">
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-500 to-purple-600 rounded-[3rem] blur-2xl opacity-30"></div>

                <div class="relative bg-white/10 border border-white/20 backdrop-blur-xl rounded-[3rem] p-8 shadow-2xl">
                    <div class="relative aspect-square rounded-[2.5rem] bg-gradient-to-br from-slate-950 via-blue-950 to-purple-950 overflow-hidden flex items-center justify-center">

                        <div class="absolute w-80 h-80 bg-blue-500/20 rounded-full"></div>
                        <div class="absolute w-56 h-56 bg-purple-500/20 rounded-full right-8 top-10"></div>

                        <!-- Large icon -->
                        <svg class="relative w-72 h-72 text-white drop-shadow-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"
                                  d="M4 13v4a3 3 0 0 0 3 3h1v-8H7a3 3 0 0 0-3 3zm16 0v4a3 3 0 0 1-3 3h-1v-8h1a3 3 0 0 1 3 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"
                                  d="M4 13a8 8 0 0 1 16 0" />
                        </svg>

                        <div class="absolute left-6 bottom-6 bg-white text-slate-950 rounded-2xl p-4 shadow-xl w-56">
                            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Featured Event</p>
                            <h3 class="font-bold text-lg mt-1">
                                <?= htmlspecialchars($featuredEvents[0]['title'] ?? 'Live Concert Night') ?>
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">
                                <?= htmlspecialchars($featuredEvents[0]['venueName'] ?? 'Mix Max Arena') ?>
                            </p>
                        </div>

                        <div class="absolute right-6 top-6 bg-blue-600 text-white rounded-2xl p-4 shadow-xl">
                            <p class="text-xs text-blue-100">Tickets from</p>
                            <p class="text-2xl font-bold">$45</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

    <section class="bg-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-slate-950">Browse Categories</h2>
            <a href="<?= $basePath ?>/events" class="text-blue-600 hover:text-blue-700 font-semibold">
                View All →
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($categories as $category): ?>
                <a href="<?= htmlspecialchars($category['link']) ?>"
                   class="group border border-slate-200 bg-white hover:bg-slate-950 hover:border-slate-950 rounded-2xl p-8 text-center transition-all shadow-sm hover:shadow-xl">
                    <div class="text-4xl text-blue-600 group-hover:text-white mb-3">
                        <?= htmlspecialchars($category['icon']) ?>
                    </div>
                    <p class="font-semibold text-slate-900 group-hover:text-white">
                        <?= htmlspecialchars($category['name']) ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

    <section class="bg-slate-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl text-slate-950 font-bold">Featured Events</h2>
            <a href="<?= $basePath ?>/events" class="text-blue-600 hover:text-blue-700 font-semibold">
                See All →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php if (empty($featuredEvents)): ?>
        <p class="text-slate-600">No featured events available.</p>
    <?php else: ?>
        <?php foreach ($featuredEvents as $event): ?>
            <a
                href="<?= $basePath ?>/events/<?= (int)$event['eventId'] ?>"
                class="group bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-xl transition-shadow"
            >
                <div class="relative">
                    <?php if (!empty($event['imageUrl'])): ?>
                        <img
                            src="<?= htmlspecialchars($event['imageUrl']) ?>"
                            alt="<?= htmlspecialchars($event['title']) ?>"
                            class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    <?php else: ?>
                        <div class="w-full h-44 bg-slate-200 flex items-center justify-center text-slate-500">
                            No Image
                        </div>
                    <?php endif; ?>

                    <span class="absolute top-3 left-3 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                        FEATURED
                    </span>

                    <span class="absolute top-3 right-3 bg-white/90 text-slate-700 w-9 h-9 rounded-full flex items-center justify-center">
                        ♡
                    </span>
                </div>

                <div class="p-4">
                    <h3 class="font-bold text-base leading-snug mb-2 group-hover:text-blue-600">
                        <?= htmlspecialchars($event['title']) ?>
                    </h3>

                    <p class="text-sm text-slate-600 mb-3">
                        <?= htmlspecialchars($event['artist']) ?>
                    </p>

                    <div class="space-y-1 text-sm text-slate-500">
                        <p>
                            📅 <?= htmlspecialchars($event['date']) ?>
                            <?php if (!empty($event['startTime'])): ?>
                                · <?= htmlspecialchars(substr($event['startTime'], 0, 5)) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($event['venueName'])): ?>
                            <p>
                                📍 <?= htmlspecialchars($event['venueName']) ?>
                                <?php if (!empty($event['city'])): ?>
                                    , <?= htmlspecialchars($event['city']) ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">From</p>
                            <p class="text-xl font-bold text-blue-600">$45</p>
                        </div>

                        <p class="text-xs text-slate-500">
                            View details →
                        </p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

    </div>
</section>
</div>
    <?php require __DIR__ . '/common/footer.php'; ?>
</body>
</html>