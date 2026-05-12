<?php

declare(strict_types=1);

use App\Domain\Services\EventService;
use App\Domain\Services\TwilioVerifyService;
use App\Domain\Services\CloudinaryService;
use App\Domain\Services\UserService;
use App\Helpers\Core\AppSettings;
use App\Helpers\Core\JsonRenderer;
use App\Helpers\Core\PDOService;
use App\Middleware\ExceptionMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Views\PhpRenderer;
use Stripe\Stripe;

$definitions = [

    AppSettings::class => function (): AppSettings {
        return new AppSettings(require_once __DIR__ . '/settings.php');
    },

    App::class => function (ContainerInterface $container): App {
        $app = AppFactory::createFromContainer($container);
        $app->setBasePath(APP_ROOT_DIR_NAME ? '/' . APP_ROOT_DIR_NAME : '');
        (require_once __DIR__ . '/../app/Routes/web-routes.php')($app);
        (require_once __DIR__ . '/middleware.php')($app);
        return $app;
    },

    PhpRenderer::class => function (): PhpRenderer {
        return new PhpRenderer(APP_VIEWS_PATH);
    },

    PDOService::class => function (ContainerInterface $container): PDOService {
        $db_config = $container->get(AppSettings::class)->get('db');
        return new PDOService($db_config);
    },

    EventService::class => function (ContainerInterface $container): EventService {
        return new EventService($container->get(PDOService::class), $container->get(CloudinaryService::class));
    },

    UserService::class => function (ContainerInterface $container): UserService {
        return new UserService($container->get(PDOService::class));
    },

    TwilioVerifyService::class => function (ContainerInterface $container): TwilioVerifyService {
        $twilio = $container->get(AppSettings::class)->get('twilio');
        return new TwilioVerifyService(
            $twilio['account_sid']        ?? '',
            $twilio['auth_token']         ?? '',
            $twilio['verify_service_sid'] ?? ''
        );
    },

    CloudinaryService::class => function (ContainerInterface $container): CloudinaryService {
        $cloudinary = $container->get(AppSettings::class)->get('cloudinary');
        return new CloudinaryService(
            $cloudinary['cloud_name'] ?? '',
            $cloudinary['api_key'] ?? '',
            $cloudinary['api_secret'] ?? ''
        );
    },
    // HTTP factories
    ResponseFactoryInterface::class      => fn() => new ResponseFactory(),
    ServerRequestFactoryInterface::class => fn() => new ServerRequestFactory(),
    StreamFactoryInterface::class        => fn() => new StreamFactory(),
    UriFactoryInterface::class           => fn() => new UriFactory(),

    ExceptionMiddleware::class => function (ContainerInterface $container): ExceptionMiddleware {
        $settings = $container->get(AppSettings::class)->get('error');
        return new ExceptionMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(JsonRenderer::class),
            $container->get(PhpRenderer::class),
            null,
            (bool) $settings['display_error_details'],
        );
    },
];

return $definitions;