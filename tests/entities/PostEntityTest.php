<!-- vendor/bin/phpunit tests\entities\PostEntityTest.php -->
<?php
use PHPUnit\Framework\TestCase;
use app\entities\PostEntity;

/**
 * Test case for the PostEntity class
 */
class PostEntityTest extends TestCase {
    /**
     * Test the getter methods of the PostEntity class
     */
    public function testGetters() {
        $post = new PostEntity();
        $post->setId(1);
        $post->setUserId(2);
        $post->setTitle('Sample Title');
        $post->setBody('Sample Body');
        $post->setCreatedAt('2022-01-01 12:00:00');
        $post->setUpdatedAt('2022-01-02 14:30:00');

        // Check that the getter methods return the expected values
        $this->assertEquals(1, $post->getId());
        $this->assertEquals(2, $post->getUserId());
        $this->assertEquals('Sample Title', $post->getTitle());
        $this->assertEquals('Sample Body', $post->getBody());
        $this->assertEquals('2022-01-01 12:00:00', $post->getCreatedAt());
        $this->assertEquals('2022-01-02 14:30:00', $post->getUpdatedAt());
    }

    /**
     * Test the setter methods of the PostEntity class
     */
    public function testSetters() {
        $post = new PostEntity();
        $post->setId(1);
        $post->setUserId(2);
        $post->setTitle('Sample Title');
        $post->setBody('Sample Body');
        $post->setCreatedAt('2022-01-01 12:00:00');
        $post->setUpdatedAt('2022-01-02 14:30:00');

        // Check that the setter methods correctly update the values
        $this->assertEquals(1, $post->getId());
        $this->assertEquals(2, $post->getUserId());
        $this->assertEquals('Sample Title', $post->getTitle());
        $this->assertEquals('Sample Body', $post->getBody());
        $this->assertEquals('2022-01-01 12:00:00', $post->getCreatedAt());
        $this->assertEquals('2022-01-02 14:30:00', $post->getUpdatedAt());
    }
}
