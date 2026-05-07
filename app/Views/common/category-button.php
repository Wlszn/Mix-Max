<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';

$categoryName = strtolower($category['name'] ?? '');
$categoryLabel = $category['label'] ?? ucfirst($categoryName);
$isActive = $category['active'] ?? false;

$icons = [
    'concert' => '♪',
    'sports' => '◉',
    'theater' => '▣',
    'comedy' => '☺',
];

$icon = $icons[$categoryName] ?? '•';

$url = $categoryName === ''
    ? $basePath . '/events'
    : $basePath . '/events?' . http_build_query(['category' => $categoryName]);

$classes = $isActive
    ? 'px-3 py-1.5 rounded-full bg-slate-950 text-white text-sm flex items-center gap-2'
    : 'px-3 py-1.5 rounded-full border border-slate-400 bg-white text-sm hover:bg-slate-100 flex items-center gap-2';
?>

<a href="<?= htmlspecialchars($url) ?>" class="<?= $classes ?>">
    <span><?= htmlspecialchars($icon) ?></span>
    <span><?= htmlspecialchars($categoryLabel) ?></span>
</a>