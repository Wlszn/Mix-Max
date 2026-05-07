<?php

declare(strict_types=1);

namespace App\Domain\Models;

class UserModel extends BaseModel
{
    public function findById(int $id): array|false
    {
        return $this->selectOne('SELECT * FROM users WHERE userId = ?', [$id]);
    }

    public function findByUsername(string $username): array|false
    {
        return $this->selectOne('SELECT * FROM users WHERE username = ?', [$username]);
    }

    public function findByEmail(string $email): array|false
    {
        return $this->selectOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    /**
     * Create a new user. stores the phone number (E.164 format).
     */
    public function create(string $username, string $email, string $password, string $phone = ''): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return (bool) $this->execute(
            'INSERT INTO users (username, email, password, twoFactor, role, phone)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$username, $email, $passwordHash, '', 'user', $phone]
        );
    }

    public function update(int $id, string $username, string $email, string $password): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return (bool) $this->execute(
            'UPDATE users SET username = ?, email = ?, password = ? WHERE userId = ?',
            [$username, $email, $passwordHash, $id]
        );
    }

    public function updatePhone(int $id, string $phone): bool
    {
        return (bool) $this->execute(
            'UPDATE users SET phone = ? WHERE userId = ?',
            [$phone, $id]
        );
    }

    public function delete(int $id): bool
    {
        return (bool) $this->execute('DELETE FROM users WHERE userId = ?', [$id]);
    }

    public function promoteUser(int $id): bool
    {
        return (bool) $this->execute(
            'UPDATE users SET role = ? WHERE userId = ?',
            ['admin', $id]
        );
    }
}