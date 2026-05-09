<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME : '';

$user    = $user    ?? $_SESSION['user'] ?? [];
$errors  = $errors  ?? [];
$success = $success ?? '';
$old     = $old     ?? [];
unset($_SESSION['flash_errors'], $_SESSION['flash_success'], $_SESSION['flash_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'My Profile') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-10">

    <!-- Page Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="w-16 h-16 bg-slate-950 rounded-2xl flex items-center justify-center text-white text-2xl font-bold">
            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($user['username'] ?? '') ?></h1>
            <p class="text-slate-500 text-sm"><?= htmlspecialchars($user['email'] ?? '') ?> &bull;
                <span class="inline-flex items-center gap-1 <?= ($user['role'] ?? '') === 'admin' ? 'text-blue-600' : 'text-slate-400' ?>">
                    <?= ($user['role'] ?? '') === 'admin' ? '⭐ Admin' : 'Member' ?>
                </span>
            </p>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 text-sm flex items-center gap-2">
            <span>✓</span> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-3 gap-6">

        <!-- Sidebar Nav -->
        <aside class="md:col-span-1">
            <nav class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <a href="#info"
                   onclick="showTab('info')"
                   id="tab-btn-info"
                   class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-medium border-b border-slate-100 hover:bg-slate-50 transition-colors text-slate-900 bg-slate-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Personal Info
                </a>
                <a href="#password"
                   onclick="showTab('password')"
                   id="tab-btn-password"
                   class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-medium hover:bg-slate-50 transition-colors text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Change Password
                </a>
            </nav>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= $basePath ?>/admin"
                   class="mt-4 flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl text-sm font-semibold transition-colors w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Admin Dashboard
                </a>
            <?php endif; ?>
        </aside>

        <!-- Main Content -->
        <div class="md:col-span-2 space-y-6">

            <!-- Personal Info Tab -->
            <div id="tab-info" class="tab-panel">
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-5">Personal Information</h2>

                    <form method="POST" action="<?= $basePath ?>/profile/update" class="space-y-4">
                        <input type="hidden" name="section" value="info">

                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                            <input
                                type="text" name="username"
                                value="<?= htmlspecialchars($old['username'] ?? $user['username'] ?? '') ?>"
                                class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['username']) ? 'border-red-400 bg-red-50' : 'border-slate-200' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Your username"
                            >
                            <?php if (!empty($errors['username'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                            <input
                                type="email" name="email"
                                value="<?= htmlspecialchars($old['email'] ?? $user['email'] ?? '') ?>"
                                class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-slate-200' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="you@example.com"
                            >
                            <?php if (!empty($errors['email'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Phone number
                                <span class="text-slate-400 font-normal">(E.164 format)</span>
                            </label>
                            <input
                                type="tel" name="phone"
                                value="<?= htmlspecialchars($old['phone'] ?? $user['phone'] ?? '') ?>"
                                class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['phone']) ? 'border-red-400 bg-red-50' : 'border-slate-200' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="+15141234567"
                            >
                            <?php if (!empty($errors['phone'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['phone']) ?></p>
                            <?php else: ?>
                                <p class="mt-1 text-xs text-slate-400">Used for 2FA SMS verification during login</p>
                            <?php endif; ?>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="bg-slate-950 hover:bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Tab -->
            <div id="tab-password" class="tab-panel hidden">
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-5">Change Password</h2>

                    <form method="POST" action="<?= $basePath ?>/profile/update" class="space-y-4">
                        <input type="hidden" name="section" value="password">

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Current password</label>
                            <input
                                type="password" name="current_password"
                                class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['current_password']) ? 'border-red-400 bg-red-50' : 'border-slate-200' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="••••••••"
                            >
                            <?php if (!empty($errors['current_password'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['current_password']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">New password</label>
                            <input
                                type="password" name="new_password"
                                class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['new_password']) ? 'border-red-400 bg-red-50' : 'border-slate-200' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Min. 8 characters"
                            >
                            <?php if (!empty($errors['new_password'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['new_password']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
                            <input
                                type="password" name="confirm_password"
                                class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['confirm_password']) ? 'border-red-400 bg-red-50' : 'border-slate-200' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Repeat new password"
                            >
                            <?php if (!empty($errors['confirm_password'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="bg-slate-950 hover:bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
function showTab(name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('text-slate-900', 'bg-slate-50');
        b.classList.add('text-slate-500');
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + name);
    btn.classList.add('text-slate-900', 'bg-slate-50');
    btn.classList.remove('text-slate-500');
}

// Auto-open password tab if there are password errors
<?php if (!empty($errors['current_password']) || !empty($errors['new_password']) || !empty($errors['confirm_password'])): ?>
document.addEventListener('DOMContentLoaded', () => showTab('password'));
<?php endif; ?>
</script>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>