<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$cart = $_SESSION['payment_tickets'] ?? [];
$totalPrice = array_sum(array_column($cart, 'price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
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
        
        <!-- Single form - not nested -->
        <form id="payment-form" method="post" action="<?= $basePath ?>/process-payment" class="space-y-4">
            <div>
                <label for="cardholder-name" class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                <input type="text" id="cardholder-name" name="cardholder_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <!-- Stripe Card Element -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Card Details</label>
                <div id="card-element" class="border border-gray-300 rounded-md p-3"></div>
                <div id="card-errors" class="text-red-600 text-sm mt-2 hidden"></div>
            </div>
            
            <div class="pt-4">
                <button type="submit" id="submit-btn" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Pay $<?= number_format($totalPrice, 2) ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const stripe = Stripe('<?= $stripePublicKey ?? '' ?>');
const elements = stripe.elements();
const cardElement = elements.create('card');
cardElement.mount('#card-element');

const form = document.getElementById('payment-form');
const cardErrors = document.getElementById('card-errors');
const submitBtn = document.getElementById('submit-btn');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    const {paymentMethod, error} = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: document.getElementById('cardholder-name').value,
        },
    });
    
    if (error) {
        cardErrors.textContent = error.message;
        cardErrors.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Pay $<?= number_format($totalPrice, 2) ?>';
    } else {
        // Add payment method ID to form and submit
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'paymentMethodId';
        hiddenInput.value = paymentMethod.id;
        form.appendChild(hiddenInput);
        form.submit();
    }
});
</script>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>