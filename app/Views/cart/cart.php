<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$cart = $cart ?? [];
$totalPrice = array_sum(array_column($cart, 'price'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="flex-grow">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">

            <?php if (empty($cart)): ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">🛒</div>
                    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h1>
                    <p class="text-gray-500 mb-6">Start browsing events to add tickets to your cart</p>
                    <a href="<?= $basePath ?>/events" class="inline-block bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800">
                        Browse Events
                    </a>
                </div>
            <?php else: ?>
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Your Cart</h1>
                <p class="mb-4 text-right font-semibold">Total: $<?= number_format($totalPrice, 2) ?></p>
                
                <?php if (count($cart) === 1): ?>
                    <?php foreach ($cart as $item): ?>
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Ticket</h2>
                                    <p class="text-sm text-gray-600">Ticket ID: <?= htmlspecialchars($item['ticketId'] ?? 'Unknown') ?></p>
                                    <p class="text-sm text-gray-600">Section: <?= htmlspecialchars($item['section'] ?? 'Unknown') ?></p>
                                    <p class="text-sm text-gray-600">Row Letter: <?= htmlspecialchars($item['rowLetter'] ?? 'Unknown') ?></p>
                                    <p class="text-sm text-gray-600">Seat Number: <?= htmlspecialchars($item['seatNumber'] ?? 'Unknown') ?></p>
                                    <p class="text-sm text-gray-600 font-semibold">Price: $<?= htmlspecialchars($item['price'] ?? '0.00') ?></p>
                                </div>
                                <div class="flex gap-2">
                                    <form method="post" action="<?= $basePath ?>/cart/remove">
                                        <input type="hidden" name="ticketId" value="<?= (int)$item['ticketId'] ?>">
                                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Remove</button>
                                    </form>
                                    <form method="post" action="<?= $basePath ?>/cart/buy">
                                        <input type="hidden" name="ticketId" value="<?= (int)$item['ticketId'] ?>">
                                        <button class="bg-slate-950 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">Buy Now</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                <?php else: ?>
                    <!-- Buy Selected Form with checkboxes INSIDE -->
                    <form id="buySelectedForm" method="post" action="<?= $basePath ?>/cart/buy-selected">
                        <?php foreach ($cart as $item): ?>
                            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-4">
                                    <input type="checkbox" name="ticketIds[]" value="<?= (int)$item['ticketId'] ?>" class="mr-4 mt-1">
                                    <div class="flex-grow text-left">
                                        <h2 class="text-lg font-semibold text-gray-900">Ticket</h2>
                                        <p class="text-sm text-gray-600">Ticket ID: <?= htmlspecialchars($item['ticketId'] ?? 'Unknown') ?></p>
                                        <p class="text-sm text-gray-600">Section: <?= htmlspecialchars($item['section'] ?? 'Unknown') ?></p>
                                        <p class="text-sm text-gray-600">Row Letter: <?= htmlspecialchars($item['rowLetter'] ?? 'Unknown') ?></p>
                                        <p class="text-sm text-gray-600">Seat Number: <?= htmlspecialchars($item['seatNumber'] ?? 'Unknown') ?></p>
                                        <p class="text-sm text-gray-600 font-semibold">Price: $<?= htmlspecialchars($item['price'] ?? '0.00') ?></p>
                                    </div>
                                    <div>
                                        <!-- Remove button as a separate form (works because it's submitted via JavaScript) -->
                                        <button type="button" onclick="removeTicket(<?= (int)$item['ticketId'] ?>)" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="flex gap-4 justify-center mt-6">
                            <button type="submit" class="bg-slate-950 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold">
                                Buy Selected
                            </button>
                        </div>
                    </form>
                    
                    <!-- Clear Cart Form -->
                    <div class="mt-4 text-center">
                        <form method="post" action="<?= $basePath ?>/cart/clear">
                            <button class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
function removeTicket(ticketId) {
    if (confirm('Remove this ticket from cart?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= $basePath ?>/cart/remove';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ticketId';
        input.value = ticketId;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>