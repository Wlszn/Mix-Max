<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\App;

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if ($autoloadPath !== false && is_file($autoloadPath)) {
    require $autoloadPath;
} else {
    die('<br><strong>Error:</strong> Composer autoload file not found. <br> <br><strong>Fix:</strong> Please run the following command in a <strong>VS Code command prompt terminal</strong> to install the missing dependencies: <br><strong>Command (keep the double quotes):</strong> <span style="background-color: yellow;"> "../../composer.bat" update </span><br> For more details, refer to: <br><a href="https://github.com/frostybee/slim-mvc?tab=readme-ov-file#how-do-i-usedeploy-this-template" target="_blank">Configuration instructions in README.md</a>');
}

// Load the app's global constants.
require_once __DIR__ . '/constants.php';
// Include the global functions that will be used across the app's various components.
require __DIR__ . '/functions.php';

// Configure the DI container and load dependencies.
$definitions = require_once __DIR__ . '/container.php';

// Build DI container instance.
//@see https://php-di.org/
$container = (new ContainerBuilder())
    ->addDefinitions($definitions)
    ->build();

// Create and return an App instance.
$app = $container->get(App::class);
return $app;
