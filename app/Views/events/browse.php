<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$events = $events ?? [];
$search = $search ?? '';
$category = $category ?? '';
$date = $date ?? '';
$sort = $sort ?? 'ending_soon';

function eventCategoryUrl(string $basePath, string $category): string
{
    if ($category === '') {
        return $basePath . '/events';
    }

    return $basePath . '/events?' . http_build_query(['category' => $category]);
}

function selectedCategory(string $category, string $value): string
{
    return $category === $value ? 'selected' : '';
}

function selectedSort(string $sort, string $value): string
{
    return $sort === $value ? 'selected' : '';
}

function categoryButtonClass(string $category, string $value): string
{
    if ($category === $value) {
        return 'px-3 py-1.5 rounded-full bg-slate-950 text-white text-sm';
    }

    return 'px-3 py-1.5 rounded-full border border-slate-400 bg-white text-sm hover:bg-slate-100';
}

$filtersOpen = $search !== '' || $category !== '' || $date !== '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Events - Mix Max</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-slate-950">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-6xl mx-auto px-4 py-6">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="text-3xl font-bold">Browse Events</h1>
            <p class="text-sm text-slate-500"><?= count($events) ?> events found</p>
        </div>

        <div class="flex gap-2">
            <button
                id="filterToggle"
                class="border border-slate-400 bg-white px-3 py-2 rounded-md text-sm hover:bg-slate-100 flex items-center gap-1"
            >
                ⚙ Filters
            </button>

            <form method="get" action="<?= $basePath ?>/events">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

                <select
                    name="sort"
                    onchange="this.form.submit()"
                    class="border border-slate-300 bg-white px-3 py-2 rounded-md text-sm"
                >
                    <option value="ending_soon" <?= selectedSort($sort, 'ending_soon') ?>>Ending Soon</option>
                    <option value="date_soonest" <?= selectedSort($sort, 'date_soonest') ?>>Date: Soonest</option>
                    <option value="price_low" <?= selectedSort($sort, 'price_low') ?>>Price: Low to High</option>
                    <option value="price_high" <?= selectedSort($sort, 'price_high') ?>>Price: High to Low</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Category Buttons -->
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="<?= eventCategoryUrl($basePath, '') ?>"
           class="<?= categoryButtonClass($category, '') ?>">
            All
        </a>

        <a href="<?= eventCategoryUrl($basePath, 'concert') ?>"
           class="<?= categoryButtonClass($category, 'concert') ?>">
            🎵 Concerts
        </a>

        <a href="<?= eventCategoryUrl($basePath, 'sports') ?>"
           class="<?= categoryButtonClass($category, 'sports') ?>">
            🏆 Sports
        </a>

        <a href="<?= eventCategoryUrl($basePath, 'theater') ?>"
           class="<?= categoryButtonClass($category, 'theater') ?>">
            🎭 Theater
        </a>

        <a href="<?= eventCategoryUrl($basePath, 'comedy') ?>"
           class="<?= categoryButtonClass($category, 'comedy') ?>">
            😄 Comedy
        </a>
    </div>

    <!-- Dropdown Filters -->
    <section
        id="filterPanel"
        class="<?= $filtersOpen ? '' : 'hidden' ?> bg-slate-100 border border-slate-300 rounded-xl p-5 mb-6"
    >
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">Filters</h2>
            <p class="text-xs text-slate-500">Filter events by search, category, or date</p>
        </div>

        <form method="get" action="<?= $basePath ?>/events" class="grid md:grid-cols-3 gap-4">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

            <div>
                <label class="block text-sm text-slate-600 mb-1">Search</label>
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search events..."
                    class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-1">Category</label>
                <select name="category" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
                    <option value="" <?= selectedCategory($category, '') ?>>All Categories</option>
                    <option value="concert" <?= selectedCategory($category, 'concert') ?>>Concerts</option>
                    <option value="sports" <?= selectedCategory($category, 'sports') ?>>Sports</option>
                    <option value="theater" <?= selectedCategory($category, 'theater') ?>>Theater</option>
                    <option value="comedy" <?= selectedCategory($category, 'comedy') ?>>Comedy</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-1">Date</label>
                <input
                    type="date"
                    name="date"
                    value="<?= htmlspecialchars($date) ?>"
                    class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm"
                >
            </div>

            <div class="md:col-span-3">
                <button class="bg-slate-950 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-600">
                    Apply Filters
                </button>
            </div>
        </form>
    </section>

    <!-- Events Grid -->
    <?php if (empty($events)): ?>
        <div class="bg-slate-100 border border-slate-300 rounded-xl p-8 text-center text-slate-500">
            No events found.
        </div>
    <?php else: ?>
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php foreach ($events as $event): ?>
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
                                 <?= htmlspecialchars($event['date']) ?>
                                <?php if (!empty($event['startTime'])): ?>
                                    · <?= htmlspecialchars(substr($event['startTime'], 0, 5)) ?>
                                <?php endif; ?>
                            </p>

                            <?php if (!empty($event['venueName'])): ?>
                                <p>
                                     <?= htmlspecialchars($event['venueName']) ?>
                                    <?php if (!empty($event['city'])): ?>
                                        , <?= htmlspecialchars($event['city']) ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500">From</p>
                                <p class="text-xl font-bold text-blue-600">
                                    $<?= htmlspecialchars(number_format((float)($event['startingPrice'] ?? 0), 0)) ?>
                                </p>
                            </div>

                            <p class="text-xs text-slate-500">
                                View details →
                            </p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../common/footer.php'; ?>

<script>
    const filterToggle = document.getElementById('filterToggle');
    const filterPanel = document.getElementById('filterPanel');

    filterToggle.addEventListener('click', () => {
        filterPanel.classList.toggle('hidden');
    });
</script>

</body>
</html>