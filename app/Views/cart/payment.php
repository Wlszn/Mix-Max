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


<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
