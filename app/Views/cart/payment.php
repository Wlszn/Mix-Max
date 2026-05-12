<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$cart = $_SESSION['payment_tickets'];

$totalPrice = array_sum(array_column($cart, 'price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-8">Payment</h1>

    <!-- Cart Items Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Selected Tickets</h2>
        <?php if (!empty($cart)): ?>
            <div class="space-y-4">
                <?php foreach ($cart as $item): ?>
                    <div class="flex justify-between items-center border-b pb-4">
                        <div>
                            <h3 class="text-lg font-medium">Ticket ID: <?php echo htmlspecialchars($item['ticketId'] ?? 'Unknown'); ?></h3>
                            <p class="text-gray-600">Section: <?php echo htmlspecialchars($item['section'] ?? 'Unknown'); ?></p>
                            <p class="text-gray-600">Row: <?php echo htmlspecialchars($item['rowLetter'] ?? 'Unknown'); ?>, Seat: <?php echo htmlspecialchars($item['seatNumber'] ?? 'Unknown'); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold">$<?php echo number_format($item['price'] ?? 0, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <h3 class="text-xl font-semibold">Total</h3>
                <p class="text-xl font-bold">$<?php echo number_format($totalPrice, 2); ?></p>
            </div>
        <?php else: ?>
            <p class="text-gray-500">No tickets selected.</p>
        <?php endif; ?>
    </div>

    <!-- Payment Form Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Credit Card Information</h2>
        <form action="#" method="post" class="space-y-4">
            <div>
                <label for="cardholder-name" class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                <input type="text" id="cardholder-name" name="cardholder_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="card-number" class="block text-sm font-medium text-gray-700">Card Number</label>
                <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="expiration-date" class="block text-sm font-medium text-gray-700">Expiration Date</label>
                    <input type="text" id="expiration-date" name="expiration_date" placeholder="MM/YY" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Pay Now
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
