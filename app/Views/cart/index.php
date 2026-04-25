<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$cart = $cart ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Your Cart</h1>

        <?php if (empty($cart)): ?>
            <p class="text-gray-600">Your cart is empty.</p>

            <a href="<?= $basePath ?>/events"
               class="inline-block mt-6 bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
                Browse Events
            </a>
        <?php else: ?>
            <div class="space-y-4">
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../common/footer.php'; ?>