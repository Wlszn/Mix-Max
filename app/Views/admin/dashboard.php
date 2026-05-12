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

<main class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-white">

    <section class="bg-slate-950/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-slate-300 mt-1">
                    Website overview and event management.
                </p>
            </div>

            <a href="<?= $basePath ?>/admin/events"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-2xl font-semibold transition">
                Review Pending Events
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-8 space-y-8">

        <!-- MAIN STATS -->
        <div class="grid md:grid-cols-4 gap-6">

            <div
                class="rounded-3xl p-6 bg-gradient-to-br from-blue-700 to-indigo-900 shadow-2xl border border-white/10 hover:scale-[1.02] transition-transform duration-200">
                <p class="text-blue-100 text-sm">Total Revenue</p>
                <p class="text-4xl font-bold mt-3">
                    $<?= number_format($totalRevenue, 2) ?>
                </p>
            </div>

            <div
                class="rounded-3xl p-6 bg-gradient-to-br from-indigo-600 to-purple-900 shadow-2xl border border-white/10 hover:scale-[1.02] transition-transform duration-200">
                <p class="text-indigo-100 text-sm">Total Users</p>
                <p class="text-4xl font-bold mt-3">
                    <?= number_format($totalUsers) ?>
                </p>
            </div>

            <div
                class="rounded-3xl p-6 bg-gradient-to-br from-sky-600 to-blue-900 shadow-2xl border border-white/10 hover:scale-[1.02] transition-transform duration-200">
                <p class="text-sky-100 text-sm">Tickets Sold</p>
                <p class="text-4xl font-bold mt-3">
                    <?= number_format($ticketsSold) ?>
                </p>
            </div>

            <div
                class="rounded-3xl p-6 bg-gradient-to-br from-purple-600 to-fuchsia-900 shadow-2xl border border-white/10 hover:scale-[1.02] transition-transform duration-200">
                <p class="text-purple-100 text-sm">Total Events</p>
                <p class="text-4xl font-bold mt-3">
                    <?= number_format($totalEvents) ?>
                </p>
            </div>

        </div>

        <!-- SECONDARY STATS -->
        <div class="grid md:grid-cols-3 gap-6">

            <div class="rounded-3xl p-6 bg-slate-900/80 border border-yellow-500/20 shadow-xl">
                <p class="text-yellow-200 text-sm">Pending Approval</p>
                <p class="text-4xl font-bold mt-3">
                    <?= $pendingEvents ?>
                </p>
            </div>

            <div class="rounded-3xl p-6 bg-slate-900/80 border border-blue-500/20 shadow-xl">
                <p class="text-blue-200 text-sm">Active Events</p>
                <p class="text-4xl font-bold mt-3">
                    <?= $activeEvents ?>
                </p>
            </div>

            <div class="rounded-3xl p-6 bg-slate-900/80 border border-purple-500/20 shadow-xl">
                <p class="text-purple-200 text-sm">Completed Events</p>
                <p class="text-4xl font-bold mt-3">
                    <?= $completedEvents ?>
                </p>
            </div>

        </div>

        <!-- ANALYTICS -->
        <div class="grid lg:grid-cols-2 gap-6">

            <!-- CATEGORY -->
            <section class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl p-6">

                <h2 class="text-2xl font-bold mb-6 text-white">
                    Events by Category
                </h2>

                <div class="space-y-5">

                    <?php foreach ($categoryData as $category): ?>

                        <?php
                        $name = $category['category'] ?? 'Uncategorized';
                        $count = (int) ($category['total'] ?? 0);
                        $percent = $totalEvents > 0
                            ? round(($count / $totalEvents) * 100)
                            : 0;
                        ?>

                        <div>

                            <div class="flex justify-between text-sm mb-2">
                                <span class="capitalize text-slate-200">
                                    <?= htmlspecialchars($name) ?>
                                </span>

                                <span class="font-semibold text-slate-300">
                                    <?= $count ?> events · <?= $percent ?>%
                                </span>
                            </div>

                            <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"
                                    style="width: <?= $percent ?>%">
                                </div>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </section>

            <!-- RECENT EVENTS -->
            <section class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl p-6">

                <h2 class="text-2xl font-bold mb-6 text-white">
                    Recent Events
                </h2>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead>
                            <tr class="border-b border-white/10 text-slate-400">
                                <th class="text-left py-3">Event</th>
                                <th class="text-left py-3">Status</th>
                                <th class="text-right py-3">Tickets</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($recentEvents as $event): ?>

                                <tr class="border-b border-white/5">

                                    <td class="py-4">

                                        <p class="font-semibold text-white">
                                            <?= htmlspecialchars($event['title']) ?>
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            <?= htmlspecialchars($event['date']) ?>
                                        </p>

                                    </td>

                                    <td class="py-4">

                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold
                                            <?= $event['status'] === 'scheduled' ? 'bg-green-500/20 text-green-300' : '' ?>
                                            <?= $event['status'] === 'pending' ? 'bg-yellow-500/20 text-yellow-300' : '' ?>
                                            <?= $event['status'] === 'rejected' ? 'bg-red-500/20 text-red-300' : '' ?>
                                            <?= $event['status'] === 'completed' ? 'bg-slate-500/20 text-slate-300' : '' ?>">

                                            <?= htmlspecialchars($event['status']) ?>

                                        </span>

                                    </td>

                                    <td class="py-4 text-right font-semibold text-white">
                                        <?= (int) $event['tickets'] ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

        <!-- QUICK ACTIONS -->
        <section
            class="rounded-3xl p-6 text-white bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 border border-white/10 shadow-2xl">

            <h2 class="text-2xl font-bold mb-6">
                Quick Actions
            </h2>

            <div class="grid md:grid-cols-3 gap-4">

                <a href="<?= $basePath ?>/admin/events"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-5 transition-all duration-200">

                    <p class="font-bold text-lg">
                        Review Pending Events
                    </p>

                    <p class="text-sm text-slate-300 mt-1">
                        <?= $pendingEvents ?> awaiting approval
                    </p>

                </a>

                <a href="<?= $basePath ?>/events/create"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-5 transition-all duration-200">

                    <p class="font-bold text-lg">
                        Create Event
                    </p>

                    <p class="text-sm text-slate-300 mt-1">
                        Add a new event
                    </p>

                </a>

                <a href="<?= $basePath ?>/events"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-5 transition-all duration-200">

                    <p class="font-bold text-lg">
                        Browse Site
                    </p>

                    <p class="text-sm text-slate-300 mt-1">
                        View public events
                    </p>

                </a>

                <a href="<?= $basePath ?>/admin/users"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-5 transition-all duration-200">

                    <p class="font-bold text-lg">
                        Manage Users
                    </p>

                    <p class="text-sm text-slate-300 mt-1">
                        View and manage user accounts
                    </p>

                </a>

                <a href="<?= $basePath ?>/admin/events/manage"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-5 transition-all duration-200">

                    <p class="font-bold text-lg">
                        Manage Events
                    </p>

                    <p class="text-sm text-slate-300 mt-1">
                        View and manage events
                    </p>

                </a>

            </div>

        </section>

    </section>

</main>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>