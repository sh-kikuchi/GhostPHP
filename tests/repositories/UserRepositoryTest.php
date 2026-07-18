<?php
// vendor/bin/phpunit --display-warnings tests/Repositories/UserRepositoryTest.php

use PHPUnit\Framework\TestCase;
use app\repositories\UserRepository;
use app\entities\UserEntity as User;

class UserRepositoryTest extends TestCase {
    private PDO $pdo;
    private PDOStatement $statement;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void {
        // Initialize the session without starting it.
        $_SESSION = [];

        $this->pdo = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);

        $this->userRepository = new UserRepository($this->pdo);
        $this->user = $this->createMock(User::class);
    }

    /**
     * Tests successful user registration.
     */
    public function testSignup(): void {
        $this->user->method('getName')->willReturn('Test User');
        $this->user->method('getEmail')->willReturn('test@example.com');
        $this->user->method('getPassword')->willReturn('password');

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `users` (`name`, `email`, `password`) VALUES (?, ?, ?)')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($params) {
                $this->assertEquals('Test User', $params[0]);
                $this->assertEquals('test@example.com', $params[1]);

                $this->assertTrue(
                    password_verify('password', $params[2])
                );

                return true;
            }))
            ->willReturn(true);

        $this->assertTrue(
            $this->userRepository->signup($this->user)
        );
    }

    /**
     * Tests retrieving a user by email.
     */
    public function testGetUserByEmail(): void {
        $email = 'test@example.com';

        $expected = [
            'id' => 1,
            'name' => 'Test User',
            'email' => $email,
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ];

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `users` WHERE `email` = ? LIMIT 1')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([$email]);

        $this->statement
            ->expects($this->once())
            ->method('fetch')
            ->willReturn($expected);

        $this->assertEquals(
            $expected,
            $this->userRepository->getUserByEmail($email)
        );
    }

    /**
     * Tests retrieving a user when the email does not exist.
     */
    public function testGetUserByEmailNotFound(): void {
        $this->pdo
            ->method('prepare')
            ->willReturn($this->statement);

        $this->statement
            ->method('execute')
            ->willReturn(true);

        $this->statement
            ->method('fetch')
            ->willReturn(false);

        $this->assertNull(
            $this->userRepository->getUserByEmail('notfound@test.com')
        );
    }

    /**
     * Tests successful user sign-in.
     */
    public function testSigninSuccess(): void {
        $this->user->method('getEmail')->willReturn('test@example.com');
        $this->user->method('getPassword')->willReturn('password');

        $hashed = password_hash('password', PASSWORD_DEFAULT);

        $userData = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $hashed
        ];

        $this->pdo->method('prepare')->willReturn($this->statement);
        $this->statement->method('execute')->willReturn(true);
        $this->statement->method('fetch')->willReturn($userData);

        $result = $this->userRepository->signin($this->user);

        $this->assertTrue($result);
        $this->assertArrayHasKey('signin_user', $_SESSION);
    }

    /**
     * Tests sign-in with an unknown email address.
     */
    public function testSigninEmailNotFound(): void {
        $this->user->method('getEmail')->willReturn('test@example.com');
        $this->user->method('getPassword')->willReturn('password');

        $this->pdo->method('prepare')->willReturn($this->statement);
        $this->statement->method('fetch')->willReturn(false);

        $result = $this->userRepository->signin($this->user);

        $this->assertFalse($result);
        $this->assertArrayNotHasKey('signin_user', $_SESSION);
    }

    /**
     * Tests sign-in with an incorrect password.
     */
    public function testSigninWrongPassword(): void {
        $this->user->method('getEmail')->willReturn('test@example.com');
        $this->user->method('getPassword')->willReturn('wrong');

        $userData = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('correct', PASSWORD_DEFAULT)
        ];

        $this->pdo->method('prepare')->willReturn($this->statement);
        $this->statement->method('execute')->willReturn(true);
        $this->statement->method('fetch')->willReturn($userData);

        $result = $this->userRepository->signin($this->user);

        $this->assertFalse($result);
        $this->assertArrayNotHasKey('signin_user', $_SESSION);
    }

    /**
     * Tests whether the user is signed in.
     */
    public function testCheckSign(): void {
        $_SESSION['signin_user'] = ['id' => 1];

        $this->assertTrue($this->userRepository->checkSign());
    }

    /**
     * Tests successful user sign-out.
     *
     * @runInSeparateProcess
     */
    public function testSignout(): void {
        $_SESSION['signin_user'] = ['id' => 1];

        $this->userRepository->signout();

        $this->assertEmpty($_SESSION);
    }
}