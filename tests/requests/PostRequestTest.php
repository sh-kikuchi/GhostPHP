<!-- vendor/bin/phpunit tests\requests\PostRequestTest.php -->
<?php

use PHPUnit\Framework\TestCase;
use app\requests\PostRequest;

/**
 * Class PostRequestTest
 *
 * PHPUnit test case for the PostRequest class.
 */
class PostRequestTest extends TestCase
{
    /**
     * Test the constructor with valid data.
     */
    public function testConstructorWithValidData()
    {
        $data = [
            'id' => 1,
            'user_id' => 2,
            'title' => 'Sample Title',
            'body' => 'Sample Body',
        ];

        $postRequest = new PostRequest($data);

        $this->assertEquals(1, $postRequest->getId());
        $this->assertEquals(2, $postRequest->getUserId());
        $this->assertEquals('Sample Title', $postRequest->getTitle());
        $this->assertEquals('Sample Body', $postRequest->getBody());
    }

    /**
     * Test the getArrayData method to ensure it returns correct data.
     */
    public function testGetArrayData()
    {
        $data = [
            'id' => 1,
            'user_id' => 2,
            'title' => 'Sample Title',
            'body' => 'Sample Body',
        ];

        $postRequest = new PostRequest($data);
        $arrayData = $postRequest->getArrayData();

        $this->assertEquals($data, $arrayData);
    }


}
