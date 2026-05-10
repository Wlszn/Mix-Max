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

    // ─── Show Profile Page ────────────────────────────────────────────────────

    public function showProfile(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        // Re-fetch fresh user data from DB so the page always shows current info
        $user = $this->userService->getUserById((int) $_SESSION['user']['userId']);

        if (!$user) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $data = [
            'page_title' => 'My Profile — Mix Max',
            'user'       => $user,
            'errors'     => $_SESSION['flash_errors']  ?? [],
            'success'    => $_SESSION['flash_success'] ?? '',
            'old'        => $_SESSION['flash_old']     ?? [],
        ];
        unset($_SESSION['flash_errors'], $_SESSION['flash_success'], $_SESSION['flash_old']);

        return $this->render($response, 'user/profile.php', $data);
    }

    // ─── Handle Profile Update POST ───────────────────────────────────────────

    public function updateProfile(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $userId = (int) $_SESSION['user']['userId'];
        $body   = $request->getParsedBody();

        $username        = trim($body['username']         ?? '');
        $email           = trim($body['email']            ?? '');
        $phone           = trim($body['phone']            ?? '');
        $currentPassword = $body['current_password']      ?? '';
        $newPassword     = $body['new_password']          ?? '';
        $confirmPassword = $body['confirm_new_password']  ?? '';

        $errors = [];

        // ── Basic field validation ─────────────────────────────────────────
        if (empty($username)) {
            $errors['username'] = 'Username is required.';
        }

        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if (!empty($phone) && !preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            $errors['phone'] = 'Use E.164 format, e.g. +15141234567';
        }

        // ── Password change is optional — only validate if user filled it in ─
        $changingPassword = !empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword);

        if ($changingPassword) {
            if (empty($currentPassword)) {
                $errors['current_password'] = 'Enter your current password to set a new one.';
            }

            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters.';
            }

            if ($newPassword !== $confirmPassword) {
                $errors['confirm_new_password'] = 'Passwords do not match.';
            }

            // Verify current password against DB if no other errors yet
            if (empty($errors['current_password'])) {
                $result = $this->userService->verifyCurrentPassword($userId, $currentPassword);
                if (!$result) {
                    $errors['current_password'] = 'Current password is incorrect.';
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old']    = compact('username', 'email', 'phone');
            return $this->redirect($request, $response, 'user.profile');
        }

        // ── Perform updates ────────────────────────────────────────────────
        $this->userService->updateProfile($userId, $username, $email, $phone);

        if ($changingPassword) {
            $this->userService->updatePassword($userId, $newPassword);
        }

        // ── Refresh session data ───────────────────────────────────────────
        $freshUser = $this->userService->getUserById($userId);
        if ($freshUser) {
            $_SESSION['user'] = $freshUser;
        }

        $_SESSION['flash_success'] = 'Profile updated successfully!';
        return $this->redirect($request, $response, 'user.profile');
    }
}