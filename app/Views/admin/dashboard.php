<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';
$stats         = $stats         ?? [];
$recent_users  = $recent_users  ?? [];
$recent_events = $recent_events ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin Dashboard') ?></title>
</head>
<body class="bg-slate-50 min-h-screen">
<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Title -->
    <div class="mb-8 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Admin Dashboard</h1>
            <p class="text-sm text-slate-500">Manage your platform</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <?php
        $cards = [
            ['label' => 'Users',    'value' => $stats['users']    ?? 0, 'color' => 'blue',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Events',   'value' => $stats['events']   ?? 0, 'color' => 'purple', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Tickets',  'value' => $stats['tickets']  ?? 0, 'color' => 'green',  'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
            ['label' => 'Bookings', 'value' => $stats['bookings'] ?? 0, 'color' => 'orange', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Revenue',  'value' => '$' . number_format((float)($stats['revenue'] ?? 0), 2), 'color' => 'emerald', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Pending',  'value' => $stats['pending_events'] ?? 0, 'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $colorMap = [
            'blue'    => 'bg-blue-50 text-blue-600',
            'purple'  => 'bg-purple-50 text-purple-600',
            'green'   => 'bg-green-50 text-green-600',
            'orange'  => 'bg-orange-50 text-orange-600',
            'emerald' => 'bg-emerald-50 text-emerald-600',
            'yellow'  => 'bg-yellow-50 text-yellow-600',
        ];
        foreach ($cards as $card): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide"><?= $card['label'] ?></span>
                    <div class="w-8 h-8 rounded-lg <?= $colorMap[$card['color']] ?> flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $card['icon'] ?>"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900"><?= $card['value'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Nav -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <?php
        $navItems = [
            ['href' => '/admin/users',    'label' => 'Manage Users',    'sub' => 'View, promote, delete', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['href' => '/admin/events',   'label' => 'Manage Events',   'sub' => 'Approve, reject, delete', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['href' => '/admin/tickets',  'label' => 'Manage Tickets',  'sub' => 'View, delete tickets', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
            ['href' => '/admin/bookings', 'label' => 'Manage Bookings', 'sub' => 'View all bookings', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ];
        foreach ($navItems as $item): ?>
            <a href="<?= $basePath . $item['href'] ?>"
               class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-md transition-all group">
                <div class="w-10 h-10 bg-slate-100 group-hover:bg-blue-600 rounded-xl flex items-center justify-center mb-3 transition-colors">
                    <svg class="w-5 h-5 text-slate-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $item['icon'] ?>"/>
                    </svg>
                </div>
                <p class="font-semibold text-slate-900 text-sm"><?= $item['label'] ?></p>
                <p class="text-xs text-slate-500 mt-0.5"><?= $item['sub'] ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Recent Activity -->
    <div class="grid md:grid-cols-2 gap-6">

        <!-- Recent Users -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-900">Recent Users</h2>
                <a href="<?= $basePath ?>/admin/users" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php foreach ($recent_users as $u): ?>
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-xs font-bold text-slate-600">
                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($u['username']) ?></p>
                            <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($u['email']) ?></p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full <?= $u['role'] === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' ?>">
                            <?= $u['role'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent_users)): ?>
                    <p class="px-6 py-4 text-sm text-slate-400">No users yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Events -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-900">Recent Events</h2>
                <a href="<?= $basePath ?>/admin/events" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php foreach ($recent_events as $e): ?>
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($e['title']) ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($e['date']) ?></p>
                        </div>
                        <?php
                        $statusColors = [
                            'scheduled' => 'bg-green-100 text-green-700',
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            'completed' => 'bg-slate-100 text-slate-500',
                        ];
                        $sc = $statusColors[$e['status'] ?? ''] ?? 'bg-slate-100 text-slate-500';
                        ?>
                        <span class="text-xs px-2 py-0.5 rounded-full <?= $sc ?>">
                            <?= htmlspecialchars($e['status'] ?? '') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent_events)): ?>
                    <p class="px-6 py-4 text-sm text-slate-400">No events yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>