<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$event = $event ?? [];
$tickets = $tickets ?? [];
$similarEvents = $similarEvents ?? [];

$groupedSeats = [];

foreach ($tickets as $ticket) {
    $section = $ticket['section'] ?? 'General';
    $row = $ticket['rowLetter'] ?? 'A';
    $groupedSeats[$section][$row][] = $ticket;
}

$startingPrice = !empty($tickets)
    ? min(array_column($tickets, 'price'))
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['title'] ?? 'Event Details') ?> - Mix Max</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-slate-950">
<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">

    <a href="<?= $basePath ?>/events" class="text-sm text-slate-600 hover:text-blue-600">
        ← Back to events
    </a>

    <!-- Top Details -->
    <section class="grid lg:grid-cols-2 gap-8 mt-5 mb-10">
        <div>
            <?php if (!empty($event['imageUrl'])): ?>
                <img
                    src="<?= htmlspecialchars($event['imageUrl']) ?>"
                    alt="<?= htmlspecialchars($event['title']) ?>"
                    class="w-full h-[430px] object-cover rounded-2xl shadow-sm"
                >
            <?php else: ?>
                <div class="w-full h-[430px] bg-slate-200 rounded-2xl flex items-center justify-center text-slate-500">
                    No Image
                </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                    <?= htmlspecialchars($event['category'] ?? 'event') ?>
                </span>
                <span class="text-sm text-slate-500">
                    <?= htmlspecialchars($event['artist'] ?? '') ?>
                </span>
            </div>

            <h1 class="text-4xl font-bold mb-4">
                <?= htmlspecialchars($event['title']) ?>
            </h1>

            <div class="space-y-3 text-slate-700 mb-8">
                <p>📅 <?= htmlspecialchars($event['date']) ?></p>
                <p>🕒 <?= htmlspecialchars(substr($event['startTime'] ?? '', 0, 5)) ?> - <?= htmlspecialchars(substr($event['endTime'] ?? '', 0, 5)) ?></p>
                <p>📍 <?= htmlspecialchars($event['venueName'] ?? '') ?>, <?= htmlspecialchars($event['city'] ?? '') ?></p>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-5">
                <p class="text-sm text-slate-500">Tickets from</p>
                <p class="text-4xl font-bold text-blue-600">
                    $<?= number_format((float)$startingPrice, 2) ?>
                </p>
            </div>

            <a href="#seats"
               class="block text-center bg-slate-950 hover:bg-blue-600 text-white py-3 rounded-xl font-semibold transition-colors">
                Select Tickets
            </a>
        </div>
    </section>

    <hr class="border-slate-200 mb-10">

    <!-- Event Info + Seats -->
    <section class="grid lg:grid-cols-3 gap-8 mb-14">
        <!-- Event Details -->
        <div class="lg:col-span-1">
            <h2 class="text-2xl font-bold mb-4">About this event</h2>

            <p class="text-slate-600 leading-7 mb-6">
                <?= nl2br(htmlspecialchars($event['description'] ?? 'No description available.')) ?>
            </p>

            <div class="grid gap-3">
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Venue</p>
                    <p class="font-semibold"><?= htmlspecialchars($event['venueName'] ?? '') ?></p>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Address</p>
                    <p class="font-semibold"><?= htmlspecialchars($event['address'] ?? '') ?></p>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Category</p>
                    <p class="font-semibold"><?= htmlspecialchars($event['category'] ?? 'Event') ?></p>
                </div>
            </div>
        </div>

        <!-- Seats -->
        <div id="seats" class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-4">Choose Your Seats</h2>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="bg-slate-950 text-white text-center py-3 rounded-xl mb-6 font-semibold">
                    STAGE
                </div>

                <?php if (empty($groupedSeats)): ?>
                    <p class="text-slate-500">No tickets available for this event.</p>
                <?php else: ?>
                    <?php foreach ($groupedSeats as $sectionName => $rows): ?>
                        <div class="mb-8">
                            <h3 class="font-bold mb-4"><?= htmlspecialchars($sectionName) ?></h3>

                            <?php foreach ($rows as $rowLetter => $rowTickets): ?>
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="w-6 text-sm font-semibold text-slate-500">
                                        <?= htmlspecialchars($rowLetter) ?>
                                    </span>

                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($rowTickets as $ticket): ?>
                                            <?php
                                                $isHeld = !empty($ticket['heldUntil']) && strtotime($ticket['heldUntil']) > time();
                                            ?>

                                            <?php if ($isHeld): ?>
                                                <button
                                                    disabled
                                                    class="w-9 h-9 rounded-md bg-slate-400 text-white text-sm cursor-not-allowed">
                                                    <?= htmlspecialchars($ticket['seatNumber']) ?>
                                                </button>
                                            <?php else: ?>
                                                <form method="post" action="<?= $basePath ?>/cart/add" class="inline">
                                                    <input type="hidden" name="ticketId" value="<?= (int)$ticket['ticketId'] ?>">
                                                    <button
                                                        type="submit"
                                                        class="w-9 h-9 rounded-md bg-slate-100 hover:bg-blue-600 hover:text-white text-sm transition-colors">
                                                        <?= htmlspecialchars($ticket['seatNumber']) ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="flex items-center gap-6 text-sm text-slate-500 pt-4 border-t border-slate-200">
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 bg-slate-100 border rounded"></span> Available
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 bg-blue-600 rounded"></span> Selected
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 bg-slate-400 rounded"></span> Taken/Held
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Similar Events -->
    <section class="mb-14">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold">Similar Events</h2>
            <a href="<?= $basePath ?>/events" class="text-blue-600 hover:text-blue-700 font-semibold">
                View all →
            </a>
        </div>

        <?php if (empty($similarEvents)): ?>
            <p class="text-slate-500">No similar events found.</p>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php foreach ($similarEvents as $similar): ?>
                    <a href="<?= $basePath ?>/events/<?= (int)$similar['eventId'] ?>"
                       class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-lg transition-shadow">
                        <?php if (!empty($similar['imageUrl'])): ?>
                            <img
                                src="<?= htmlspecialchars($similar['imageUrl']) ?>"
                                alt="<?= htmlspecialchars($similar['title']) ?>"
                                class="w-full h-40 object-cover"
                            >
                        <?php endif; ?>

                        <div class="p-4">
                            <h3 class="font-bold mb-1">
                                <?= htmlspecialchars($similar['title']) ?>
                            </h3>
                            <p class="text-sm text-slate-500 mb-1">
                                <?= htmlspecialchars($similar['date']) ?>
                            </p>
                            <p class="text-sm text-slate-500">
                                <?= htmlspecialchars($similar['venueName'] ?? '') ?>, <?= htmlspecialchars($similar['city'] ?? '') ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>