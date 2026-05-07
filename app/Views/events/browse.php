<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$events = $events ?? [];
$search = $search ?? '';
$selectedCategory = $selectedCategory ?? '';
$date = $date ?? '';
$sort = $sort ?? 'ending_soon';

$filtersOpen = $search !== '' || $selectedCategory !== '' || $date !== '';

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Events - Mix Max</title>
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
                <button id="filterToggle"
                    class="border border-slate-400 bg-white px-3 py-2 rounded-md text-sm hover:bg-slate-100 flex items-center gap-1">
                    ⚙ Filters
                </button>

                <form method="get" action="<?= $basePath ?>/events">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

                    <select name="sort" onchange="this.form.submit()"
                        class="border border-slate-300 bg-white px-3 py-2 rounded-md text-sm">
                        <option value="ending_soon" <?= selectedSort($sort, 'ending_soon') ?>>Ending Soon</option>
                        <option value="date_soonest" <?= selectedSort($sort, 'date_soonest') ?>>Date: Soonest</option>
                        <option value="price_low" <?= selectedSort($sort, 'price_low') ?>>Price: Low to High</option>
                        <option value="price_high" <?= selectedSort($sort, 'price_high') ?>>Price: High to Low</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Category Buttons -->
        <?php
        $categories = [
            [
                'name' => '',
                'label' => 'All',
                'active' => $selectedCategory === ''
            ],
            [
                'name' => 'concert',
                'label' => 'Concerts',
                'active' => $selectedCategory === 'concert'
            ],
            [
                'name' => 'sports',
                'label' => 'Sports',
                'active' => $selectedCategory === 'sports'
            ],
            [
                'name' => 'theater',
                'label' => 'Theater',
                'active' => $selectedCategory === 'theater'
            ],
            [
                'name' => 'comedy',
                'label' => 'Comedy',
                'active' => $selectedCategory === 'comedy'
            ],
        ];
        ?>

        <div class="flex flex-wrap gap-2 mb-5">
            <?php foreach ($categories as $categoryItem): ?>
                <?php
                $category = $categoryItem;
                require __DIR__ . '/../common/category-button.php';
                ?>
            <?php endforeach; ?>
        </div>

        <!-- Dropdown Filters -->
        <section id="filterPanel" class="overflow-hidden transition-all duration-300 ease-in-out bg-slate-100 border border-slate-300 rounded-xl mb-6
            <?= $filtersOpen ? 'max-h-96 opacity-100 p-5' : 'max-h-0 opacity-0 p-0 border-transparent' ?>">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">Filters</h2>
                <p class="text-xs text-slate-500">Filter events by search, category, or date</p>
            </div>

            <form method="get" action="<?= $basePath ?>/events" class="grid md:grid-cols-3 gap-4">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

                <div>
                    <label class="block text-sm text-slate-600 mb-1">Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="Search events..."
                        class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm text-slate-600 mb-1">Category</label>
                    <select name="category" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
                        <option value="" <?= selectedCategory($selectedCategory, '') ?>>All Categories</option>
                        <option value="concert" <?= selectedCategory($selectedCategory, 'concert') ?>>Concerts</option>
                        <option value="sports" <?= selectedCategory($selectedCategory, 'sports') ?>>Sports</option>
                        <option value="theater" <?= selectedCategory($selectedCategory, 'theater') ?>>Theater</option>
                        <option value="comedy" <?= selectedCategory($selectedCategory, 'comedy') ?>>Comedy</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-slate-600 mb-1">Date</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"
                        class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
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
                    <?php require __DIR__ . '/../common/event-card.php'; ?>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

    </main>
    <?php require __DIR__ . '/../common/js-scripts.php'; ?>
    <?php require __DIR__ . '/../common/footer.php'; ?>
</body>

</html>