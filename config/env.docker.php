<?php

declare(strict_types=1);

/**
 * Docker environment configuration.
 *
 * This file is used when running the application in Docker containers.
 * It is automatically mounted as env.php by docker-compose.yml.
 *
 * WARNING: This file is tracked in Git. Only use it for LOCAL Docker
 * development with the default credentials below. NEVER put production
 * or sensitive credentials in this file.
 *
 * Database credentials must match those defined in docker-compose.yml.
 */

return function (array $settings): array {
    // Database credentials for Docker MySQL container
    $settings['db']['host'] = 'db';  // Docker service name
    $settings['db']['username'] = 'slim_user';
    $settings['db']['database'] = 'slim_mvc';
    $settings['db']['password'] = 'slim_pass';
    $settings['db']['port'] = '3306';

    return $settings;
};
