<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$events = $events ?? [];
$search = $search ?? '';

function getEventCategory(array $event): string
{
    $text = strtolower(
        ($event['title'] ?? '') . ' ' .
        ($event['artist'] ?? '') . ' ' .
        ($event['description'] ?? '')
    );

    if (str_contains($text, 'sport') || str_contains($text, 'nba') || str_contains($text, 'lakers') || str_contains($text, 'warriors') || str_contains($text, 'soccer') || str_contains($text, 'hockey')) {
        return 'sports';
    }

    if (str_contains($text, 'theater') || str_contains($text, 'theatre') || str_contains($text, 'hamilton') || str_contains($text, 'play')) {
        return 'theater';
    }

    if (str_contains($text, 'comedy') || str_contains($text, 'comedian') || str_contains($text, 'chappelle')) {
        return 'comedy';
    }

    return 'concert';
}

function categoryClass(string $category): string
{
    return match ($category) {
        'sports' => 'bg-green-100 text-green-700',
        'theater' => 'bg-red-100 text-red-700',
        'comedy' => 'bg-yellow-100 text-yellow-700',
        default => 'bg-purple-100 text-purple-700',
    };
}

function formatEventDate(string $date): string
{
    return date('M j, Y', strtotime($date));
}

function formatEventTime(string $time): string
{
    return date('g:i A', strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Events') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-4xl font-bold text-gray-900 mb-3">Discover Events</h1>
    <p class="text-gray-600 mb-10"><?= count($events) ?> events available</p>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-xl">⌯</span>
            <h2 class="text-xl font-bold">Filters</h2>
        </div>

        <form method="get" action="<?= $basePath ?>/events" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Search events..."
                class="w-full bg-gray-100 rounded-lg px-4 py-3 focus:outline-none"
            >

            <select name="category" class="w-full bg-gray-100 rounded-lg px-4 py-3 focus:outline-none">
                <option>All Categories</option>
                <option>Concerts</option>
                <option>Sports</option>
                <option>Theater</option>
                <option>Comedy</option>
            </select>

            <select name="date" class="w-full bg-gray-100 rounded-lg px-4 py-3 focus:outline-none">
                <option>Date</option>
                <option>Today</option>
                <option>This Week</option>
                <option>This Month</option>
            </select>
        </form>
    </div>

    <?php if (empty($events)): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
            <p class="text-gray-600">No events found.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($events as $event): ?>
                <?php
                $category = getEventCategory($event);
                $price = $event['startingPrice'] ?? 0;
                ?>

                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[520px]">
                    <?php if (!empty($event['imageUrl'])): ?>
                        <img
                            src="<?= htmlspecialchars($event['imageUrl']) ?>"
                            alt="<?= htmlspecialchars($event['title']) ?>"
                            class="w-full h-52 object-cover"
                        >
                    <?php else: ?>
                        <div class="w-full h-52 bg-gray-200 flex items-center justify-center text-gray-500">
                            No image
                        </div>
                    <?php endif; ?>

                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h2 class="text-xl font-bold text-gray-900 line-clamp-2">
                                <?= htmlspecialchars($event['title']) ?>
                            </h2>

                            <span class="shrink-0 text-xs font-semibold px-3 py-1 rounded-lg <?= categoryClass($category) ?>">
                                <?= htmlspecialchars($category) ?>
                            </span>
                        </div>

                        <p class="text-gray-700 mb-2">
                            📅 <?= htmlspecialchars(formatEventDate($event['date'])) ?>
                            ·
                            <?= htmlspecialchars(formatEventTime($event['startTime'])) ?>
                        </p>

                        <p class="text-gray-700 mb-6 line-clamp-2">
                            📍 <?= htmlspecialchars($event['venueName'] ?? 'Venue') ?>
                            <?php if (!empty($event['city'])): ?>
                                , <?= htmlspecialchars($event['city']) ?>
                            <?php endif; ?>
                        </p>

                        <div class="flex items-end justify-between mt-auto">
                            <div>
                                <p class="text-sm text-gray-500">From</p>
                                <p class="text-2xl font-bold text-black">
                                    $<?= htmlspecialchars(number_format((float)$price, 0)) ?>
                                </p>
                            </div>

                            <a
                                href="<?= $basePath ?>/events/<?= (int)$event['eventId'] ?>"
                                class="bg-black text-white px-5 py-3 rounded-lg font-semibold hover:bg-gray-800"
                            >
                                Get Tickets
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>