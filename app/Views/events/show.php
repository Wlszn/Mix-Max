<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$event = $event ?? [];
$tickets = $tickets ?? [];

$sections = ['Orchestra', 'Mezzanine', 'Balcony'];
$rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
$seatsPerRow = 12;

function detailDate(string $date): string
{
    return date('l, F j, Y', strtotime($date));
}

function detailTime(string $time): string
{
    return date('g:i A', strtotime($time));
}

function detailCategory(array $event): string
{
    $text = strtolower(
        ($event['title'] ?? '') . ' ' .
        ($event['artist'] ?? '') . ' ' .
        ($event['description'] ?? '')
    );

    if (str_contains($text, 'sport') || str_contains($text, 'nba') || str_contains($text, 'lakers') || str_contains($text, 'warriors') || str_contains($text, 'soccer') || str_contains($text, 'hockey')) {
        return 'sports';
    }

    if (str_contains($text, 'theater') || str_contains($text, 'theatre') || str_contains($text, 'hamilton') || str_contains($text, 'play')) {
        return 'theater';
    }

    if (str_contains($text, 'comedy') || str_contains($text, 'comedian') || str_contains($text, 'chappelle')) {
        return 'comedy';
    }

    return 'concert';
}

function detailCategoryClass(string $category): string
{
    return match ($category) {
        'sports' => 'bg-green-100 text-green-700',
        'theater' => 'bg-red-100 text-red-700',
        'comedy' => 'bg-yellow-100 text-yellow-700',
        default => 'bg-purple-100 text-purple-700',
    };
}

$category = detailCategory($event);
$startingPrice = $event['startingPrice'] ?? 0;

$seatMap = [];

foreach ($tickets as $ticket) {
    $section = $ticket['section'] ?? '';
    $row = $ticket['rowLetter'] ?? '';
    $seat = (string)($ticket['seatNumber'] ?? '');

    if ($section !== '' && $row !== '' && $seat !== '') {
        $seatMap[$section][$row][$seat] = $ticket;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title'] ?? 'Event Details') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">
    <a href="<?= $basePath ?>/events" class="inline-block mb-8 font-semibold text-gray-900">
        ← Back
    </a>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div>
            <?php if (!empty($event['imageUrl'])): ?>
                <img
                    src="<?= htmlspecialchars($event['imageUrl']) ?>"
                    alt="<?= htmlspecialchars($event['title']) ?>"
                    class="w-full h-[430px] object-cover rounded-xl"
                >
            <?php else: ?>
                <div class="w-full h-[430px] bg-gray-200 rounded-xl flex items-center justify-center text-gray-500">
                    No image
                </div>
            <?php endif; ?>
        </div>

        <div class="flex flex-col">
            <div class="flex items-start justify-between gap-4 mb-5">
                <h1 class="text-4xl font-bold text-gray-900">
                    <?= htmlspecialchars($event['title'] ?? 'Event') ?>
                </h1>

                <span class="shrink-0 text-xs font-semibold px-3 py-2 rounded-lg <?= detailCategoryClass($category) ?>">
                    <?= htmlspecialchars($category) ?>
                </span>
            </div>

            <p class="text-xl mb-4">
                📅 <?= htmlspecialchars(detailDate($event['date'])) ?>
            </p>

            <p class="text-xl mb-4">
                🕒 <?= htmlspecialchars(detailTime($event['startTime'])) ?>
            </p>

            <p class="text-xl mb-6">
                📍 <?= htmlspecialchars($event['venueName'] ?? 'Venue') ?>
                <?php if (!empty($event['city'])): ?>
                    , <?= htmlspecialchars($event['city']) ?>
                <?php endif; ?>
            </p>

            <div class="border border-blue-200 bg-blue-50 rounded-xl p-6 mt-auto">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <p class="text-gray-700 mb-1">Tickets from</p>
                        <p class="text-5xl font-bold text-blue-600">
                            $<?= htmlspecialchars(number_format((float)$startingPrice, 0)) ?>
                        </p>
                    </div>

                    <div>
                    <button type="button" class="bg-gray-200 text-black px-5 py-3 rounded-lg font-semibold hover:bg-gray-800">
                        +
                    </button>
                    <button type="button" class="bg-blue-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-gray-800">
                        Buy Tickets
                    </button>
                  <button type="button" class="bg-gray-200 text-black px-5 py-3 rounded-lg font-semibold hover:bg-gray-800">
                        -
                    </button>
                     
                </div>

            </div>
        </div>
    </section>

    <div class="w-full max-w-md bg-gray-200 rounded-xl p-1 grid grid-cols-2 mb-10">
        <button
            type="button"
            id="selectSeatsTab"
            class="main-tab bg-white rounded-lg text-center py-2 font-semibold"
            onclick="showMainTab('seats')"
        >
            Select Seats
        </button>

        <button
            type="button"
            id="eventDetailsTab"
            class="main-tab rounded-lg text-center py-2 font-semibold"
            onclick="showMainTab('details')"
        >
            Event Details
        </button>
    </div>

    <section id="seatsSection" class="bg-white border border-gray-200 rounded-xl p-6 mb-10">
        <h2 class="text-2xl font-bold mb-6">Choose Your Seats</h2>

        <div class="flex justify-center gap-2 mb-6">
            <?php foreach ($sections as $index => $section): ?>
                <button
                    type="button"
                    id="<?= strtolower($section) ?>Tab"
                    class="zone-tab px-5 py-3 rounded-lg font-semibold border border-gray-300 <?= $index === 0 ? 'bg-black text-white' : 'bg-white text-black' ?>"
                    onclick="showZone('<?= strtolower($section) ?>')"
                >
                    <?= htmlspecialchars($section) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="bg-gray-800 text-white text-center py-4 rounded-lg mb-6">
            STAGE
        </div>

        <?php foreach ($sections as $index => $section): ?>
            <?php $sectionId = strtolower($section); ?>

            <div
                id="<?= $sectionId ?>Seats"
                class="zone-section border border-gray-200 rounded-xl p-6 min-h-[410px] <?= $index === 0 ? '' : 'hidden' ?>"
            >
                <h3 class="font-bold text-lg mb-6">
                    <?= htmlspecialchars($section) ?>
                </h3>

                <div class="space-y-3">
                    <?php foreach ($rows as $row): ?>
                        <div class="flex items-center gap-2">
                            <span class="w-6 text-gray-700"><?= $row ?></span>

                            <?php for ($seat = 1; $seat <= $seatsPerRow; $seat++): ?>
                                <?php
                                $ticket = $seatMap[$section][$row][(string)$seat] ?? null;
                                $isTaken = $ticket && !empty($ticket['heldUntil']) && strtotime($ticket['heldUntil']) > time();

                                $seatClass = $isTaken
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'bg-gray-200 hover:bg-blue-600 hover:text-white';
                                ?>

                                <button
                                    type="button"
                                    class="seat-btn w-8 h-8 rounded <?= $seatClass ?> text-sm"
                                    <?= $isTaken ? 'disabled' : '' ?>
                                >
                                    <?= $seat ?>
                                </button>
                            <?php endfor; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-center gap-8 mt-8 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 bg-gray-200 rounded inline-block"></span>
                Available
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 bg-blue-600 rounded inline-block"></span>
                Selected
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 bg-gray-400 rounded inline-block"></span>
                Taken
            </div>
        </div>
    </section>

    <section id="detailsSection" class="hidden bg-white border border-gray-200 rounded-xl p-6 mb-10">
        <h2 class="text-2xl font-bold mb-6">About This Event</h2>

        <p class="text-gray-700 mb-8">
            <?= htmlspecialchars($event['description'] ?? 'No description available.') ?>
        </p>

        <h3 class="text-xl font-bold mb-4">Venue Information</h3>

        <p class="mb-3">
            <strong>Venue:</strong>
            <?= htmlspecialchars($event['venueName'] ?? 'Venue') ?>
        </p>

        <p class="mb-3">
            <strong>Location:</strong>
            <?= htmlspecialchars($event['city'] ?? '') ?>
        </p>

        <p class="mb-3">
            <strong>Date:</strong>
            <?= htmlspecialchars(detailDate($event['date'])) ?>
        </p>

        <p class="mb-6">
            <strong>Time:</strong>
            <?= htmlspecialchars(detailTime($event['startTime'])) ?>
        </p>

        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
            <strong>Please Note:</strong>
            All sales are final. No refunds or exchanges. Please arrive 30 minutes before the event starts.
        </div>
    </section>
</main>

<script>
    function showMainTab(tab) {
        const seatsSection = document.getElementById('seatsSection');
        const detailsSection = document.getElementById('detailsSection');
        const selectSeatsTab = document.getElementById('selectSeatsTab');
        const eventDetailsTab = document.getElementById('eventDetailsTab');

        if (tab === 'seats') {
            seatsSection.classList.remove('hidden');
            detailsSection.classList.add('hidden');

            selectSeatsTab.classList.add('bg-white');
            eventDetailsTab.classList.remove('bg-white');
        } else {
            detailsSection.classList.remove('hidden');
            seatsSection.classList.add('hidden');

            eventDetailsTab.classList.add('bg-white');
            selectSeatsTab.classList.remove('bg-white');
        }
    }

    function showZone(zone) {
        document.querySelectorAll('.zone-section').forEach(function (section) {
            section.classList.add('hidden');
        });

        document.querySelectorAll('.zone-tab').forEach(function (button) {
            button.classList.remove('bg-black', 'text-white');
            button.classList.add('bg-white', 'text-black');
        });

        document.getElementById(zone + 'Seats').classList.remove('hidden');

        const activeButton = document.getElementById(zone + 'Tab');
        activeButton.classList.remove('bg-white', 'text-black');
        activeButton.classList.add('bg-black', 'text-white');
    }

    document.querySelectorAll('.seat-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.disabled) {
                return;
            }

            if (button.classList.contains('bg-blue-600')) {
                button.classList.remove('bg-blue-600', 'text-white');
                button.classList.add('bg-gray-200');
            } else {
                button.classList.remove('bg-gray-200');
                button.classList.add('bg-blue-600', 'text-white');
            }
        });
    });
</script>

</body>
</html>