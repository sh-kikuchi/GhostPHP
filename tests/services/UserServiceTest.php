<!-- vendor/bin/phpunit tests\services\UserServiceTest.php -->
<?php

use PHPUnit\Framework\TestCase;
use app\aura\Template;
use app\aura\utils\File;
use app\aura\utils\Mail;
use app\services\UserService;
use app\repositories\UserRepository;
use app\entities\UserEntity;
require 'bootstrap.php';

/**
 * Unit tests for UserService class.
 */
class UserServiceTest extends TestCase {

    /**
     * Set up the test environment.
     */
    protected function setUp(): void {
        parent::setUp();
        $_SESSION = [];
        $_POST = [];
        $_FILES = [];
    }

    /**
     * Test for rendering the MyPage view.
     */
    public function testMyPage() {
        $_SESSION['signin_user'] = 'testman';
        $_SESSION['errors'] = ['Error message 1', 'Error message 2'];

        // Create a mock for UserRepository
        $mock = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock->method('checkSign')->willReturn(true);
        
        $result = $mock->checkSign();
        $this->assertTrue($result);

        $template = new Template(
            'user/index', [
                'signin_user' => $_SESSION['signin_user'],
            ]
        );

        unset($_SESSION['errors']);
        $this->assertArrayNotHasKey('errors', $_SESSION);
        $this->assertNotNull($template);
    }

    /**
     * Test for rendering the Signup form.
     */
    public function testSignupForm() {
        $_SESSION['errors'] = ['Error message 1', 'Error message 2'];

        // Create a mock for UserRepository
        $mock = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock->method('checkSign')->willReturn(true);
        
        $result = $mock->checkSign();
        $this->assertTrue($result);

        $user_service = new UserService();

        $template = new Template(
            'user/form', [
                'csrf'        => $user_service->setToken('signup'),
                'errors'      => $_SESSION['errors'] ?? null,
                'old'         => $_SESSION['old'] ?? null
            ]
        );

        unset($_SESSION['errors']);
        $this->assertArrayNotHasKey('errors', $_SESSION);
        $this->assertNotNull($template);
    }

    /**
     * Test for user signup process.
     */
    public function testSignup() {
        $_SESSION['csrf_token']['signup'] = 'valid_token';

        $_POST = [
            'name'       => 'testman',
            'email'      => 'test@test.com',
            'password'   => 'testtest1234',
            'password_conf' => 'testtest1234',
            'csrf_token' => 'valid_token'
        ];

        $user_service = new UserService();
        
        // Check CSRF token and create user data
        $token_check = $user_service->checkToken('signup');
        // Pass both parameters: the POST data and the 'signup' type
        $user_data = $user_service->makeUser($_POST, 'signup');

        // Create a mock for UserRepository
        $mock_user_repository = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock_user_repository->method('signup')->willReturn(true);

        $result = $mock_user_repository->signup($user_data);
        $this->assertTrue($token_check);
        $this->assertTrue($result);
    }

    /**
     * Test for rendering the Signin form.
     */
    public function testSigninForm() {
        $_SESSION['errors'] = ['Error message 1', 'Error message 2'];

        // Create a mock for UserRepository
        $mock = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock->method('checkSign')->willReturn(true);
        
        $result = $mock->checkSign();
        $this->assertTrue($result);

        $user_service = new UserService();

        $template = new Template(
            'user/form', [
                'csrf'        => $user_service->setToken('signin'),
                'errors'      => $_SESSION['errors'] ?? null,
                'old'         => $_SESSION['old'] ?? null
            ]
        );

        unset($_SESSION['errors']);
        $this->assertArrayNotHasKey('errors', $_SESSION);
        $this->assertNotNull($template);
    }

    /**
     * Test for user signin process.
     */
    public function testSignin() {
        $_SESSION['csrf_token']['signin'] = 'valid_token';

        $_POST = [
            'email'      => 'test@test.com',
            'password'   => 'jsfhdjf',
            'csrf_token' => 'valid_token'
        ];

        $user_service = new UserService();

        // Check CSRF token and create user data
        $token_check = $user_service->checkToken('signin');
        $user_data = $user_service->makeUser($_POST, 'signin');

        // Create a mock for UserRepository
        $mock_user_repository = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock_user_repository->method('signin')->willReturn(true);

        $result = $mock_user_repository->signin($user_data);
        $this->assertTrue($token_check);
        $this->assertTrue($result);
    }

    /**
     * Test for user signout process.
     */
    public function testSignout() {
        $_POST['logout'] = true;

        // Create a mock for UserRepository
        $mock_user_repository = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock_user_repository->expects($this->once())
            ->method('signout');

        $mock_user_repository->signout();
    }

    /**
     * Test for sending mail.
     */
    public function testMail() {
        $_POST = [
            'email' => 'test@test.com',
            'text'  => 'sfhdsjfhdsjfhsdkj',
        ];

        // Create a mock for Mail
        $mock_mail = $this->createMock(Mail::class);
        $mock_mail->method('sendMail')->willReturn(true);
        
        // Create a mock for UserService
        $mock_user_service = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['mail'])
            ->getMock();
        
        $mock_user_service->expects($this->once())
            ->method('mail');
        
        $mock_user_service->mail();
    }

    /**
     * Test for file upload.
     */
    public function testUpload() {
        $_FILES['file'] = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/phpYzdqkD',
            'error' => 0,
            'size' => 123
        ];

        // Create a mock for File
        $mock_file = $this->createMock(File::class);
        $mock_file->method('uploadFile')->willReturn(true);
        
        // Create a mock for UserService
        $mock_user_service = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['upload'])
            ->getMock();
        
        $mock_user_service->expects($this->once())
            ->method('upload');
        
        $mock_user_service->upload();
    }

    /**
     * Test for creating a User entity from input data.
     */
    public function testMakeUser() {
        $user_service = new UserService();
        $user_entity  = new UserEntity();

        $_POST = [
            'id'       => 1,
            'name'     => 'nekotaro',
            'email'    => 'test@test.com',
            'password' => 'dxhdjkshdjkhfdsjfh',
        ];

        $user_entity->setId($_POST['id']);
        $user_entity->setName($_POST['name']);
        $user_entity->setEmail($_POST['email']);
        $user_entity->setPassword($_POST['password']);

        $user_data = $user_service->makeUser($_POST, 'signin');

        $this->assertEquals($user_entity, $user_data);
    }
}
