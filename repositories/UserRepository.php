<?php

namespace app\repositories;

use app\aura\Repository;
use app\entities\UserEntity as User;
use app\aura\https\Redirect;
use app\aura\database\DataBaseConnect;

require_once 'interfaces/repositories/IUserRepository.php';

class UserRepository extends Repository implements IUserRepository
{
    /**
     * UserRepository constructor.
     * Initializes the repository for the 'users' table.
     */
    public function __construct() {
        parent::__construct('users');
    }

    /**
     * Registers a new user by inserting their data into the database.
     *
     * @param User $user The user entity containing registration data.
     * @return bool True on success, false on failure.
     */
    public function signup(User $user): bool {
        $result = false;

        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
        ];

        try {
            $result = $this->create($data);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log($e, 3, '/log/error.log');
        } finally {
            return $result;
        }
    }
    
    /**
     * Authenticates a user based on email and password.
     * Stores user data in session on success.
     *
     * @param User $user The user entity containing login credentials.
     * @return bool True if authentication succeeds, false otherwise.
     */
    public function signin(User $user): bool {
        $result = false;

        $email = $user->getEmail();
        $password = $user->getPassword();

        $user_data = $this->getUserByEmail($email);

        if (!$user_data) {
            $_SESSION['msg'] = 'E-mail does not match.';
            return $result;
        }

        if (password_verify($password, $user_data['password'])) {
            session_regenerate_id(true);
            $_SESSION['signin_user'] = $user_data;
            $result = true;
        } else {
            $_SESSION['msg'] = 'Password is incorrect.';
        }

        return $result;
    }

    /**
     * Retrieves user data by email.
     *
     * @param string $email The email address to search for.
     * @return array|false Associative array of user data or false if not found.
     */
    public function getUserByEmail(string $email) {
        try {
            return $this->findByColumn('email', $email);  // Repositoryの新メソッドを使用
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
        session_destroy();
        Redirect::to('signin');
    }
}
