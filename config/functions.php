<?php

declare(strict_types=1);

/**
 * This file contains various helper functions that can be used throughout the
 * application. These functions are not part of any class and can be called
 * directly.
 */


/**
 * Returns the application's base URL.
 *
 * This function provides a single point of truth for the base URL,
 * detecting the protocol and host dynamically. It handles both Docker
 * (root path) and local development (subdirectory) environments.
 *
 * @param string $path Optional path to append to the base URL.
 * @return string The full base URL, optionally with appended path.
 *
 * @example
 * base_url();           // 'http://localhost/app-name' or 'http://localhost:8080'
 * base_url('/');        // 'http://localhost/app-name/' or 'http://localhost:8080/'
 * base_url('/users');   // 'http://localhost/app-name/users' or 'http://localhost:8080/users'
 */
function base_url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = APP_ROOT_DIR_NAME ? '/' . APP_ROOT_DIR_NAME : '';

    return $protocol . '://' . $host . $basePath . $path;
}

/**
 * dd: dump and die.
 *
 * This helper function is very useful for debugging: it outputs the content of the supplied variable and
 * terminates the execution of the application.
 *
 * @see https://www.php.net/manual/en/function.var-dump.php
 * @see https://www.php.net/manual/en/function.die.php
 *
 * @param  mixed $data The variable whose content needs to be dumped.
 * @return void
 */
function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Escapes a string for safe output in HTML context.
 *
 * This function is a wrapper around htmlspecialchars() with secure defaults
 * for preventing Cross-Site Scripting (XSS) attacks. It converts special
 * characters to HTML entities using UTF-8 encoding and HTML5 standards.
 *
 * Characters escaped:
 * - & (ampersand) becomes &amp;
 * - " (double quote) becomes &quot;
 * - ' (single quote) becomes &#039;
 * - < (less than) becomes &lt;
 * - > (greater than) becomes &gt;
 *
 * @param string $string The input string to be escaped.
 *
 * @return string The escaped string safe for HTML output.
 *
 * @example
 * echo e('<script>alert("XSS")</script>');
 * // Outputs: &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;
 *
 * @example
 * echo '<input value="' . e($userInput) . '">';
 * // Safely outputs user input in HTML attribute
 *
 * @see htmlspecialchars()
 * @link https://www.php.net/manual/en/function.htmlspecialchars.php
 */
function hs($string)
{
    return htmlspecialchars((string) $string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 | ENT_DISALLOWED, 'UTF-8');
}


if (!function_exists('asset_url')) {
    /**
     * Returns the full URL for an asset file with optional cache busting.
     *
     * This is the recommended way to reference any static asset (CSS, JS, images,
     * fonts, etc.) in your views. It provides full flexibility for custom HTML
     * attributes while handling cache busting and minification automatically.
     *
     * Features:
     * - Verifies the file exists before generating the URL
     * - Adds cache busting token in production (optional)
     * - Auto-switches to .min.js/.min.css in production
     * - Works with any asset type (CSS, JS, images, fonts, etc.)
     *
     * @param string $asset_uri Path relative to public/assets/ (e.g., '/css/style.css').
     * @param bool $cache_bust Whether to add cache busting token (default: true).
     *
     * @return string The full asset URL, or empty string if file not found.
     *
     * @example
     * <link href="<?= asset_url('/css/style.css') ?>" rel="stylesheet">
     * <link href="<?= asset_url('/css/print.css') ?>" rel="stylesheet" media="print">
     * <script src="<?= asset_url('/js/app.js') ?>"></script>
     * <script src="<?= asset_url('/js/app.js') ?>" defer async></script>
     * <img src="<?= asset_url('/images/logo.png') ?>" alt="Logo">
     * <img src="<?= asset_url('/images/versioned-v2.png', false) ?>" alt="No cache bust">
     */
    function asset_url(string $asset_uri, bool $cache_bust = true): string
    {
        // Check if file exists
        $file_path = APP_ASSETS_DIR_PATH . $asset_uri;
        if (!file_exists($file_path)) {
            return '';
        }

        $cache_busting_token = $cache_bust ? '?' . filemtime($file_path) : '';

        // Auto-switch to minified versions in production
        if (!APP_DEBUG_MODE) {
            if (str_contains($asset_uri, '.js') && !str_contains($asset_uri, '.min.js')) {
                $asset_uri = str_replace('.js', '.min.js', $asset_uri);
            }
            if (str_contains($asset_uri, '.css') && !str_contains($asset_uri, '.min.css')) {
                $asset_uri = str_replace('.css', '.min.css', $asset_uri);
            }
        }

        return base_url(APP_ASSETS_DIR . $asset_uri) . $cache_busting_token;
    }
}

/**
 * Removes the trailing seconds from the supplied date.
 *
 * @param  mixed $date The date in string representation to be formatted.
 * @return string The formatted date without seconds.
 */
function date_remove_secs(string $date): string
{
    return date('Y-m-d H:i', strtotime($date));
}

/**
 * Get the name of the error.
 *
 * @param  int $error_no The error number.
 * @return string The name of the error.
 */
function getErrorName(int $error_no): string
{
    $error_types = [
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
    ];

    return $error_types[$error_no] ?? "UNKNOWN_ERROR";
}
