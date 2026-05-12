<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$events = $events ?? [];
?>

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="bg-slate-50 min-h-screen">
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Manage Events</h1>
                <p class="text-slate-600 mt-1">View, approve, reject, or delete events.</p>
            </div>

            <a href="<?= $basePath ?>/admin"
               class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-semibold">
                Back to Dashboard
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="text-left px-5 py-4">Event</th>
                        <th class="text-left px-5 py-4">Venue</th>
                        <th class="text-left px-5 py-4">Category</th>
                        <th class="text-left px-5 py-4">Date</th>
                        <th class="text-left px-5 py-4">Status</th>
                        <th class="text-right px-5 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                No events found.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($events as $event): ?>
                        <?php
                            $status = $event['status'] ?? 'pending';

                            $statusClass = match ($status) {
                                'scheduled' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'completed' => 'bg-slate-100 text-slate-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                'postponed' => 'bg-orange-100 text-orange-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        ?>

                        <tr class="border-t border-slate-100">
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-950">
                                    <?= htmlspecialchars($event['title'] ?? '') ?>
                                </p>
                                <p class="text-xs text-slate-500">
                                    <?= htmlspecialchars($event['artist'] ?? '') ?>
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p><?= htmlspecialchars($event['venueName'] ?? '') ?></p>
                                <p class="text-xs text-slate-500">
                                    <?= htmlspecialchars($event['city'] ?? '') ?>
                                </p>
                            </td>

                            <td class="px-5 py-4 capitalize">
                                <?= htmlspecialchars($event['category'] ?? 'N/A') ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars($event['date'] ?? '') ?>
                            </td>

                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <?php if ($status !== 'scheduled'): ?>
                                        <form method="POST" action="<?= $basePath ?>/admin/events/<?= (int) $event['eventId'] ?>/approve">
                                            <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg font-semibold">
                                                Approve
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($status !== 'rejected'): ?>
                                        <form method="POST" action="<?= $basePath ?>/admin/events/<?= (int) $event['eventId'] ?>/reject">
                                            <button type="submit"
                                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg font-semibold">
                                                Reject
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST"
                                          action="<?= $basePath ?>/admin/events/<?= (int) $event['eventId'] ?>/delete"
                                          onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>