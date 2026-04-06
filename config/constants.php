<?php

declare(strict_types=1);

// Define the path of the application's root directory.
define('APP_BASE_DIR_PATH', dirname(__DIR__, 1));

// Holds the name of the app's root directory.
// In Docker, this can be set via environment variable; otherwise, uses the folder name.
// NOTE: the APP_ROOT_DIR_NAME constant is used to set the base path of the application. Do not remove or change it.
define('APP_ROOT_DIR_NAME', $_ENV['APP_ROOT_DIR'] ?? basename(dirname(__FILE__, 2)));

// Define the path of the application's views directory.
const APP_VIEWS_PATH = APP_BASE_DIR_PATH . '/app/Views';



//* HTTP response status code.
const HTTP_OK = 200;
const HTTP_CREATED = 201;
const HTTP_NO_CONTENT = 204;
const HTTP_METHOD_NOT_ALLOWED = 405;
const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
const HTTP_NOT_FOUND = 404;
//* HTTP response headers.
const HEADERS_CONTENT_TYPE = "Content-Type";
//* Supported Media Types.
const APP_MEDIA_TYPE_JSON = "application/json";
const APP_MEDIA_TYPE_XML = "application/xml";
const APP_MEDIA_TYPE_YAML = "application/yaml";
