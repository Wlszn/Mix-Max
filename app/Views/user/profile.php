<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME : '';

$errors  = $errors  ?? [];
$success = $success ?? '';
$old     = $old     ?? [];

// Use $old values (on validation fail) or current user data
$displayUsername = $old['username'] ?? $user['username'] ?? '';
$displayEmail    = $old['email']    ?? $user['email']    ?? '';
$displayPhone    = $old['phone']    ?? $user['phone']    ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'My Profile') ?></title>
</head>
<body class="bg-slate-50 min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-10">

    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">My Profile</h1>
        <p class="text-slate-500 mt-1">Manage your personal information and password</p>
    </div>

    <!-- Success banner -->
    <?php if (!empty($success)): ?>
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 text-sm">
            <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $basePath ?>/profile" class="space-y-6">

        <!-- ── Personal Information ──────────────────────────────────────── -->
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="flex items-center gap-3 mb-6">
                <!-- Avatar initial -->
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-bold shrink-0">
                    <?= strtoupper(substr($displayUsername, 0, 1)) ?>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Personal Information</h2>
                    <p class="text-sm text-slate-500">Update your name, email, and phone number</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($displayUsername) ?>"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['username']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                        placeholder="johndoe"
                    >
                    <?php if (!empty($errors['username'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Email address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($displayEmail) ?>"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                        placeholder="you@example.com"
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Phone -->
                <div class="md:col-span-2">
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Phone number
                        <span class="font-normal text-slate-400">(used for SMS verification)</span>
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($displayPhone) ?>"
                        class="w-full px-4 py-2.5 rounded-xl border <?= isset($errors['phone']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                        placeholder="+15141234567"
                    >
                    <?php if (!empty($errors['phone'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= htmlspecialchars($errors['phone']) ?></p>
                    <?php else: ?>
                        <p class="mt-1.5 text-xs text-slate-400">Include country code, e.g. +1 for Canada/US. Leave blank to keep unchanged.</p>
                    <?php endif; ?>
                </div>

            </div>
        </section>

        <!-- ── Change Password ───────────────────────────────────────────── -->
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">Change Password</h2>
                <p class="text-sm text-slate-500 mt-0.5">Leave all three fields blank if you don't want to change your password</p>
            </div>

            <div class="grid md:grid-cols-2 gap-5">

                <!-- Current password — full width -->
                <div class="md:col-span-2">
                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Current password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="w-full px-4 py-2.5 pr-11 rounded-xl border <?= isset($errors['current_password']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <button type="button" onclick="toggleVisibility('current_password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errors['current_password'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= htmlspecialchars($errors['current_password']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- New password -->
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        New password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="w-full px-4 py-2.5 pr-11 rounded-xl border <?= isset($errors['new_password']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                            placeholder="Min. 8 characters"
                            autocomplete="new-password"
                        >
                        <button type="button" onclick="toggleVisibility('new_password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errors['new_password'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= htmlspecialchars($errors['new_password']) ?></p>
                    <?php endif; ?>

                    <!-- Password strength bar -->
                    <div id="strength-bar-wrap" class="mt-2 hidden">
                        <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                            <div id="strength-bar" class="h-full rounded-full transition-all duration-300 w-0"></div>
                        </div>
                        <p id="strength-label" class="text-xs mt-1 text-slate-500"></p>
                    </div>
                </div>

                <!-- Confirm new password -->
                <div>
                    <label for="confirm_new_password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Confirm new password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="confirm_new_password"
                            name="confirm_new_password"
                            class="w-full px-4 py-2.5 pr-11 rounded-xl border <?= isset($errors['confirm_new_password']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                            placeholder="Repeat new password"
                            autocomplete="new-password"
                        >
                        <button type="button" onclick="toggleVisibility('confirm_new_password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errors['confirm_new_password'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= htmlspecialchars($errors['confirm_new_password']) ?></p>
                    <?php endif; ?>
                </div>

            </div>
        </section>

        <!-- ── Account info (read-only) ──────────────────────────────────── -->
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-5">Account Details</h2>

            <div class="grid sm:grid-cols-3 gap-4 text-sm">
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-slate-500 text-xs mb-1 uppercase tracking-wide font-semibold">Role</p>
                    <p class="font-semibold text-slate-800 capitalize"><?= htmlspecialchars($user['role'] ?? 'user') ?></p>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-slate-500 text-xs mb-1 uppercase tracking-wide font-semibold">Member since</p>
                    <p class="font-semibold text-slate-800">
                        <?= !empty($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : '—' ?>
                    </p>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-slate-500 text-xs mb-1 uppercase tracking-wide font-semibold">User ID</p>
                    <p class="font-semibold text-slate-800">#<?= (int) ($user['userId'] ?? 0) ?></p>
                </div>
            </div>
        </section>

        <!-- ── Submit ─────────────────────────────────────────────────────── -->
        <div class="flex items-center justify-between pt-2">
            <a href="<?= $basePath ?>/" class="text-sm text-slate-500 hover:text-slate-700">
                ← Back to home
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-7 py-2.5 rounded-xl transition-colors text-sm"
            >
                Save Changes
            </button>
        </div>

    </form>

</main>

<script>
// ── Toggle password visibility ──────────────────────────────────────────────
function toggleVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';

    // Swap eye icon colour as a visual cue
    btn.classList.toggle('text-blue-500', !isText);
    btn.classList.toggle('text-slate-400', isText);
}

// ── Password strength meter ──────────────────────────────────────────────────
const newPwInput  = document.getElementById('new_password');
const strengthBar = document.getElementById('strength-bar');
const strengthLbl = document.getElementById('strength-label');
const strengthWrap = document.getElementById('strength-bar-wrap');

const levels = [
    { label: 'Too short',  color: '#ef4444', pct: '15%'  },
    { label: 'Weak',       color: '#f97316', pct: '30%'  },
    { label: 'Fair',       color: '#eab308', pct: '55%'  },
    { label: 'Good',       color: '#22c55e', pct: '80%'  },
    { label: 'Strong',     color: '#16a34a', pct: '100%' },
];

function scorePassword(pw) {
    if (pw.length < 8) return 0;
    let score = 1;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    return Math.min(score, 4);
}

newPwInput.addEventListener('input', () => {
    const val = newPwInput.value;
    if (!val) {
        strengthWrap.classList.add('hidden');
        return;
    }
    strengthWrap.classList.remove('hidden');
    const idx = scorePassword(val);
    const lvl = levels[idx];
    strengthBar.style.width = lvl.pct;
    strengthBar.style.backgroundColor = lvl.color;
    strengthLbl.textContent = lvl.label;
    strengthLbl.style.color = lvl.color;
});
</script>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>