<?php 
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';

$eventId = (int) ($event['eventId'] ?? 0);
$title = $event['title'] ?? 'Untitled Event';
$artist = $event['artist'] ?? '';
$imageUrl = $event['imageUrl'] ?? '';
$date = $event['date'] ?? '';
$startTime = $event['startTime'] ?? '';
$venueName = $event['venueName'] ?? '';
$city = $event['city'] ?? '';
$price = $event['minPrice'] ?? $event['price'] ?? 45; 

// Let's format the date to look nicer (e.g., "Thu, May 14")
$formattedDate = !empty($date) ? date('D, M j', strtotime($date)) : 'TBA';
$formattedTime = !empty($startTime) ? date('g:i A', strtotime($startTime)) : '';
?>

<a href="<?= $basePath ?>/events/<?= $eventId ?>" 
   class="group block bg-white rounded-xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300">
    
    <!-- Image Section -->
    <div class="relative overflow-hidden aspect-video"> 
        <?php if (!empty($imageUrl)): ?> 
            <img src="<?= htmlspecialchars($imageUrl) ?>"
                 alt="<?= htmlspecialchars($title) ?>"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"> 
        <?php else: ?>
            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="text-xs font-medium">NO IMAGE AVAILABLE</span>
            </div>
        <?php endif; ?> 
        
        <!-- Badges -->
        <div class="absolute top-3 left-3 flex gap-2">
            <span class="bg-blue-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-md tracking-wider uppercase shadow-lg">
                Featured
            </span>
        </div>

    </div>

    <!-- Content Section -->
    <div class="p-5">
        <div class="flex justify-between items-start mb-2">
            <h3 class="font-bold text-lg text-slate-800 leading-tight group-hover:text-blue-600 transition-colors"> 
                <?= htmlspecialchars($title) ?>
            </h3>
        </div>

        <p class="text-sm font-medium text-slate-500 mb-4 flex items-center"> 
           <span class="w-4 h-[1px] bg-slate-300 mr-2"></span> <?= htmlspecialchars($artist) ?> 
        </p>

        <div class="space-y-2 text-sm text-slate-600">
            <div class="flex items-center gap-2"> 
                <span>📅</span> 
                <span class="font-semibold"><?= $formattedDate ?></span> 
                <?php if ($formattedTime): ?>
                    <span class="text-slate-400">•</span> 
                    <span><?= $formattedTime ?></span>
                <?php endif; ?>
            </div> 
            
            <?php if (!empty($venueName)): ?>
                <div class="flex items-center gap-2"> 
                    <span>📍</span> 
                    <span><?= htmlspecialchars($venueName) ?><span class="text-slate-400">, <?= htmlspecialchars($city) ?></span></span>
                </div> 
            <?php endif; ?>
        </div>

        <!-- Price and Action -->
        <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Tickets from</p>
                <p class="text-xl font-black text-slate-900"> $<?= number_format((float) $price, 2) ?> </p>
            </div>
            <span class="bg-slate-100 group-hover:bg-blue-600 group-hover:text-white text-slate-600 text-xs font-bold py-2 px-4 rounded-lg transition-all">
                Get Tickets
            </span>
        </div>
    </div>
</a>