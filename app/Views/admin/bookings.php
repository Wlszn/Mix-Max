<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';
$bookings = $bookings ?? [];
$success  = $success  ?? '';
unset($_SESSION['flash_success']);

$statusColors = [
    'pending'   => 'bg-yellow-100 text-yellow-700',
    'confirmed' => 'bg-green-100 text-green-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'completed' => 'bg-slate-100 text-slate-500',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Manage Bookings') ?></title>
</head>
<body class="bg-slate-50 min-h-screen">
<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="<?= $basePath ?>/admin" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <span class="text-slate-900 font-medium">Bookings</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Manage Bookings</h1>
        <span class="text-sm text-slate-500"><?= count($bookings) ?> bookings</span>
    </div>

    <?php if (!empty($success)): ?>
        <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 text-sm">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Reference</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">User</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($bookings as $b): ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded">
                                <?= htmlspecialchars($b['bookingRef'] ?? '—') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-900"><?= htmlspecialchars($b['username'] ?? '—') ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($b['email'] ?? '') ?></p>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($b['date']) ?></td>
                        <td class="px-6 py-4 font-semibold text-slate-900">$<?= number_format((float)$b['totalPrice'], 2) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $statusColors[$b['status'] ?? ''] ?? 'bg-slate-100 text-slate-500' ?>">
                                <?= htmlspecialchars($b['status'] ?? '') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>