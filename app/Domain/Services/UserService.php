<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\UserModel;
use App\Helpers\Core\PDOService;

class UserService extends BaseService
{
    private UserModel $userModel;

    public function __construct(PDOService $db_service)
    {
        $this->userModel = new UserModel($db_service);
    }

    /**
     * Register a new user (now includes phone number).
     */
    public function Register(string $username, string $email, string $password, string $phone = ''): bool
    {
        if ($this->userModel->findByEmail($email)) {
            return false;
        }
        if ($this->userModel->findByUsername($username)) {
            return false;
        }
        return $this->userModel->create($username, $email, $password, $phone);
    }

    /**
     * Validate credentials and return the user array (minus password hash),
     * or false on failure.
     */
    public function login(string $email, string $password): array|false
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        unset($user['password'], $user['twoFactor']);
        return $user;
    }

    /**
     * Load a user by ID (used after OTP verification to populate the session).
     */
    public function getUserById(int $userId): array|false
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return false;
        }
        unset($user['password'], $user['twoFactor']);
        return $user;
    }

    public function isAdmin(array $user): bool
    {
        return isset($user['role']) && $user['role'] === 'admin';
    }

    public function updateUser(int $userId, array $data): void
    {
        // TODO: implement
    }

    public function deleteUser(int $userId): void
    {
        // TODO: implement
    }
}