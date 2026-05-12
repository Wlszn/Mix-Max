<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$stats = $stats ?? [];
$recentEvents = $recentEvents ?? [];
$categoryData = $categoryData ?? [];

$totalEvents = (int) ($stats['totalEvents'] ?? 0);
$pendingEvents = (int) ($stats['pendingEvents'] ?? 0);
$activeEvents = (int) ($stats['activeEvents'] ?? 0);
$completedEvents = (int) ($stats['completedEvents'] ?? 0);

$totalUsers = (int) ($stats['totalUsers'] ?? 0);
$ticketsSold = (int) ($stats['ticketsSold'] ?? 0);
$totalRevenue = (float) ($stats['totalRevenue'] ?? 0);
?>

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="bg-slate-50 min-h-screen">
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Admin Dashboard</h1>
                <p class="text-slate-600 mt-1">Website overview and event management.</p>
            </div>

            <a href="<?= $basePath ?>/admin/events"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-semibold">
                Review Pending Events
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-8 space-y-8">

        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-6 text-white shadow">
                <p class="text-green-100 text-sm">Total Revenue</p>
                <p class="text-4xl font-bold mt-2">$<?= number_format($totalRevenue, 2) ?></p>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow">
                <p class="text-blue-100 text-sm">Total Users</p>
                <p class="text-4xl font-bold mt-2"><?= number_format($totalUsers) ?></p>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl p-6 text-white shadow">
                <p class="text-orange-100 text-sm">Tickets Sold</p>
                <p class="text-4xl font-bold mt-2"><?= number_format($ticketsSold) ?></p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 text-white shadow">
                <p class="text-purple-100 text-sm">Total Events</p>
                <p class="text-4xl font-bold mt-2"><?= number_format($totalEvents) ?></p>
            </div>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 text-white shadow">
                <p class="text-purple-100 text-sm">Total Events</p>
                <p class="text-4xl font-bold mt-2"><?= $totalEvents ?></p>
            </div>

            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 text-white shadow">
                <p class="text-yellow-50 text-sm">Pending Approval</p>
                <p class="text-4xl font-bold mt-2"><?= $pendingEvents ?></p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-6 text-white shadow">
                <p class="text-green-100 text-sm">Active Events</p>
                <p class="text-4xl font-bold mt-2"><?= $activeEvents ?></p>
            </div>

            <div class="bg-gradient-to-br from-slate-500 to-slate-700 rounded-2xl p-6 text-white shadow">
                <p class="text-slate-100 text-sm">Completed Events</p>
                <p class="text-4xl font-bold mt-2"><?= $completedEvents ?></p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold mb-5">Events by Category</h2>

                <div class="space-y-4">
                    <?php foreach ($categoryData as $category): ?>
                        <?php
                        $name = $category['category'] ?? 'Uncategorized';
                        $count = (int) ($category['total'] ?? 0);
                        $percent = $totalEvents > 0 ? round(($count / $totalEvents) * 100) : 0;
                        ?>

                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="capitalize text-slate-700"><?= htmlspecialchars($name) ?></span>
                                <span class="font-semibold"><?= $count ?> events · <?= $percent ?>%</span>
                            </div>

                            <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full" style="width: <?= $percent ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold mb-5">Recent Events</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500">
                                <th class="text-left py-3">Event</th>
                                <th class="text-left py-3">Status</th>
                                <th class="text-right py-3">Tickets</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($recentEvents as $event): ?>
                                <tr class="border-b border-slate-100">
                                    <td class="py-3">
                                        <p class="font-semibold text-slate-950">
                                            <?= htmlspecialchars($event['title']) ?>
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            <?= htmlspecialchars($event['date']) ?>
                                        </p>
                                    </td>

                                    <td class="py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?= $event['status'] === 'scheduled' ? 'bg-green-100 text-green-700' : '' ?>
                                            <?= $event['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                                            <?= $event['status'] === 'rejected' ? 'bg-red-100 text-red-700' : '' ?>
                                            <?= $event['status'] === 'completed' ? 'bg-slate-100 text-slate-700' : '' ?>">
                                            <?= htmlspecialchars($event['status']) ?>
                                        </span>
                                    </td>

                                    <td class="py-3 text-right font-semibold">
                                        <?= (int) $event['tickets'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white shadow">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>

            <div class="grid md:grid-cols-3 gap-4">
                <a href="<?= $basePath ?>/admin/events" class="bg-white/20 hover:bg-white/30 rounded-xl p-4">
                    <p class="font-bold">Review Pending Events</p>
                    <p class="text-sm text-blue-100"><?= $pendingEvents ?> awaiting approval</p>
                </a>

                <a href="<?= $basePath ?>/events/create" class="bg-white/20 hover:bg-white/30 rounded-xl p-4">
                    <p class="font-bold">Create Event</p>
                    <p class="text-sm text-blue-100">Add a new event</p>
                </a>

                <a href="<?= $basePath ?>/events" class="bg-white/20 hover:bg-white/30 rounded-xl p-4">
                    <p class="font-bold">Browse Site</p>
                    <p class="text-sm text-blue-100">View public events</p>
                </a>
            </div>
        </section>

    </section>
</main>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>