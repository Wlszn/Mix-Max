<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-6">Pending Event Reviews</h1>

    <div class="space-y-4">
        <?php foreach ($events as $event): ?>
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex justify-between gap-6">
                <div>
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($event['title']) ?></h2>
                    <p class="text-slate-600"><?= htmlspecialchars($event['artist']) ?></p>
                    <p class="text-sm text-slate-500">
                        <?= htmlspecialchars($event['category']) ?> ·
                        <?= htmlspecialchars($event['date']) ?> ·
                        <?= htmlspecialchars($event['city'] ?? '') ?>
                    </p>
                    <p class="mt-3 text-slate-700">
                        <?= htmlspecialchars($event['description'] ?? '') ?>
                    </p>
                </div>

                <div class="flex flex-col gap-2 min-w-32">
                    <form method="POST" action="<?= $basePath ?>/admin/events/<?= $event['eventId'] ?>/approve">
                        <button class="w-full bg-green-600 text-white px-4 py-2 rounded-lg">
                            Approve
                        </button>
                    </form>

                    <form method="POST" action="<?= $basePath ?>/admin/events/<?= $event['eventId'] ?>/reject">
                        <button class="w-full bg-red-600 text-white px-4 py-2 rounded-lg">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>