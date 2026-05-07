<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';
?>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="<?= $basePath ?>/assets/css/style.css">

<script>window.basePath = "<?= $basePath ?>";</script>

<script src="<?= $basePath ?>/assets/js/live-search.js"></script>
<script src="<?= $basePath ?>/assets/js/filter-toggle.js"></script>