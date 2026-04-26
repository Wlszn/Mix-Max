<?php

namespace App\Domain\Services;

use App\Domain\Models\UserModel;
use App\Helpers\Core\PDOService;

class UserService extends BaseService
{
    private UserModel $userModel;

    public function __construct(PDOService $db_service)
    {
        // Initialize any dependencies or services here
        $this->userModel = new UserModel($db_service);
    }

    public function Register(string $username, string $email, string $password) : bool
    {
        if ($this->userModel->findByEmail($email)) {
            //throw new Exception("Email already in use.");
            return false;
        }

        if ($this->userModel->findByUsername($username)) {
            //throw new Exception("Username already in use.");
            return false;
        }

        return $this->userModel->create($username, $email, $password);

    }

    // this returns the user array if it works, or false if it fails
    public function login(string $email, string $password): array|false
    {
        $user = $this->userModel->findByEmail($email);
 
        if (!$user) {
            return false;
        }
 
        if (!password_verify($password, $user['password'])) {
            return false;
        }
 
        // Don't expose the password hash to the session
        unset($user['password'], $user['twoFactor']);
 
        return $user;
    }

    public function isAdmin(array $user): bool
    {
        return isset($user['role']) && $user['role'] === 'admin';
    }

    public function updateUser(int $userId, array $data)
    {
        // Logic to update an existing user
        // This could involve checking if the user exists, validating the data, etc.
    }

    public function deleteUser(int $userId)
    {
        // Logic to delete a user
        // This could involve checking if the user exists and then deleting them from the database
    }

}