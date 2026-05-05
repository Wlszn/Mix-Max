<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return static function (Slim\App $app): void {

    // ── Home ────────────────────────────────────────────────────────────────
    $app->get('/', [HomeController::class, 'index'])->setName('home.index');
    $app->get('/home', [HomeController::class, 'index']);

    // ── Events ──────────────────────────────────────────────────────────────
    // ---- Events ------------------------------------------------
    $app->get('/events', [EventController::class, 'index'])
    ->setName('events.index');

    $app->get('/events/search', [EventController::class, 'searchJson'])
    ->setName('events.search');

    $app->get('/events/create', [EventController::class, 'create'])
    ->setName('events.create');

    $app->post('/events', [EventController::class, 'store'])
    ->setName('events.store');

    $app->get('/events/{id}', [EventController::class, 'show'])
    ->setName('events.show');
    // ── Cart ────────────────────────────────────────────────────────────────
    $app->get('/cart', [CartController::class, 'index'])->setName('cart.index');

    // ── Auth ────────────────────────────────────────────────────────────────
    $app->get('/login',    [AuthController::class, 'showLogin'])->setName('auth.login');
    $app->post('/login',   [AuthController::class, 'login'])->setName('auth.login.post');

    $app->get('/register', [AuthController::class, 'showRegister'])->setName('auth.register');
    $app->post('/register',[AuthController::class, 'register'])->setName('auth.register.post');

    $app->get('/logout',   [AuthController::class, 'logout'])->setName('auth.logout');

    // ── Dev utilities ───────────────────────────────────────────────────────
    $app->get('/phpinfo', function (Request $request, Response $response, $args) {
        ob_start();
        phpinfo();
        $response->getBody()->write(ob_get_clean());
        return $response;
    });

    $app->get('/error', function (Request $request, Response $response, $args) {
        throw new \Slim\Exception\HttpBadRequestException($request, 'This is a runtime error.');
    });
};