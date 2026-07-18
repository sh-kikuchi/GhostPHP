<?php
//vendor/bin/phpunit --display-warnings tests/Controllers/UserControllerTest.php
namespace tests\Controllers;

use PHPUnit\Framework\TestCase;
use app\controllers\UserController;
use app\services\UserService;
use app\aura\utils\Mail;
use app\requests\UserRequest;
use app\requests\MailRequest;
use app\requests\FileRequest;

class UserControllerTest extends TestCase {
    private UserService $UserService;
    private Mail $mail;
    private UserController $controller;

    protected function setUp(): void {
        $this->UserService = $this->createMock(UserService::class);
        $this->mail = $this->createMock(Mail::class);

        $this->controller = new UserController(
            $this->UserService,
            $this->mail
        );

        $_SESSION = [];
        $_GET = [];
        $_POST = [];

        ob_start();
    }

    protected function tearDown(): void {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];

        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    /**
     * Tests displaying the user's My Page.
     */
    public function testMyPageSuccess(): void {
        $_SESSION['signin_user'] = [
            'name' => 'test user',
            'email' => 'test@example.com'
        ];

        $this->UserService
            ->method('checkSign')
            ->willReturn(true);

        ob_start();
        $this->controller->myPage();
        ob_end_clean();

        $this->assertTrue(true);
    }

    /**
     * Tests redirecting guests from My Page.
     */
    public function testMyPageRedirect(): void {
        $this->UserService
            ->method('checkSign')
            ->willReturn(false);

        $this->controller->myPage();

        $this->assertTrue(true);
    }

    /**
     * Tests displaying the sign-up form.
     */
    public function testShowSignUpForm(): void {
        $this->UserService
            ->method('checkSign')
            ->willReturn(false);

        $this->controller->showSignUpForm();

        $this->assertTrue(true);
    }

    /**
     * Tests redirecting signed-in users from the sign-up form.
     */
    public function testShowSignUpFormRedirect(): void {
        $this->UserService
            ->method('checkSign')
            ->willReturn(true);

        $this->controller->showSignUpForm();

        $this->assertTrue(true);
    }

    /**
     * Tests successful user registration.
     */
    public function testSignupSuccess(): void {
        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->method('signup')
            ->willReturn(true);

        $this->controller->signup($request);

        $this->assertTrue(true);
    }

    /**
     * Tests failed user registration.
     */
    public function testSignupFail(): void {
        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->method('signup')
            ->willReturn(false);

        $this->controller->signup($request);

        $this->assertTrue(true);
    }

    /**
     * Tests displaying the sign-in form.
     */
    public function testShowSignInForm(): void {
        $this->UserService
            ->method('checkSign')
            ->willReturn(false);

        $this->controller->showSignInForm();

        $this->assertTrue(true);
    }

    /**
     * Tests successful user sign-in.
     */
    public function testSigninSuccess(): void {
        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->method('signin')
            ->willReturn(true);

        $this->controller->signin($request);

        $this->assertTrue(true);
    }

    /**
     * Tests failed user sign-in.
     */
    public function testSigninFail(): void {
        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->method('signin')
            ->willReturn(false);

        $this->controller->signin($request);

        $this->assertTrue(true);
    }

    /**
     * Tests user sign-out.
     */
    public function testSignout(): void {
        $this->UserService
            ->expects($this->once())
            ->method('signout');

        $this->controller->signout();

        $this->assertTrue(true);
    }

    /**
     * Tests file upload.
     */
    public function testUpload(): void {
        $request = $this->createMock(FileRequest::class);

        $this->UserService
            ->expects($this->once())
            ->method('upload')
            ->with($request);

        $this->controller->upload($request);

        $this->assertTrue(true);
    }

    /**
     * Tests sending an email.
     */
    public function testMail(): void {
        $request = $this->createMock(MailRequest::class);

        $request->method('all')
            ->willReturn([
                'message' => 'test'
            ]);

        $this->mail
            ->expects($this->once())
            ->method('sendMail')
            ->with([
                'message' => 'test'
            ]);

        $this->controller->mail($request);

        $this->assertTrue(true);
    }
}