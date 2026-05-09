<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME : '';
$errors      = $errors      ?? [];
$maskedPhone = $maskedPhone ?? '';
$secondsLeft = $seconds_left ?? 0;
$success     = $success     ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Verify') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            <!-- Icon + heading -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-50 rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Check your phone</h1>
                <p class="text-gray-500 mt-1">
                    We sent a 6-digit code to
                    <span class="font-semibold text-gray-700"><?= htmlspecialchars($maskedPhone) ?></span>
                </p>
            </div>

            <!-- Success banner -->
            <?php if (!empty($success)): ?>
                <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm text-center">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Error banner -->
            <?php if (!empty($errors['general'])): ?>
                <div class="mb-5 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm text-center">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <!-- OTP form -->
            <form method="POST" action="<?= $basePath ?>/verify" class="space-y-5">

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Verification code
                    </label>

                    <!-- 6-digit input boxes -->
                    <div class="flex gap-2 justify-center mb-1" id="otp-boxes">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <input
                                type="text"
                                maxlength="1"
                                inputmode="numeric"
                                pattern="[0-9]"
                                class="otp-digit w-11 h-12 text-center text-xl font-bold border <?= isset($errors['code']) ? 'border-red-400 bg-red-50' : 'border-gray-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                autocomplete="one-time-code"
                            >
                        <?php endfor; ?>
                    </div>

                    <!-- Hidden input that holds the assembled code -->
                    <input type="hidden" name="code" id="code-hidden">

                    <?php if (!empty($errors['code'])): ?>
                        <p class="mt-1 text-xs text-red-600 text-center"><?= htmlspecialchars($errors['code']) ?></p>
                    <?php endif; ?>
                </div>

                <button
                    type="submit"
                    id="submit-btn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors"
                >
                    Verify Code
                </button>

            </form>

            <!-- Resend -->
            <div class="mt-6 text-center text-sm text-gray-500">
                Didn't receive it?
                <span id="resend-wrapper">
                    <?php if ($secondsLeft > 0): ?>
                        <span class="text-gray-400">
                            Resend in <span id="countdown"><?= (int)$secondsLeft ?></span>s
                        </span>
                    <?php else: ?>
                        <a href="<?= $basePath ?>/resend-otp"
                           class="text-blue-600 hover:underline font-medium">
                            Resend code
                        </a>
                    <?php endif; ?>
                </span>
            </div>

            <div class="mt-4 text-center">
                <a href="<?= $basePath ?>/login" class="text-sm text-gray-400 hover:text-gray-600">
                    ← Back to sign in
                </a>
            </div>

        </div>
    </div>
</main>

<script>
// ── OTP box behaviour ─────────────────────────────────────────────────────────
const digits  = document.querySelectorAll('.otp-digit');
const hidden  = document.getElementById('code-hidden');
const form    = document.querySelector('form');

digits.forEach((box, idx) => {
    box.addEventListener('input', (e) => {
        const val = e.target.value.replace(/\D/g, '');
        e.target.value = val;

        if (val && idx < digits.length - 1) {
            digits[idx + 1].focus();
        }

        assembleCode();
    });

    box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && idx > 0) {
            digits[idx - 1].focus();
        }
    });

    // Handle paste on any box
    box.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        [...pasted].slice(0, 6).forEach((ch, i) => {
            if (digits[i]) digits[i].value = ch;
        });
        const nextEmpty = [...digits].findIndex(d => !d.value);
        if (nextEmpty !== -1) digits[nextEmpty].focus();
        else digits[5].focus();
        assembleCode();
    });
});

function assembleCode() {
    hidden.value = [...digits].map(d => d.value).join('');
}

// Auto-submit when all 6 digits entered
form.addEventListener('input', () => {
    if ([...digits].every(d => d.value)) {
        assembleCode();
        form.submit();
    }
});

// ── Countdown timer ───────────────────────────────────────────────────────────
const countdownEl = document.getElementById('countdown');
const resendWrapper = document.getElementById('resend-wrapper');
const basePath = '<?= $basePath ?>';

<?php if ($secondsLeft > 0): ?>
let remaining = <?= (int)$secondsLeft ?>;

const timer = setInterval(() => {
    remaining--;
    if (countdownEl) countdownEl.textContent = remaining;

    if (remaining <= 0) {
        clearInterval(timer);
        resendWrapper.innerHTML = `<a href="${basePath}/resend-otp" class="text-blue-600 hover:underline font-medium">Resend code</a>`;
    }
}, 1000);
<?php endif; ?>
</script>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>