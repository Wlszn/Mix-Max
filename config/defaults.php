<?php

declare(strict_types=1);

// Application's default settings.

// Error reporting.
// Default settings: disable all error reporting for production.
error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Timezone.
date_default_timezone_set('America/Toronto');

function myCustomErrorHandler(int $error_no, string $error_message, string $file, int $line_number)
{
    echo sprintf(
        "<strong>Error:</strong> %s <br><strong>Message:</strong> %s <br> <strong> occurred in:</strong> [%s] <strong> at line:</strong> [%s] <br>",
        getErrorName($error_no),
        $error_message,
        $file,
        $line_number
    );
}

set_error_handler('myCustomErrorHandler');


$settings = [];

// Error handler.
$settings['error'] = [
    // Should be set to false for the production environment
    'display_error_details' => false,
];


//TODO: Set the session path to a temporary directory.
$settings['session'] = [
    'name' => 'app_session',
    'lifetime' => 30 * 557200,
    //'path' => realpath(__DIR__ . '/../var/tmp'),
    // Must be set to the application's base directory name.
    'path' => '/' . APP_ROOT_DIR_NAME,
    'domain' => 'localhost',
    'secure' => false,
    'httponly' => true,
    'cache_limiter' => 'nocache',
];

// Logger settings.
$settings['logger'] = [
    // Log file location
    'path' => APP_BASE_DIR_PATH . '/var/logs',
    // Default log level
    'level' => Psr\Log\LogLevel::DEBUG,
];

// Database settings.
$settings['db'] = [
    'host' => 'localhost',
    'encoding' => 'utf8mb4',
    'options' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => false,
        // Leave column names as returned by the database driver.
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Set character set (use new constant on PHP 8.4+, fallback for older versions)
        (class_exists('Pdo\\Mysql') ? Pdo\Mysql::ATTR_INIT_COMMAND : PDO::MYSQL_ATTR_INIT_COMMAND) => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ]
];

return $settings;
