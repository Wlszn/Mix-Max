<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';
$users   = $users   ?? [];
$search  = $search  ?? '';
$success = $success ?? '';
unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Manage Users') ?></title>
</head>
<body class="bg-slate-50 min-h-screen">
<?php require __DIR__ . '/../common/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="<?= $basePath ?>/admin" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <span class="text-slate-900 font-medium">Users</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Manage Users</h1>
        <span class="text-sm text-slate-500"><?= count($users) ?> users</span>
    </div>

    <?php if (!empty($success)): ?>
        <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 text-sm">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Search -->
    <form method="get" action="<?= $basePath ?>/admin/users" class="mb-5">
        <div class="flex gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                placeholder="Search by username or email..."
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button class="bg-slate-950 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-600 transition-colors">Search</button>
            <?php if ($search): ?>
                <a href="<?= $basePath ?>/admin/users" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-100">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">User</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Role</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Joined</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-xs font-bold text-slate-600 shrink-0">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900"><?= htmlspecialchars($u['username']) ?></p>
                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($u['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($u['phone'] ?: '—') ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $u['role'] === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs"><?= htmlspecialchars(substr($u['created_at'] ?? '', 0, 10)) ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="<?= $basePath ?>/admin/users/<?= (int)$u['userId'] ?>/promote"
                                       class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                                       onclick="return confirm('Promote <?= htmlspecialchars($u['username']) ?> to admin?')">
                                        Promote
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $basePath ?>/admin/users/<?= (int)$u['userId'] ?>/demote"
                                       class="px-3 py-1.5 text-xs font-medium bg-slate-50 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors"
                                       onclick="return confirm('Demote this admin to user?')">
                                        Demote
                                    </a>
                                <?php endif; ?>
                                <a href="<?= $basePath ?>/admin/users/<?= (int)$u['userId'] ?>/delete"
                                   class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                   onclick="return confirm('Delete <?= htmlspecialchars($u['username']) ?>? This cannot be undone.')">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>
</body>
</html>