<?php

namespace app\repositories;

use app\entities\UserEntity as User;

/**
 * Interface IUserRepository
 *
 * Defines the contract for User repository classes.
 */
interface IUserRepository {
    public function signup(User $user): bool;
    public function signin(User $user): bool;
    public function getUserByEmail(string $email);
    public function checkSign(): bool;
    public function signout(): void;
}
