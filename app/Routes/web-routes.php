<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return static function (Slim\App $app): void {

    // ── Home ────────────────────────────────────────────────────────────────
    $app->get('/', [HomeController::class, 'index'])->setName('home.index');
    $app->get('/home', [HomeController::class, 'index']);

    // ── Events ──────────────────────────────────────────────────────────────
    $app->get('/events', [EventController::class, 'index'])->setName('events.index');

    $app->get('/events/create', [EventController::class, 'create'])->setName('events.create');
    $app->post('/events', [EventController::class, 'store'])->setName('events.store');

    // —— Admin Events ────────────────────────────────────────────────
    $app->get('/admin/events', [EventController::class, 'adminPending'])->setName('admin.events');

    $app->post('/admin/events/{id}/approve', [EventController::class, 'approve'])
        ->setName('admin.events.approve');

    $app->post('/admin/events/{id}/reject', [EventController::class, 'reject'])
        ->setName('admin.events.reject');

    $app->get('/events/search', [EventController::class, 'searchJson'])->setName('events.search');
    $app->get('/events/{id}', [EventController::class, 'show'])->setName('events.show');

    // ── Cart ────────────────────────────────────────────────────────────────
    $app->get('/cart', [CartController::class, 'index'])->setName('cart.index');
    $app->post('/cart/add', [CartController::class, 'add'])->setName('cart.add');
    $app->post('/cart/remove', [CartController::class, 'remove'])->setName('cart.remove');
    $app->post('/cart/clear', [CartController::class, 'clear'])->setName('cart.clear');
    $app->get('/cart/payment', [CartController::class, 'payment'])->setName('cart.payment');
    $app->post('/cart/buy', [CartController::class, 'buy'])->setName('cart.buy');
    $app->post('/cart/buy-selected', [CartController::class, 'buySelected'])->setName('cart.buy-selected');

    // ── Profile ─────────────────────────────────────────────────────────────
    $app->get('/profile', [UserController::class, 'showProfile'])->setName('user.profile');
    $app->post('/profile', [UserController::class, 'updateProfile'])->setName('user.profile.update');

    // ── Auth: Login ─────────────────────────────────────────────────────────
    $app->get('/login', [AuthController::class, 'showLogin'])->setName('auth.login');
    $app->post('/login', [AuthController::class, 'login'])->setName('auth.login.post');

    // ── Auth: OTP Verify ────────────────────────────────────────────────────
    $app->get('/verify', [AuthController::class, 'showVerify'])->setName('auth.verify');
    $app->post('/verify', [AuthController::class, 'verify'])->setName('auth.verify.post');
    $app->get('/resend-otp', [AuthController::class, 'resendOtp'])->setName('auth.resend');

    // ── Auth: Register ──────────────────────────────────────────────────────
    $app->get('/register', [AuthController::class, 'showRegister'])->setName('auth.register');
    $app->post('/register', [AuthController::class, 'register'])->setName('auth.register.post');

    // ── Auth: Logout ─────────────────────────────────────────────────────────
    $app->get('/logout', [AuthController::class, 'logout'])->setName('auth.logout');

    // ── Dev utilities ────────────────────────────────────────────────────────
    $app->get('/phpinfo', function (Request $request, Response $response) {
        ob_start();
        phpinfo();
        $response->getBody()->write(ob_get_clean());
        return $response;
    });

    $app->get('/error', function (Request $request, Response $response) {
        throw new \Slim\Exception\HttpBadRequestException($request, 'Runtime error test.');
    });
};