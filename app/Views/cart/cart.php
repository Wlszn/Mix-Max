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
<body class="bg-gray-50">

<?php require __DIR__ . '/../common/header.php'; ?>

<div class="flex items-center justify-center min-h-[80vh] px-4">
    <div class="text-center">

        <?php if (empty($cart)): ?>

            <!-- Icon -->
            <div class="text-gray-400 text-6xl mb-4">
                🛒
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">
                Your cart is empty
            </h1>

            <!-- Subtitle -->
            <p class="text-gray-500 mb-6">
                Start browsing events to add tickets to your cart
            </p>

            <!-- Button -->
            <a href="<?= $basePath ?>/events"
               class="inline-block bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800">
                Browse Events
            </a>

        <?php else: ?>

            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Your Cart</h1>
                   <p>
                <?php if (isset($totalPrice)): ?>
                    Total: $<?= number_format($totalPrice, 2) ?>
                <?php endif; ?>
                </p>
                <?php if (count($cart) === 1): ?>
                    <?php foreach ($cart as $item): ?>
                        <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Ticket
                                </h2>

                                <p class="text-sm text-gray-600">
                                    Ticket ID:
                                    <?= htmlspecialchars(is_array($item) ? ($item['ticketId'] ?? 'Unknown') : $item) ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Section:
                                    <?= htmlspecialchars(is_array($item) ? ($item['section'] ?? 'Unknown') : $item) ?>
                                </p>

                                <p class="text-sm text-gray-600">
                                    Row Letter:
                                    <?= htmlspecialchars(is_array($item) ? ($item['rowLetter'] ?? 'Unknown') : $item) ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Seat Number:
                                    <?= htmlspecialchars(is_array($item) ? ($item['seatNumber'] ?? 'Unknown') : $item) ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Price:
                                    $<?= htmlspecialchars(is_array($item) ? ($item['price'] ?? '0.00') : '0.00') ?>
                                </p>
                                <form method="post" action="<?= $basePath ?>/cart/remove" class="inline">
                                    <input type="hidden" name="ticketId" value="<?= (int)$item['ticketId'] ?>">
                                    <button class="mt-2 inline-block bg-red-600 text-white px-4 py-1 rounded-lg hover:bg-red-700 font-semibold transition-colors">
                                        Remove
                                    </button>
                                </form>
                
                                <form method="post" action="<?= $basePath ?>/cart/buy" class="inline">
                                    <input type="hidden" name="ticketId" value="<?= (int)$item['ticketId'] ?>">
                                    <button class="mt-2 inline-block bg-slate-950 hover:bg-blue-600 text-white py-1 px-4 rounded-lg font-semibold transition-colors">
                                       Buy Now
                                    </button>
                                </form>
                               
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <form method="post" action="<?= $basePath ?>/cart/buy-selected">
                        <div class="space-y-4">
                            <?php foreach ($cart as $item): ?>
                                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="ticketIds[]" value="<?= (int)$item['ticketId'] ?>" class="mr-4">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900">
                                                Ticket
                                            </h2>

                                            <p class="text-sm text-gray-600">
                                                Ticket ID:
                                                <?= htmlspecialchars(is_array($item) ? ($item['ticketId'] ?? 'Unknown') : $item) ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Section:
                                                <?= htmlspecialchars(is_array($item) ? ($item['section'] ?? 'Unknown') : $item) ?>
                                            </p>

                                            <p class="text-sm text-gray-600">
                                                Row Letter:
                                                <?= htmlspecialchars(is_array($item) ? ($item['rowLetter'] ?? 'Unknown') : $item) ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Seat Number:
                                                <?= htmlspecialchars(is_array($item) ? ($item['seatNumber'] ?? 'Unknown') : $item) ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Price:
                                                $<?= htmlspecialchars(is_array($item) ? ($item['price'] ?? '0.00') : '0.00') ?>
                                            </p>
                                            </form>
                                            <form method="post" action="<?= $basePath ?>/cart/remove" class="inline">
                                                <input type="hidden" name="ticketId" value="<?= (int)$item['ticketId'] ?>">
                                                <button class="mt-2 inline-block bg-red-600 text-white px-4 py-1 rounded-lg hover:bg-red-700 font-semibold transition-colors">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-4">
                            <button type="submit" class="bg-slate-950 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold transition-colors">
                                Buy Selected
                            </button> 
                  
                      <form method="post" action="<?= $basePath ?>/cart/clear" class="inline">
                        <button class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold transition-colors">
                            Clear
                        </button>
                    </form>
                </div>
                <br>
                <?php endif; ?> 
            </div>

        <?php endif; ?>

    </div>
</div>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>