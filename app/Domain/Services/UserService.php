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
     * Register a new user (includes phone number).
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
     * Password and twoFactor fields are stripped.
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

    // ─── Profile management ────────────────────────────────────────────────────

    /**
     * Verify a user's current (plain-text) password against the stored hash.
     */
    public function verifyCurrentPassword(int $userId, string $plainPassword): bool
    {
        // We need the raw row including the hash — bypass the stripped version
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return false;
        }
        return password_verify($plainPassword, $user['password']);
    }

    /**
     * Update a user's username, email, and phone.
     * Duplicate-detection: if the new email/username already belongs to a
     * *different* user we return false so the controller can show an error.
     */
    public function updateProfile(int $userId, string $username, string $email, string $phone): bool
    {
        // Check email uniqueness (ignore current user)
        $byEmail = $this->userModel->findByEmail($email);
        if ($byEmail && (int) $byEmail['userId'] !== $userId) {
            return false;
        }

        // Check username uniqueness (ignore current user)
        $byUsername = $this->userModel->findByUsername($username);
        if ($byUsername && (int) $byUsername['userId'] !== $userId) {
            return false;
        }

        return $this->userModel->updateProfile($userId, $username, $email, $phone);
    }

    /**
     * Hash and store a new password for the given user.
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        return $this->userModel->updatePassword($userId, $newPassword);
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