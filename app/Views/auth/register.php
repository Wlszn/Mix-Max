<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME : '';

$errors = $errors ?? [];
$old    = $old    ?? [];
unset($_SESSION['flash_errors'], $_SESSION['flash_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Create Account') ?></title>
</head>
<body class="bg-gray-50 min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4 py-12">
    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
                <p class="text-gray-500 mt-1">Join Mix Max and start booking tickets</p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $basePath ?>/register" class="space-y-5">

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input
                        type="text" id="username" name="username"
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['username']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="johndoe"
                    >
                    <?php if (!empty($errors['username'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input
                        type="email" id="email" name="email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="you@example.com"
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone number
                        <span class="text-gray-400 font-normal">(for SMS verification)</span>
                    </label>
                    <input
                        type="tel" id="phone" name="phone"
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['phone']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="+15141234567"
                    >
                    <?php if (!empty($errors['phone'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['phone']) ?></p>
                    <?php else: ?>
                        <p class="mt-1 text-xs text-gray-400">Include country code, e.g. +1 for Canada/US</p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input
                        type="password" id="password" name="password"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['password']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Min. 8 characters"
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Confirm password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                    <input
                        type="password" id="confirm_password" name="confirm_password"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['confirm_password']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Repeat your password"
                    >
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                    <?php endif; ?>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors"
                >
                    Create Account
                </button>

            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account?
                <a href="<?= $basePath ?>/login" class="text-blue-600 hover:underline font-medium">Sign in</a>
            </p>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>