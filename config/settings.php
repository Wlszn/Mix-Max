<?php

declare(strict_types=1);

//TODO: set the app environment based on the domaine name.
// The APP_ENV value must be set to either prod or dev
//$_ENV['APP_ENV'] = 'prod';
$_ENV['APP_ENV'] ??= $_SERVER['APP_ENV'] ?? 'dev';

// Docker uses dev settings.
$app_environment = in_array($_ENV['APP_ENV'], ['docker', 'dev']) ? 'dev' : $_ENV['APP_ENV'];

// Load default settings.
$settings = require __DIR__ . '/defaults.php';


if (!file_exists(__DIR__ . '/env.php')) {

    trigger_error('&nbsp; env.php file not found. Please create it in the <strong>config folder</strong> by copying <b>env.example.php</b> and renaming it to <b>env.php</b>. For more details about configuring this application, refer to: <br><a href="https://github.com/frostybee/slim-mvc?tab=readme-ov-file#how-do-i-usedeploy-this-template" target="_blank">Configuration instructions in README.md</a>');
    exit;
}


// Override default settings with environment specific local settings.
$config_files = [
    __DIR__ . sprintf('/settings.%s.php', $app_environment),
    __DIR__ . '/env.php',
    __DIR__ . '/../../env.php',
];

foreach ($config_files as $config_file) {
    if (!file_exists($config_file)) {
        continue;
    }

    $local_settings = require $config_file;
    if (is_callable($local_settings)) {
        $settings = $local_settings($settings);
    }
}

return $settings;
