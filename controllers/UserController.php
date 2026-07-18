<?php

namespace app\controllers;

use app\aura\Controller;
use app\aura\Template;
use app\aura\https\Redirect;
use app\aura\utils\Mail;
use app\requests\FileRequest;
use app\requests\UserRequest;
use app\requests\MailRequest;
use app\services\UserService;

/**
 * Class UserController
 *
 * Handles HTTP requests related to users.
 *
 * @package app\controllers
 */
class UserController extends Controller
{
    private UserService $home_service;
    private Mail $mail;

    /**
     * UserController constructor.
     *
     * @param UserService $UserService
     * @param Mail $mail
     */
    public function __construct(
        UserService $UserService,
        Mail $mail) {
        parent::__construct();

        $this->home_service = $UserService;
        $this->mail = $mail;
    }

    /**
     * Display user page.
     *
     * @return void
     */
    public function myPage(): void {
        if (!$this->home_service->checkSign()) {
            Redirect::to('signin');
            return;
        }

        $template = new Template(
            'user/index',
            [
                'errors' => $_SESSION['ERROR_MESSAGES'] ?? null,
                'signin_user' => $_SESSION['signin_user']
            ]
        );

        $template->render();
    }

    /**
     * Display sign up form.
     *
     * @return void
     */
    public function showSignUpForm(): void {
        if ($this->home_service->checkSign()) {
            Redirect::to('index');
            return;
        }

        $template = new Template(
            'user/form',
            [
                'csrf' => $this->setToken('signup'),
                'errors' => $_SESSION['ERROR_MESSAGES'] ?? null,
                'old' => $_SESSION['old'] ?? null,
                'form_name' => 'signup'
            ]
        );

        unset(
            $_SESSION['ERROR_MESSAGES'],
            $_SESSION['old']
        );

        $template->render();
    }

    /**
     * Register a new user.
     *
     * @param UserRequest $user_request
     * @return void
     */
    public function signup(UserRequest $user_request): void {
        if (!$this->checkToken('signup')) {
            return;
        }

        if (!$this->home_service->signup($user_request)) {
            Redirect::to('signup');
            return;
        }

        Redirect::to('index');
        return;
    }

    /**
     * Display sign in form.
     *
     * @return void
     */
    public function showSignInForm(): void {
        if ($this->home_service->checkSign()) {
            Redirect::to('index');
            return;
        }

        $template = new Template(
            'user/form',
            [
                'csrf' => $this->setToken('signin'),
                'errors' => $_SESSION['ERROR_MESSAGES'] ?? null,
                'old' => $_SESSION['old'] ?? null,
                'form_name' => 'signin'
            ]
        );

        unset(
            $_SESSION['ERROR_MESSAGES'],
            $_SESSION['old']
        );

        $template->render();
    }

    /**
     * Authenticate a user.
     *
     * @param UserRequest $user_request
     * @return void
     */
    public function signin(UserRequest $user_request): void {
        $result = $this->home_service->signin($user_request);
        if (!$result) {
            Redirect::to('signin');
            return;
        }

        Redirect::to('index');
    }

    /**
     * Sign out the current user.
     *
     * @return void
     */
    public function signout(): void {
        $this->home_service->signout();
        Redirect::to('index');
        return;
    }

    /**
     * Upload a file.
     *
     * @param FileRequest $file_request
     * @return void
     */
    public function upload(FileRequest $file_request): void {
        $this->home_service->upload($file_request);
        Redirect::to('index');
        return;
    }

    /**
     * Send an email.
     *
     * @param MailRequest $mail_request
     * @return void
     */
    public function mail(MailRequest $mail_request): void {
        $this->mail->sendMail($mail_request->all());
    }
}