<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$users = $users ?? [];
?>

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="bg-slate-50 min-h-screen">
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Manage Users</h1>
                <p class="text-slate-600 mt-1">View users, change roles, or delete accounts.</p>
            </div>

            <a href="<?= $basePath ?>/admin"
               class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-semibold">
                Back to Dashboard
            </a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="text-left px-5 py-4">ID</th>
                        <th class="text-left px-5 py-4">Username</th>
                        <th class="text-left px-5 py-4">Email</th>
                        <th class="text-left px-5 py-4">Phone</th>
                        <th class="text-left px-5 py-4">Role</th>
                        <th class="text-right px-5 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                No users found.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($users as $user): ?>
                        <tr class="border-t border-slate-100">
                            <td class="px-5 py-4 font-semibold">
                                <?= (int) $user['userId'] ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars($user['username'] ?? '') ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars($user['email'] ?? '') ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars($user['phone'] ?? 'N/A') ?>
                            </td>

                            <td class="px-5 py-4">
                                <form method="POST" action="<?= $basePath ?>/admin/users/<?= (int) $user['userId'] ?>/role">
                                    <select name="role"
                                            onchange="this.form.submit()"
                                            class="border border-slate-300 rounded-lg px-3 py-2">
                                        <option value="user" <?= ($user['role'] ?? '') === 'user' ? 'selected' : '' ?>>
                                            User
                                        </option>
                                        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                            Admin
                                        </option>
                                    </select>
                                </form>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <form method="POST"
                                      action="<?= $basePath ?>/admin/users/<?= (int) $user['userId'] ?>/delete"
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>