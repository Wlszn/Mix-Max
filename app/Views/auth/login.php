<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME : '';

$errors  = $errors ?? [];
$old     = $old    ?? [];
$success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_errors'], $_SESSION['flash_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Sign In') ?></title>
</head>
<body class="bg-gray-50 min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4 py-12">
    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
                <p class="text-gray-500 mt-1">Sign in to your Mix Max account</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $basePath ?>/login" class="space-y-5">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="you@example.com"
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border <?= isset($errors['password']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors"
                >
                    Sign In
                </button>

            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="<?= $basePath ?>/register" class="text-blue-600 hover:underline font-medium">Create one</a>
            </p>
        </div>
    </div>
</main>
<?php require __DIR__ . '/common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>