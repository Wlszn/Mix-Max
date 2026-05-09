<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';
$events  = $events  ?? [];
$search  = $search  ?? '';
$status  = $status  ?? '';
$success = $success ?? '';
unset($_SESSION['flash_success']);

$statusColors = [
    'scheduled' => 'bg-green-100 text-green-700',
    'pending'   => 'bg-yellow-100 text-yellow-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'rejected'  => 'bg-red-100 text-red-700',
    'postponed' => 'bg-orange-100 text-orange-700',
    'completed' => 'bg-slate-100 text-slate-500',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Manage Events') ?></title>
</head>
<body class="bg-slate-50 min-h-screen">
<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="<?= $basePath ?>/admin" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <span class="text-slate-900 font-medium">Events</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Manage Events</h1>
        <span class="text-sm text-slate-500"><?= count($events) ?> events</span>
    </div>

    <?php if (!empty($success)): ?>
        <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 text-sm">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <form method="get" action="<?= $basePath ?>/admin/events" class="mb-5 flex flex-wrap gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
            placeholder="Search events..."
            class="flex-1 min-w-48 px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Statuses</option>
            <?php foreach (['pending','scheduled','cancelled','postponed','completed','rejected'] as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="bg-slate-950 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-600 transition-colors">Filter</button>
        <?php if ($search || $status): ?>
            <a href="<?= $basePath ?>/admin/events" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-100">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Event</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Venue</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($events as $e): ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-900"><?= htmlspecialchars($e['title']) ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($e['artist'] ?? '') ?></p>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($e['date']) ?></td>
                        <td class="px-6 py-4 text-slate-500 text-xs"><?= htmlspecialchars($e['venueName'] ?? '—') ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $statusColors[$e['status'] ?? ''] ?? 'bg-slate-100 text-slate-500' ?>">
                                <?= htmlspecialchars($e['status'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= $basePath ?>/events/<?= (int)$e['eventId'] ?>"
                                   class="px-3 py-1.5 text-xs font-medium bg-slate-50 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors" target="_blank">
                                    View
                                </a>
                                <?php if (($e['status'] ?? '') === 'pending'): ?>
                                    <a href="<?= $basePath ?>/admin/events/<?= (int)$e['eventId'] ?>/approve"
                                       class="px-3 py-1.5 text-xs font-medium bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors"
                                       onclick="return confirm('Approve this event?')">
                                        Approve
                                    </a>
                                    <a href="<?= $basePath ?>/admin/events/<?= (int)$e['eventId'] ?>/reject"
                                       class="px-3 py-1.5 text-xs font-medium bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors"
                                       onclick="return confirm('Reject this event?')">
                                        Reject
                                    </a>
                                <?php endif; ?>
                                <a href="<?= $basePath ?>/admin/events/<?= (int)$e['eventId'] ?>/delete"
                                   class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                   onclick="return confirm('Delete this event permanently?')">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($events)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>