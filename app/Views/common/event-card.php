<?php $basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';

$eventId = (int) ($event['eventId'] ?? 0);
$title = $event['title'] ?? 'Untitled Event';
$artist = $event['artist'] ?? '';
$imageUrl = $event['imageUrl'] ?? '';
$date = $event['date'] ?? '';
$startTime = $event['startTime'] ?? '';
$venueName = $event['venueName'] ?? '';
$city = $event['city'] ?? '';
$price = $event['minPrice'] ?? $event['price'] ?? 45; ?>

<a href="<?= $basePath ?>/events/<?= $eventId ?>" class="group card">
    <div class="relative"> <?php if (!empty($imageUrl)): ?> <img src="<?= htmlspecialchars($imageUrl) ?>"
                alt="<?= htmlspecialchars($title) ?>"
                class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300"> <?php else: ?>
            <div class="w-full h-44 bg-slate-200 flex items-center justify-center text-slate-500"> No Image </div>
        <?php endif; ?> <span class="badge"> FEATURED </span> <span
            class="absolute top-3 right-3 bg-white/90 text-slate-700 w-9 h-9 rounded-full flex items-center justify-center">
            ♡ </span>
    </div>
    <div class="p-4">
        <h3 class="font-bold text-base leading-snug mb-2 group-hover:text-blue-600"> <?= htmlspecialchars($title) ?>
        </h3>
        <p class="text-sm text-slate-600 mb-3"> <?= htmlspecialchars($artist) ?> </p>
        <div class="space-y-1 text-sm text-slate-500">
            <p> 📅 <?= htmlspecialchars($date) ?> <?php if (!empty($startTime)): ?> ·
                    <?= htmlspecialchars(substr($startTime, 0, 5)) ?> <?php endif; ?>
            </p> <?php if (!empty($venueName)): ?>
                <p> 📍 <?= htmlspecialchars($venueName) ?>     <?php if (!empty($city)): ?> , <?= htmlspecialchars($city) ?>
                    <?php endif; ?>
                </p> <?php endif; ?>
        </div>
        <div class="mt-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500">From</p>
                <p class="text-xl font-bold text-blue-600"> $<?= number_format((float) $price, 2) ?> </p>
            </div>
            <p class="text-xs text-slate-500"> View details → </p>
        </div>
    </div>
</a>