<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return static function (Slim\App $app): void {

    // ── Home ─────────────────────────────────────────────────────────────────
    $app->get('/',     [HomeController::class, 'index'])->setName('home.index');
    $app->get('/home', [HomeController::class, 'index']);

    // ── Events ───────────────────────────────────────────────────────────────
    // IMPORTANT: static routes (/search, /create) MUST be registered before
    // the variable route (/events/{id}), otherwise FastRoute throws a shadow error.
    $app->get('/events',         [EventController::class, 'index'])->setName('events.index');
    $app->get('/events/search',  [EventController::class, 'searchJson'])->setName('events.search');
    $app->get('/events/create',  [EventController::class, 'create'])->setName('events.create');
    $app->post('/events',        [EventController::class, 'store'])->setName('events.store');
    $app->get('/events/{id}',    [EventController::class, 'show'])->setName('events.show');

    // ── Cart ─────────────────────────────────────────────────────────────────
    $app->get('/cart',      [CartController::class, 'index'])->setName('cart.index');
    $app->post('/cart/add', [CartController::class, 'add'])->setName('cart.add');

    // ── Auth: Login ──────────────────────────────────────────────────────────
    $app->get('/login',  [AuthController::class, 'showLogin'])->setName('auth.login');
    $app->post('/login', [AuthController::class, 'login'])->setName('auth.login.post');

    // ── Auth: OTP Verify ─────────────────────────────────────────────────────
    $app->get('/verify',     [AuthController::class, 'showVerify'])->setName('auth.verify');
    $app->post('/verify',    [AuthController::class, 'verify'])->setName('auth.verify.post');
    $app->get('/resend-otp', [AuthController::class, 'resendOtp'])->setName('auth.resend');

    // ── Auth: Register ───────────────────────────────────────────────────────
    $app->get('/register',  [AuthController::class, 'showRegister'])->setName('auth.register');
    $app->post('/register', [AuthController::class, 'register'])->setName('auth.register.post');

    // ── Auth: Logout ──────────────────────────────────────────────────────────
    $app->get('/logout', [AuthController::class, 'logout'])->setName('auth.logout');

    // ── User Profile ─────────────────────────────────────────────────────────
    $app->get('/profile',        [UserController::class, 'profile'])->setName('user.profile');
    $app->post('/profile/update',[UserController::class, 'updateProfile'])->setName('user.profile.update');

    // ── Admin Panel ───────────────────────────────────────────────────────────
    $app->get('/admin',          [AdminController::class, 'dashboard'])->setName('admin.dashboard');
    $app->get('/admin/users',    [AdminController::class, 'users'])->setName('admin.users');
    $app->get('/admin/events',   [AdminController::class, 'events'])->setName('admin.events');
    $app->get('/admin/tickets',  [AdminController::class, 'tickets'])->setName('admin.tickets');
    $app->get('/admin/bookings', [AdminController::class, 'bookings'])->setName('admin.bookings');

    $app->get('/admin/users/{id}/promote', [AdminController::class, 'promoteUser'])->setName('admin.users.promote');
    $app->get('/admin/users/{id}/demote',  [AdminController::class, 'demoteUser'])->setName('admin.users.demote');
    $app->get('/admin/users/{id}/delete',  [AdminController::class, 'deleteUser'])->setName('admin.users.delete');

    $app->get('/admin/events/{id}/approve', [AdminController::class, 'approveEvent'])->setName('admin.events.approve');
    $app->get('/admin/events/{id}/reject',  [AdminController::class, 'rejectEvent'])->setName('admin.events.reject');
    $app->get('/admin/events/{id}/delete',  [AdminController::class, 'deleteEvent'])->setName('admin.events.delete');

    $app->get('/admin/tickets/{id}/delete', [AdminController::class, 'deleteTicket'])->setName('admin.tickets.delete');

    // ── Dev utilities ─────────────────────────────────────────────────────────
    $app->get('/phpinfo', function (Request $request, Response $response) {
        ob_start(); phpinfo();
        $response->getBody()->write(ob_get_clean());
        return $response;
    });

    $app->get('/error', function (Request $request, Response $response) {
        throw new \Slim\Exception\HttpBadRequestException($request, 'Runtime error test.');
    });
};