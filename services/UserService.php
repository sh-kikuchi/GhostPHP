<?php

namespace app\services;

use app\aura\Service;
use app\aura\utils\File;
use app\aura\utils\Mail;
use app\aura\utils\Session;
use app\entities\UserEntity as User;
use app\repositories\UserRepository;
use app\requests\UserRequest;
use app\requests\FileRequest;

/**
 * Class UserService
 *
 * Handles user-related business logic.
 *
 * @package app\services
 */
class UserService extends Service {
    /**
     * UserService constructor.
     *
     * @param UserRepository $user_repository
     * @param File $file
     * @param Mail $mail
     * @param Session $session
     */
    public function __construct(
        private UserRepository $user_repository,
        private File $file,
        private Mail $mail,
        private Session $session
    ) {
        parent::__construct();
    }

    /**
     * Check whether a user is signed in.
     *
     * @return bool
     */
    public function checkSign(): bool {
        return $this->user_repository->checkSign();
    }

    /**
     * Register a new user.
     *
     * @param UserRequest $user_request
     * @return bool
     */
    public function signup(UserRequest $user_request): bool {
        $this->beginTransaction();

        try {
            $user = $this->makeUser($user_request);

            if (!$this->user_repository->signup($user)) {
                throw new \Exception('Signup failed.');
            }

            $this->commit();
            return true;

        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Authenticate a user.
     *
     * @param UserRequest $user_request
     * @return bool
     */
    public function signin(UserRequest $user_request): bool {
        $user = $this->makeUser($user_request);

        return $this->user_repository->signin($user);
    }

    /**
     * Sign out the current user.
     *
     * @return void
     */
    public function signout(): void {
        $this->user_repository->signout();
    }

    /**
     * Upload a file.
     *
     * @param FileRequest $file_request
     * @return void
     */
    public function upload(FileRequest $file_request): void {
        $this->file->uploadFile(
            $file_request->files()
        );
    }

    public function mail(array $data): void  {
        $this->mail->sendMail($data);
    }

    private function makeUser(UserRequest $user_request): User {
        $user = new User();

        if (!empty($user_request->input('id'))) {
            $user->setId($user_request->input('id'));
        }

        if (!empty($user_request->input('name'))) {
            $user->setName($user_request->input('name'));
        }

        $user->setEmail($user_request->input('email'));
        $user->setPassword($user_request->input('password'));

        return $user;
    }
}