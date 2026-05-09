<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\UserService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends BaseController
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

    public function profile(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $data = [
            'page_title' => 'My Profile — Mix Max',
            'user'       => $_SESSION['user'],
            'errors'     => $_SESSION['flash_errors'] ?? [],
            'success'    => $_SESSION['flash_success'] ?? '',
            'old'        => $_SESSION['flash_old'] ?? [],
        ];
        unset($_SESSION['flash_errors'], $_SESSION['flash_success'], $_SESSION['flash_old']);

        return $this->render($response, 'profile/index.php', $data);
    }

    public function updateProfile(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $userId = (int) $_SESSION['user']['userId'];
        $body   = $request->getParsedBody();
        $section = $body['section'] ?? 'info';

        if ($section === 'info') {
            return $this->handleInfoUpdate($request, $response, $userId, $body);
        }

        if ($section === 'password') {
            return $this->handlePasswordUpdate($request, $response, $userId, $body);
        }

        return $this->redirect($request, $response, 'user.profile');
    }

    private function handleInfoUpdate(Request $request, Response $response, int $userId, array $body): Response
    {
        $username = trim($body['username'] ?? '');
        $email    = trim($body['email']    ?? '');
        $phone    = trim($body['phone']    ?? '');

        $errors = [];
        if (empty($username)) $errors['username'] = 'Username is required.';
        if (empty($email))    $errors['email']    = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Please enter a valid email address.';
        if (!empty($phone) && !preg_match('/^\+[1-9]\d{7,14}$/', $phone))
            $errors['phone'] = 'Use E.164 format, e.g. +15141234567';

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old']    = compact('username', 'email', 'phone');
            return $this->redirect($request, $response, 'user.profile');
        }

        // Check if email/username already taken by someone else
        $existingByEmail    = $this->userService->findByEmail($email);
        $existingByUsername = $this->userService->findByUsername($username);

        if ($existingByEmail && (int)$existingByEmail['userId'] !== $userId) {
            $_SESSION['flash_errors'] = ['email' => 'That email is already in use.'];
            $_SESSION['flash_old']    = compact('username', 'email', 'phone');
            return $this->redirect($request, $response, 'user.profile');
        }

        if ($existingByUsername && (int)$existingByUsername['userId'] !== $userId) {
            $_SESSION['flash_errors'] = ['username' => 'That username is already taken.'];
            $_SESSION['flash_old']    = compact('username', 'email', 'phone');
            return $this->redirect($request, $response, 'user.profile');
        }

        $this->userService->updateInfo($userId, $username, $email, $phone);

        // Refresh session
        $refreshed = $this->userService->getUserById($userId);
        if ($refreshed) {
            $_SESSION['user'] = $refreshed;
        }

        $_SESSION['flash_success'] = 'Profile updated successfully.';
        return $this->redirect($request, $response, 'user.profile');
    }

    private function handlePasswordUpdate(Request $request, Response $response, int $userId, array $body): Response
    {
        $current = $body['current_password'] ?? '';
        $new     = $body['new_password']     ?? '';
        $confirm = $body['confirm_password'] ?? '';

        $errors = [];
        if (empty($current)) $errors['current_password'] = 'Current password is required.';
        if (strlen($new) < 8) $errors['new_password']    = 'New password must be at least 8 characters.';
        if ($new !== $confirm) $errors['confirm_password'] = 'Passwords do not match.';

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            return $this->redirect($request, $response, 'user.profile');
        }

        $result = $this->userService->changePassword($userId, $current, $new);

        if (!$result) {
            $_SESSION['flash_errors'] = ['current_password' => 'Current password is incorrect.'];
            return $this->redirect($request, $response, 'user.profile');
        }

        $_SESSION['flash_success'] = 'Password changed successfully.';
        return $this->redirect($request, $response, 'user.profile');
    }
}