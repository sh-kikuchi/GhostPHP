<!-- vendor/bin/phpunit tests\entities\UserEntityTest.php -->
<?php
use PHPUnit\Framework\TestCase;
use app\entities\UserEntity;

/**
 * Test case for the UserEntity class
 */
class UserEntityTest extends TestCase {
    /**
     * Test the getter methods of the UserEntity class
     */
    public function testGetters() {
        $user = new UserEntity();
        $user->setId(1);
        $user->setName('John Doe');
        $user->setEmail('john.doe@example.com');
        $user->setPassword('password123');
        $user->setCreatedAt('2022-01-01 12:00:00');
        $user->setUpdatedAt('2022-01-02 14:30:00');

        // Check that the getter methods return the expected values
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
        $this->assertEquals('password123', $user->getPassword());
        $this->assertEquals('2022-01-01 12:00:00', $user->getCreatedAt());
        $this->assertEquals('2022-01-02 14:30:00', $user->getUpdatedAt());
    }

    /**
     * Test the setter methods of the UserEntity class
     */
    public function testSetters() {
        $user = new UserEntity();
        $user->setId(1);
        $user->setName('John Doe');
        $user->setEmail('john.doe@example.com');
        $user->setPassword('password123');
        $user->setCreatedAt('2022-01-01 12:00:00');
        $user->setUpdatedAt('2022-01-02 14:30:00');

        // Check that the setter methods correctly update the values
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
        $this->assertEquals('password123', $user->getPassword());
        $this->assertEquals('2022-01-01 12:00:00', $user->getCreatedAt());
        $this->assertEquals('2022-01-02 14:30:00', $user->getUpdatedAt());
    }
}
