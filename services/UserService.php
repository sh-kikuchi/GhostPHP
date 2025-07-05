<?php

namespace app\services;

use app\aura\Service;
use app\aura\Template;
use app\aura\https\Redirect;
use app\aura\utils\Session;
use app\aura\utils\File;
use app\aura\utils\Mail;
use app\entities\UserEntity as User;
use app\repositories\UserRepository;
use app\form_classes\UserRequest;

/**
 * Class UserService
 * 
 * Handles business logic related to user management, including sign-up,
 * sign-in, and user profile management.
 *
 * @package app\services
 */
class UserService extends Service implements IUserService {

    /**
     * UserService constructor.
     */
    public function __construct() {
        parent::__construct();
        $session = new Session;
    }

    /**
     * Display the user's personal page.
     *
     * @return string The rendered template.
     */
    public function myPage() {
        $user_repository = new UserRepository();
        $result = $user_repository->checkSign();
        if (!$result) {
            $_SESSION['signin_err'] = 'Please sign in.';
            Redirect::to('signin');
            return;
        }
        
        // Rendering
        $template = new Template(
            'user/index', [
                'signin_user' => $_SESSION['signin_user']
            ]
        );
        return $template->render();
    }

    /**
     * Show the sign-up form.
     *
     * @return string The rendered template.
     */
    public function showSignUpForm() {
        $user_repository = new UserRepository();
        $result = $user_repository->checkSign();
        if ($result) {
            Redirect::to('index');
            exit();
        }

        // Rendering
        $template = new Template(
            'user/form', [
                'csrf' => $this->setToken('signup'),
                'errors' => $_SESSION['errors'] ?? null,
                'old' => $_SESSION['old'] ?? null,
                'form_name' => 'signup'
            ]
        );

        unset($_SESSION['errors'], $_SESSION['old']);

        return $template->render();
    }

    /**
     * Handle the sign-up process.
     *
     * @return bool
     */
    public function signup() {
        // Check token
        if (!$this->checkToken('signup')) {
            echo 'Invalid token.';
            return false;
        }

        // Start transaction
        $this->beginTransaction();

        try {
            // Create an instance
            $user = new UserRepository();
            $user_request = $this->makeUser($_POST, 'signup');

            // Execute methods
            $result = $user->signup($user_request);

            if (!$result) {
                throw new \Exception('Post creation failed.');
            }

            // Commit transaction
            $this->commit();

            // Redirect
            Redirect::to('index');

        } catch (\Exception $e) {
            $this->rollBack();
            Redirect::error(500);  
        }
    }

    /**
     * Show the sign-in form.
     *
     * @return string The rendered template.
     */
    public function showSignInForm() {
        $user_repository = new UserRepository();
        $result = $user_repository->checkSign();
        if ($result) {
            Redirect::to('index');
            return;
        }

        // Rendering
        $template = new Template(
            'user/form', [
                'csrf' => $this->setToken('signin'),
                'errors' => $_SESSION['errors'] ?? null,
                'old' => $_SESSION['old'] ?? null,
                'form_name' => 'signin'
            ]
        );

        unset($_SESSION['errors'], $_SESSION['old']);

        return $template->render();
    }

    /**
     * Handle the sign-in process.
     *
     * @return bool
     */
    public function signin() {
        // Check token
        if (!$this->checkToken('signin')) {
            echo 'Invalid token.';
            return false;
        }

        // Create an instance
        $user = new UserRepository();
        $user_request = $this->makeUser($_POST, 'signin');

        // Execute methods
        $result = $user->signin($user_request);

        // Transitioning screen
        if (!$result) {
            Redirect::to('signin');
        } else {
            Redirect::to('index');
        }
    }

    /**
     * Handle the sign-out process.
     */
    public function signout() {
        // Create an instance
        $user = new UserRepository();

        // Execute methods
        $user->signout();
    }

    /**
     * Handle sending mail.
     */
    public function mail() {
        $mail = new Mail();
        $mail->sendMail($_POST);
    }

    /**
     * Handle file upload.
     *
     * @return void
     */
    public function upload() {
        // Create an instance
        $file = new File();
        $result = $file->uploadFile($_FILES);

        // Transitioning screen
        Redirect::to('index');
        exit();
    }

    /**
     * Create a User entity from the form data.
     *
     * @param array $user_form The form data.
     * @param string $type The type of request (signup or signin).
     * @return User The User entity.
     */
    public function makeUser(array $user_form, string $type):User {
        $user = new User();
        $user_request = new UserRequest($user_form);

        // Validate based on the request type
        switch ($type) {
            case 'signup':
                $user_request->signUpValidation();
                break;
            case 'signin':
                $user_request->signInValidation();
                break;
        }

        if ($user_request->getId() !== null) {
            $user->setId($user_request->getId());
        }
        $user->setName($user_request->getName());
        $user->setEmail($user_request->getEmail());
        $user->setPassword($user_request->getPassword());

        return $user;
    }
}
