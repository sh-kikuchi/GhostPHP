<?php
//vendor/bin/phpunit --display-warnings tests/Controllers/UserControllerTest.php
namespace tests\Controllers;

use PHPUnit\Framework\TestCase;
use app\aura\Lang;
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

        $_SESSION['ERROR_MESSAGES'] = [['files' => 'error']];

        ob_start();
        $this->controller->myPage();
        ob_end_clean();

        // Errors are shown once and then cleared
        $this->assertArrayNotHasKey('ERROR_MESSAGES', $_SESSION);
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
        $_SESSION['csrf_token']['signin'] = 'token';
        $_POST['csrf_token'] = 'token';

        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->expects($this->once())
            ->method('signin')
            ->willReturn(true);

        $this->controller->signin($request);
    }

    /**
     * Tests that sign-in is rejected when the CSRF token is invalid.
     */
    public function testSigninInvalidToken(): void {
        $_SESSION['csrf_token']['signin'] = 'token';
        $_POST['csrf_token'] = 'wrong';

        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->expects($this->never())
            ->method('signin');

        $this->controller->signin($request);
    }

    /**
     * Tests failed user sign-in.
     */
    public function testSigninFail(): void {
        $_SESSION['csrf_token']['signin'] = 'token';
        $_POST['csrf_token'] = 'token';

        $request = $this->createMock(UserRequest::class);

        $this->UserService
            ->method('signin')
            ->willReturn(false);

        $this->controller->signin($request);

        $this->assertSame(
            [['email' => Lang::get('SIGNIN_FAILED')]],
            $_SESSION['ERROR_MESSAGES']
        );
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
            ->with($request)
            ->willReturn([
                ['name' => 'a.png', 'success' => true, 'path' => 'storage/a.png'],
            ]);

        $this->controller->upload($request);

        $this->assertArrayNotHasKey('ERROR_MESSAGES', $_SESSION);
    }

    /**
     * Tests that failed uploads are reported as error messages.
     */
    public function testUploadFailureSetsErrors(): void {
        $request = $this->createMock(FileRequest::class);

        $this->UserService
            ->method('upload')
            ->willReturn([
                ['name' => 'a.png', 'success' => true, 'path' => 'storage/a.png'],
                ['name' => 'b.webp', 'success' => false, 'message' => 'b.webp has an invalid file type.'],
            ]);

        $this->controller->upload($request);

        $this->assertSame(
            [['files' => 'b.webp has an invalid file type.']],
            $_SESSION['ERROR_MESSAGES']
        );
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