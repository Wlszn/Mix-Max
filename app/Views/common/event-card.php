<?php
function renderEventCard($event) {
    $date = new DateTime($event['date']);
    $formattedDate = $date->format('M d, Y');
    $eventId = htmlspecialchars($event['id']);
    $title = htmlspecialchars($event['title']);
    $artist = htmlspecialchars($event['artist']);
    $venue = htmlspecialchars($event['venue']);
    $location = htmlspecialchars($event['location']);
    $category = htmlspecialchars($event['category']);
    $image = htmlspecialchars($event['image']);
    $time = htmlspecialchars($event['time']);
    $minPrice = htmlspecialchars($event['priceRange']['min']);
    
    ?>
    <a href="event-detail.php?id=<?php echo $eventId; ?>" class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow">
        <div class="relative h-48 overflow-hidden">
            <img
                src="<?php echo $image; ?>"
                alt="<?php echo $title; ?>"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute top-2 right-2 bg-blue-600 text-white px-3 py-1 rounded-full text-sm capitalize">
                <?php echo $category; ?>
            </div>
        </div>
        <div class="p-4">
            <h3 class="text-xl font-semibold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">
                <?php echo $title; ?>
            </h3>
            <p class="text-gray-600 mb-3"><?php echo $artist; ?></p>
            <div class="space-y-2 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></rect>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                    </svg>
                    <span><?php echo $formattedDate; ?></span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                        <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    </svg>
                    <span><?php echo $time; ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                    </svg>
                    <span><?php echo $venue; ?>, <?php echo $location; ?></span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">From</span>
                    <span class="text-xl font-bold text-gray-900">$<?php echo $minPrice; ?></span>
                </div>
            </div>
        </div>
    </a>
    <?php
}
?>