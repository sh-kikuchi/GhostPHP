<?php

namespace app\repositories;

use PDO;
use app\aura\Repository;
use app\entities\UserEntity as User;
use app\aura\https\Redirect;

require_once 'interfaces/repositories/IUserRepository.php';

class UserRepository extends Repository implements IUserRepository {
    /**
     * UserRepository constructor.
     * Initializes the repository for the 'users' table.
     */
    public function __construct(?PDO $pdo = null){
        parent::__construct('users', $pdo);
    }

    /**
     * Registers a new user by inserting their data into the database.
     *
     * @param User $user The user entity containing registration data.
     * @return bool True on success, false on failure.
     * @throws \PDOException If the insert fails (error handling and rollback are done by the Service).
     */
    public function signup(User $user): bool {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
        ];

        return $this->create($data);
    }
    
    /**
     * Authenticates a user based on email and password.
     * Stores user data in session on success.
     * The failure message is set by the Controller, so the reason (email or password) is not exposed here.
     *
     * @param User $user The user entity containing login credentials.
     * @return bool True if authentication succeeds, false otherwise.
     */
    public function signin(User $user): bool {
        $user_data = $this->getUserByEmail($user->getEmail());

        if (!$user_data || !password_verify($user->getPassword(), $user_data['password'])) {
            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION['signin_user'] = $user_data;
        return true;
    }

    /**
     * Retrieves user data by email.
     *
     * @param string $email The email address to search for.
     * @return array|false Associative array of user data or false if not found.
     */
    public function getUserByEmail(string $email) {
        try {
            return $this->findByColumn('email', $email);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Checks whether a user is currently signed in.
     *
     * @return bool True if a user session exists, false otherwise.
     */
    public function checkSign(): bool {
        return isset($_SESSION['signin_user']) && $_SESSION['signin_user']['id'] > 0;
    }

    /**
     * Logs the user out by clearing the session and redirecting to the sign-in page.
     *
     * @return void
     */
    public function signout(): void {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        if (!headers_sent()) {
            Redirect::to('signin');
        }
    }
}
