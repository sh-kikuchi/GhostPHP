<!-- vendor/bin/phpunit tests\requests\UserRequestTest.php -->
<?php

use PHPUnit\Framework\TestCase;
use app\requests\UserRequest;

/**
 * Class UserRequestTest
 *
 * PHPUnit test case for the UserRequest class.
 */
class UserRequestTest extends TestCase
{
    /**
     * Test the constructor with valid data.
     */
    public function testConstructorWithValidData()
    {
        $data = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'securepassword',
            'password_conf' => 'securepassword',
        ];

        $userRequest = new UserRequest($data);

        $this->assertEquals(1, $userRequest->getId());
        $this->assertEquals('John Doe', $userRequest->getName());
        $this->assertEquals('johndoe@example.com', $userRequest->getEmail());
        $this->assertEquals('securepassword', $userRequest->getPassword());
        $this->assertEquals('securepassword', $userRequest->getPasswordConf());
    }

    /**
     * Test the getArrayData method to ensure it returns correct data.
     */
    public function testGetArrayData()
    {
        $data = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'securepassword',
            'password_conf' => 'securepassword',
            'csrf_token' => 'csrf123456'
        ];

        $userRequest = new UserRequest($data);
        $arrayData = $userRequest->getArrayData();

        $this->assertEquals($data, $arrayData);
    }
}
?>
