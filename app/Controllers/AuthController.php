<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\TwilioVerifyService;
use App\Domain\Services\UserService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class AuthController extends BaseController
{
    private UserService $userService;
    private TwilioVerifyService $twilioService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userService = $container->get(UserService::class);
        $this->twilioService = $container->get(TwilioVerifyService::class);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ─── Show Login Form ──────────────────────────────────────────────────────

    public function showLogin(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'home.index');
        }

        $data = [
            'page_title' => 'Sign In — Mix Max',
            'errors' => $_SESSION['flash_errors'] ?? [],
            'old' => $_SESSION['flash_old'] ?? [],
        ];
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        return $this->render($response, 'auth/login.php', $data);
    }

    // ─── Handle Login POST ────────────────────────────────────────────────────
    // Step 1: validate credentials → send OTP → redirect to verify page

    public function login(Request $request, Response $response): Response
    {
        unset($_SESSION['flash_errors'], $_SESSION['flash_old'], $_SESSION['otp_user_id']);

        $body = $request->getParsedBody();
        $email = trim($body['email'] ?? '');
        $pass = $body['password'] ?? '';

        $errors = [];
        if (empty($email))
            $errors['email'] = 'Email is required.';
        if (empty($pass))
            $errors['password'] = 'Password is required.';

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old'] = ['email' => $email];
            return $this->redirect($request, $response, 'auth.login');
        }

        $user = $this->userService->login($email, $pass);

        if ($user === false) {
            $_SESSION['flash_errors'] = ['general' => 'Invalid email or password.'];
            $_SESSION['flash_old'] = ['email' => $email];
            return $this->redirect($request, $response, 'auth.login');
        }
        // ---------------------   Check if user has a phone number on file for OTP

        if (empty($user['phone'])) {
            $_SESSION['flash_errors'] = [
                'general' => 'No phone number on file. Please contact support or re-register.'
            ];
            $_SESSION['flash_old'] = ['email' => $email];
            return $this->redirect($request, $response, 'auth.login');
        }

        try {
            $sent = $this->twilioService->sendOtp($user['phone']);
        } catch (RuntimeException $e) {
            $_SESSION['flash_errors'] = ['general' => 'Could not send verification code. Please try again.'];
            return $this->redirect($request, $response, 'auth.login');
        }

        if (!$sent) {
            $_SESSION['flash_errors'] = ['general' => 'Could not send verification code. Please try again.'];
            return $this->redirect($request, $response, 'auth.login');
        }

        //----------------- comment the section here to disable the OTP to save credits

        //Store pending OTP state — user is NOT logged in yet

        $_SESSION['otp_user_id'] = $user['userId'];
        $_SESSION['otp_phone'] = $user['phone'];
        $_SESSION['otp_sent_at'] = time();
        return $this->redirect($request, $response, 'auth.verify');

        // ---------------------   If you want to disable OTP for testing, just log the user in directly here:

        // $_SESSION['user']          = $user;
        // $_SESSION['cart_count']    = count($_SESSION['cart'] ?? []);
        // $_SESSION['flash_success'] = 'Welcome back, ' . htmlspecialchars($user['username']) . '!';
        // return $this->redirect($request, $response, 'home.index');

        // ---------------------   testing on here comment and uncomment the section above to enable OTP again

    }

    // ─── Show OTP Verify Form ─────────────────────────────────────────────────

    public function showVerify(Request $request, Response $response): Response
    {
        if (empty($_SESSION['otp_user_id'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        // Expire after 10 minutes
        if ((time() - ($_SESSION['otp_sent_at'] ?? 0)) > 600) {
            unset($_SESSION['otp_user_id'], $_SESSION['otp_phone'], $_SESSION['otp_sent_at']);
            $_SESSION['flash_errors'] = ['general' => 'Session expired. Please sign in again.'];
            return $this->redirect($request, $response, 'auth.login');
        }

        $data = [
            'page_title' => 'Enter Verification Code — Mix Max',
            'masked_phone' => $this->maskPhone($_SESSION['otp_phone'] ?? ''),
            'seconds_left' => max(0, 30 - (time() - ($_SESSION['otp_sent_at'] ?? 0))),
            'errors' => $_SESSION['flash_errors'] ?? [],
            'success' => $_SESSION['flash_success'] ?? '',
        ];
        unset($_SESSION['flash_errors'], $_SESSION['flash_success']);

        return $this->render($response, 'auth/verify.php', $data);
    }

    // ─── Handle OTP Verify POST ───────────────────────────────────────────────

    public function verify(Request $request, Response $response): Response
    {
        if (empty($_SESSION['otp_user_id'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $code = trim($request->getParsedBody()['code'] ?? '');

        if (empty($code)) {
            $_SESSION['flash_errors'] = ['code' => 'Please enter the verification code.'];
            return $this->redirect($request, $response, 'auth.verify');
        }

        try {
            $valid = $this->twilioService->verifyOtp($_SESSION['otp_phone'], $code);
        } catch (RuntimeException $e) {
            $_SESSION['flash_errors'] = ['general' => 'Verification failed. Please try again.'];
            return $this->redirect($request, $response, 'auth.verify');
        }

        if (!$valid) {
            $_SESSION['flash_errors'] = ['code' => 'Incorrect or expired code. Please try again.'];
            return $this->redirect($request, $response, 'auth.verify');
        }

        // OTP approved — load user and start real session
        $user = $this->userService->getUserById((int) $_SESSION['otp_user_id']);
        unset($_SESSION['otp_user_id'], $_SESSION['otp_phone'], $_SESSION['otp_sent_at']);

        if (!$user) {
            $_SESSION['flash_errors'] = ['general' => 'User not found. Please sign in again.'];
            return $this->redirect($request, $response, 'auth.login');
        }

        $_SESSION['user'] = $user;
        $_SESSION['cart_count'] = count($_SESSION['cart'] ?? []);
        $_SESSION['flash_success'] = 'Welcome back, ' . htmlspecialchars($user['username']) . '!';

        return $this->redirect($request, $response, 'home.index');
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────

    public function resendOtp(Request $request, Response $response): Response
    {
        if (empty($_SESSION['otp_user_id']) || empty($_SESSION['otp_phone'])) {
            return $this->redirect($request, $response, 'auth.login');
        }

        // Rate-limit: 30 seconds between resends
        if ((time() - ($_SESSION['otp_sent_at'] ?? 0)) < 30) {
            $_SESSION['flash_errors'] = ['general' => 'Please wait before requesting a new code.'];
            return $this->redirect($request, $response, 'auth.verify');
        }

        try {
            $this->twilioService->sendOtp($_SESSION['otp_phone']);
            $_SESSION['otp_sent_at'] = time();
            $_SESSION['flash_success'] = 'A new code has been sent to your phone.';
        } catch (RuntimeException $e) {
            $_SESSION['flash_errors'] = ['general' => 'Could not resend code. Please try again.'];
        }

        return $this->redirect($request, $response, 'auth.verify');
    }

    // ─── Show Register Form ───────────────────────────────────────────────────

    public function showRegister(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['user'])) {
            return $this->redirect($request, $response, 'home.index');
        }

        $data = [
            'page_title' => 'Create Account — Mix Max',
            'errors' => $_SESSION['flash_errors'] ?? [],
            'old' => $_SESSION['flash_old'] ?? [],
        ];
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        return $this->render($response, 'auth/register.php', $data);
    }

    // ─── Handle Register POST ─────────────────────────────────────────────────

    public function register(Request $request, Response $response): Response
    {
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        $body = $request->getParsedBody();
        $username = trim($body['username'] ?? '');
        $email = trim($body['email'] ?? '');
        $phone = trim($body['phone'] ?? '');
        $pass = $body['password'] ?? '';
        $confirm = $body['confirm_password'] ?? '';

        $errors = [];
        if (empty($username))
            $errors['username'] = 'Username is required.';
        if (empty($email))
            $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Please enter a valid email address.';
        if (empty($phone))
            $errors['phone'] = 'Phone number is required.';
        elseif (!preg_match('/^\+[1-9]\d{7,14}$/', $phone))
            $errors['phone'] = 'Use E.164 format, e.g. +15141234567';
        if (strlen($pass) < 8)
            $errors['password'] = 'Password must be at least 8 characters.';
        if ($pass !== $confirm)
            $errors['confirm_password'] = 'Passwords do not match.';

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old'] = compact('username', 'email', 'phone');
            return $this->redirect($request, $response, 'auth.register');
        }

        $result = $this->userService->Register($username, $email, $pass, $phone);

        if ($result === false) {
            $_SESSION['flash_errors'] = ['general' => 'That email or username is already registered.'];
            $_SESSION['flash_old'] = compact('username', 'email', 'phone');
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
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        return $this->redirect($request, $response, 'home.index');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) < 7)
            return $phone;
        return substr($phone, 0, 5) . '***' . substr($phone, -4);
    }

    public function adminManageUsers(Request $request, Response $response): Response
    {
        $users = $this->userService->getAllUsers();

        return $this->render($response, 'admin/users.php', [
            'page_title' => 'Manage Users',
            'users' => $users
        ]);
    }

    public function adminDeleteUser(Request $request, Response $response, array $args): Response
    {
        $this->userService->deleteUser((int) $args['id']);

        return $this->redirect($request, $response, 'admin.users');
    }

    public function adminUpdateUserRole(Request $request, Response $response, array $args): Response
    {
        $role = $request->getParsedBody()['role'] ?? 'user';

        if (!in_array($role, ['user', 'admin'], true)) {
            $role = 'user';
        }

        $this->userService->updateUserRole((int) $args['id'], $role);

        return $this->redirect($request, $response, 'admin.users');
    }
}