<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\UserService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userService = $container->get(UserService::class);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function showLogin(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'home.index');
        }

        return $this->render($response, 'auth/login.php', [
            'page_title' => 'Sign In — Mix Max',
            'errors'     => $_SESSION['flash_errors'] ?? [],
            'old'        => $_SESSION['flash_old'] ?? [],
        ]);
    }

    public function login(Request $request, Response $response): Response
    {
        // Clear flash
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        $body  = $request->getParsedBody();
        $email = trim($body['email'] ?? '');
        $pass  = $body['password'] ?? '';

        // Basic validation
        $errors = [];
        if (empty($email))  $errors['email']    = 'Email is required.';
        if (empty($pass))   $errors['password'] = 'Password is required.';

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old']    = ['email' => $email];
            return $this->redirect($request, $response, 'auth.login');
        }

        $user = $this->userService->login($email, $pass);

        if ($user === false) {
            $_SESSION['flash_errors'] = ['general' => 'Invalid email or password.'];
            $_SESSION['flash_old']    = ['email' => $email];
            return $this->redirect($request, $response, 'auth.login');
        }

        // Store user in session
        $_SESSION['user']           = $user;
        $_SESSION['cart_count']     = count($_SESSION['cart'] ?? []);
        $_SESSION['flash_success']  = 'Welcome back, ' . htmlspecialchars($user['username']) . '!';

        return $this->redirect($request, $response, 'home.index');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function showRegister(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'home.index');
        }

        return $this->render($response, 'auth/register.php', [
            'page_title' => 'Create Account — Mix Max',
            'errors'     => $_SESSION['flash_errors'] ?? [],
            'old'        => $_SESSION['flash_old'] ?? [],
        ]);
    }

    public function register(Request $request, Response $response): Response
    {
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        $body     = $request->getParsedBody();
        $username = trim($body['username'] ?? '');
        $email    = trim($body['email'] ?? '');
        $pass     = $body['password'] ?? '';
        $confirm  = $body['confirm_password'] ?? '';

        $errors = [];
        if (empty($username))               $errors['username'] = 'Username is required.';
        if (empty($email))                  $errors['email']    = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
                                            $errors['email']    = 'Please enter a valid email.';
        if (strlen($pass) < 8)             $errors['password'] = 'Password must be at least 8 characters.';
        if ($pass !== $confirm)            $errors['confirm_password'] = 'Passwords do not match.';

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old']    = ['username' => $username, 'email' => $email];
            return $this->redirect($request, $response, 'auth.register');
        }

        $result = $this->userService->Register($username, $email, $pass);

        if ($result === false) {
            $_SESSION['flash_errors'] = ['general' => 'That email or username is already registered.'];
            $_SESSION['flash_old']    = ['username' => $username, 'email' => $email];
            return $this->redirect($request, $response, 'auth.register');
        }

        $_SESSION['flash_success'] = 'Account created! Please sign in.';
        return $this->redirect($request, $response, 'auth.login');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request, Response $response): Response
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        return $this->redirect($request, $response, 'home.index');
    }
}