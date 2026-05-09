<?php

declare(strict_types=1);

use App\Domain\Services\BookingService;
use App\Domain\Services\CartService;
use App\Domain\Services\EventService;
use App\Domain\Services\TicketService;
use App\Domain\Services\TwilioVerifyService;
use App\Domain\Services\UserService;
use App\Domain\Services\VenueService;
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
        return new EventService($container->get(PDOService::class));
    },

    TicketService::class => function (ContainerInterface $container): TicketService {
        return new TicketService($container->get(PDOService::class));
    },

    BookingService::class => function (ContainerInterface $container): BookingService {
        return new BookingService($container->get(PDOService::class));
    },

    VenueService::class => function (ContainerInterface $container): VenueService {
        return new VenueService($container->get(PDOService::class));
    },

    UserService::class => function (ContainerInterface $container): UserService {
        return new UserService($container->get(PDOService::class));
    },

    // Twilio: wrapped in try/catch so missing credentials don't crash every page.
    // It will only throw when AuthController actually tries to send an OTP.
    TwilioVerifyService::class => function (ContainerInterface $container): TwilioVerifyService {
        $settings = $container->get(AppSettings::class)->get();
        $twilio   = $settings['twilio'] ?? [];
        return new TwilioVerifyService(
            $twilio['account_sid']        ?? '',
            $twilio['auth_token']         ?? '',
            $twilio['verify_service_sid'] ?? ''
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