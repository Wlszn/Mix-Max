<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';
$tickets        = $tickets        ?? [];
$events         = $events         ?? [];
$selected_event = $selected_event ?? 0;
$success        = $success        ?? '';
unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Manage Tickets') ?></title>
</head>
<body class="bg-slate-50 min-h-screen">
<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="<?= $basePath ?>/admin" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <span class="text-slate-900 font-medium">Tickets</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Manage Tickets</h1>
        <span class="text-sm text-slate-500"><?= count($tickets) ?> tickets</span>
    </div>

    <?php if (!empty($success)): ?>
        <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 text-sm">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Filter by Event -->
    <form method="get" action="<?= $basePath ?>/admin/tickets" class="mb-5 flex gap-3">
        <select name="event" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="0">All Events</option>
            <?php foreach ($events as $ev): ?>
                <option value="<?= (int)$ev['eventId'] ?>" <?= $selected_event === (int)$ev['eventId'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ev['title']) ?> (<?= htmlspecialchars($ev['date']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button class="bg-slate-950 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-600 transition-colors">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Seat</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Event</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Price</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($tickets as $t): ?>
                    <?php
                    $isHeld = !empty($t['heldUntil']) && strtotime($t['heldUntil']) > time();
                    ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <p class="font-mono font-medium text-slate-900">
                                <?= htmlspecialchars($t['section']) ?> – <?= htmlspecialchars($t['rowLetter']) ?><?= htmlspecialchars($t['seatNumber']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs"><?= htmlspecialchars($t['eventTitle'] ?? $t['title'] ?? '—') ?></td>
                        <td class="px-6 py-4 text-slate-900 font-medium">$<?= number_format((float)$t['price'], 2) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $isHeld ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' ?>">
                                <?= $isHeld ? 'Held' : 'Available' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?= $basePath ?>/admin/tickets/<?= (int)$t['ticketId'] ?>/delete"
                               class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                               onclick="return confirm('Delete this ticket permanently?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No tickets found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>