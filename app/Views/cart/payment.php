<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$cart = $tickets ?? [];
$totalPrice = $totalPrice ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="flex-grow">
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

        <!-- Checkout Button -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <p class="text-gray-600 mb-4">
                You'll be redirected to Stripe's secure checkout page to complete payment.
                Use test card <span class="font-mono font-semibold">4242 4242 4242 4242</span>, any future expiry, any CVC.
            </p>

            <form id="payment-form" method="post" action="<?= $basePath ?>/cart/process-payment">
                <button type="submit" id="submit-btn" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 font-semibold">
                    Pay $<?= number_format($totalPrice, 2) ?> with Stripe
                </button>
            </form>
        </div>
    </div>
</main>

<script>
const form = document.getElementById('payment-form');
const submitBtn = document.getElementById('submit-btn');
form.addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Redirecting to Stripe…';
});
</script>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>