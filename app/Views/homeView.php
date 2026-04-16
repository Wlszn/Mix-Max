<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$featuredEvents = $featuredEvents ?? [];
$categories = [
    ['name' => 'Concerts', 'color' => 'bg-purple-500', 'link' => $basePath . '/events?category=concert'],
    ['name' => 'Sports',   'color' => 'bg-blue-500',   'link' => $basePath . '/events?category=sports'],
    ['name' => 'Theater',  'color' => 'bg-pink-500',   'link' => $basePath . '/events?category=theater'],
    ['name' => 'Comedy',   'color' => 'bg-orange-500', 'link' => $basePath . '/events?category=comedy']
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
    <section class="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl mb-6 font-bold">
                    Discover Amazing Events
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Book tickets to concerts, sports, theater shows, and more
                </p>
                <a
                    href="<?= $basePath ?>/events"
                    class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors"
                >
                    Browse All Events
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl mb-8 text-gray-900 font-bold">Browse by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($categories as $category): ?>
                <a
                    href="<?= htmlspecialchars($category['link']) ?>"
                    class="<?= htmlspecialchars($category['color']) ?> text-white p-6 rounded-lg hover:opacity-90 transition-opacity flex flex-col items-center justify-center gap-3"
                >
                    <?php
                    $icons = [
                        'Concerts' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                        'Sports'   => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 2v20M2 12h20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                        'Theater'  => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 21h8M12 17v4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                        'Comedy'   => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                    ];
                    echo $icons[$category['name']];
                    ?>
                    <span class="text-lg font-semibold"><?= htmlspecialchars($category['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl text-gray-900 font-bold">Featured Events</h2>
            <a href="<?= $basePath ?>/events" class="text-blue-600 hover:text-blue-700 font-semibold">
                View All →
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($featuredEvents)): ?>
                <p class="text-gray-600">No featured events available.</p>
            <?php else: ?>
                <?php foreach ($featuredEvents as $event): ?>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <?php if (!empty($event['imageUrl'])): ?>
                            <img src="<?= htmlspecialchars($event['imageUrl']) ?>"
                                 alt="<?= htmlspecialchars($event['title']) ?>"
                                 class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                No image
                            </div>
                        <?php endif; ?>

                        <div class="p-5">
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                                <?= htmlspecialchars($event['title']) ?>
                            </h3>

                            <p class="text-gray-600 mb-2">
                                <?= htmlspecialchars($event['artist']) ?>
                            </p>

                            <?php if (!empty($event['date'])): ?>
                                <p class="text-sm text-gray-500 mb-1">
                                    <?= htmlspecialchars($event['date']) ?>
                                    <?php if (!empty($event['startTime'])): ?>
                                        at <?= htmlspecialchars($event['startTime']) ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($event['venueName'])): ?>
                                <p class="text-sm text-gray-500 mb-4">
                                    <?= htmlspecialchars($event['venueName']) ?>
                                    <?php if (!empty($event['city'])): ?>
                                        , <?= htmlspecialchars($event['city']) ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <a href="<?= $basePath ?>/events/<?= (int)$event['eventId'] ?>"
                               class="inline-block text-blue-600 hover:text-blue-700 font-semibold">
                                View Details →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="bg-white py-16 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl text-center mb-12 text-gray-900 font-bold">Why Choose Mix Max?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2 text-gray-900 font-semibold">Secure Booking</h3>
                    <p class="text-gray-600">Safe and secure payment processing for peace of mind</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2 text-gray-900 font-semibold">Instant Confirmation</h3>
                    <p class="text-gray-600">Get your tickets instantly via email</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl mb-2 text-gray-900 font-semibold">Best Selection</h3>
                    <p class="text-gray-600">Access to thousands of events nationwide</p>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html>