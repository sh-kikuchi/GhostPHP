<!-- vendor/bin/phpunit tests\models\repositories\PostRepositoryTest.php -->
<?php

use PHPUnit\Framework\TestCase;
use app\repositories\UserRepository;
use app\entities\UserEntity as User;

/**
 * Test case for the UserRepository class.
 */
class UserRepositoryTest extends TestCase
{
    /**
     * @var PDO Mocked PDO instance.
     */
    private $pdo;

    /**
     * @var UserRepository Instance of UserRepository to be tested.
     */
    private $userRepository;

    /**
     * @var User Mocked User entity.
     */
    private $user;

    /**
     * Set up the test environment.
     * Initializes the mocked PDO, UserRepository, and User entities.
     * Starts a new session for testing.
     */
    protected function setUp(): void
    {
        // Create a mock of PDO
        $this->pdo = $this->createMock(PDO::class);
        $this->userRepository = new UserRepository($this->pdo);
        $this->user = $this->createMock(User::class);

        // // Start session if it is not already started
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }
        // Reset the session
        $_SESSION = [];
    }

    /**
     * Clean up after each test.
     * Clears the session and closes it to ensure no state leakage between tests.
     */
    // public function tearDown(): void
    // {
    //     $_SESSION = [];
    //     session_write_close();
    // }

    /**
     * Test the signup method of the UserRepository class.
     * Verifies that the signup method correctly interacts with the database and returns true on success.
     */
    public function testSignup()
    {
        $this->user->method('getName')->willReturn('Test User');
        $this->user->method('getEmail')->willReturn('test@example.com');
        $this->user->method('getPassword')->willReturn('password');

        // Create a mock of PDOStatement
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())->method('execute')->willReturn(true);

        // Configure PDO to return the mocked PDOStatement
        $this->pdo->method('prepare')->willReturn($statement);

        $result = $this->userRepository->signup($this->user);
        $this->assertTrue($result);
    }

    /**
     * Test the signin method of the UserRepository class.
     * Checks if the signin method successfully handles the user login process.
     */
    public function testSignin()
    {
        $this->user->method('getEmail')->willReturn('test@example.com');
        $this->user->method('getPassword')->willReturn('password');
        $password = password_hash('password', PASSWORD_DEFAULT);
    
        $user_data = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
        ];
    
        // Create a mock of PDOStatement
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('fetch')->willReturn($user_data);
        $statement->method('execute')->willReturn(true);
    
        // Configure PDO to return the mocked PDOStatement
        $this->pdo->method('prepare')->willReturn($statement);
    
        // Perform signin and check result
        $result = $this->userRepository->signin($this->user);
        $this->assertTrue($result);
    
        // Verify session data
        $this->assertArrayHasKey('signin_user', $_SESSION);
        $this->assertEquals($user_data, $_SESSION['signin_user']);
    }

    /**
     * Test the getUserByEmail method of the UserRepository class.
     * Ensures that the method correctly retrieves user data by email.
     */
    public function testGetUserByEmail()
    {
        $email = 'test@example.com';
        $user_data = [
            'id' => 1,
            'name' => 'Test User',
            'email' => $email,
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ];

        // Create a mock of PDOStatement
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('fetch')->willReturn($user_data);
        $statement->method('execute')->willReturn(true);

        // Configure PDO to return the mocked PDOStatement
        $this->pdo->method('prepare')->willReturn($statement);

        $result = $this->userRepository->getUserByEmail($email);
        $this->assertEquals($user_data, $result);
    }

    /**
     * Test the checkSign method of the UserRepository class.
     * Verifies that the method correctly checks if a user is signed in based on session data.
     */
    public function testCheckSign()
    {
        // Clear the session and check if user is signed in
        $_SESSION = [];
        $this->assertFalse($this->userRepository->checkSign());

        // Simulate a signed-in user
        $_SESSION['signin_user'] = ['id' => 1];
        $this->assertTrue($this->userRepository->checkSign());
    }

    /**
     * Test the signout method of the UserRepository class.
     * Ensures that the signout method properly clears the session data.
     */
    public function testSignout()
    {
        // Simulate a signed-in user and sign out
        $_SESSION['signin_user'] = ['id' => 1];
        $this->userRepository->signout();

        // Verify that the session is empty after signout
        $this->assertArrayNotHasKey('signin_user', $_SESSION);
    }
}
