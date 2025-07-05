<?php

use PHPUnit\Framework\TestCase;
use app\repositories\PostRepository;
use app\entities\PostEntity as Post;

/**
 * Test case for the PostRepository class
 */
class PostRepositoryTest extends TestCase
{
    private $pdo;
    private $postRepository;
    private $post;

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        // Create a mock of PDO
        $this->pdo = $this->createMock(PDO::class);
        $this->postRepository = new PostRepository($this->pdo);
        $this->post = $this->createMock(Post::class);
    }

    /**
     * Test the show method of the PostRepository class
     */
    public function testShow()
    {
        // モックされたPDOStatementを作成
        $statement = $this->createMock(PDOStatement::class);
        
        // fetchAll()が返す値を設定
        $statement->method('fetchAll')->willReturn([
            ['id' => 1, 'user_id' => 1, 'title' => 'Test Title', 'body' => 'Test Body']
        ]);
    
        // query()が呼ばれた際に、上記のモックされたPDOStatementを返すように設定
        $this->pdo->method('prepare')->willReturn($statement);
    
        // show()メソッドを実行して結果を検証
        $result = $this->postRepository->show();
        
        // 結果の件数が1件であることを確認
        $this->assertCount(1, $result);
        
        // 結果のタイトルが「Test Title」であることを確認
        $this->assertEquals('Test Title', $result[count($result)-1]['title']);
    }
    /**
     * Test the getPost method of the PostRepository class
     */
    public function testGetPost()
    {
        $expectedPost = [
            'id' => 1,
            'title' => 'Test Post',
            'content' => 'This is a test post.'
        ];

        // Create a mock of PDOStatement
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('fetch')->willReturn($expectedPost);

        // Mock prepare to return the PDOStatement mock
        $this->pdo->method('prepare')->willReturn($statement);

        // Execute getPost and check the result
        $result = $this->postRepository->getPost(1);
        $this->assertEquals($expectedPost, $result);
    }

    /**
     * Test the create method of the PostRepository class
     */
    public function testCreate()
    {
        // Mock post data
        $this->post->method('getUserId')->willReturn(1);
        $this->post->method('getTitle')->willReturn('Test Title');
        $this->post->method('getBody')->willReturn('Test Body');

        // Create a mock for the PDOStatement and expect execute() to be called once
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())->method('execute')->willReturn(true);

        // Mock prepare to return the PDOStatement mock
        $this->pdo->method('prepare')->willReturn($statement);

        // Execute createPost and verify the result
        $result = $this->postRepository->createPost($this->post);
        $this->assertTrue($result);
    }

    /**
     * Test the update method of the PostRepository class
     */
    public function testUpdate()
    {
        // Mock post data
        $this->post->method('getId')->willReturn(1);
        $this->post->method('getTitle')->willReturn('Updated Title');
        $this->post->method('getBody')->willReturn('Updated Body');

        // Create a mock for the PDOStatement and expect execute() to be called once
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())->method('execute')->willReturn(true);

        // Mock prepare to return the PDOStatement mock
        $this->pdo->method('prepare')->willReturn($statement);

        // Execute updatePost and verify the result
        $result = $this->postRepository->updatePost($this->post);
        $this->assertTrue($result);
    }

    /**
     * Test the delete method of the PostRepository class
     */
    public function testDelete()
    {
        // Mock post data
        $this->post->method('getId')->willReturn(1);

        // Create a mock for the PDOStatement and expect execute() to be called once
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())->method('execute')->willReturn(true);

        // Mock prepare to return the PDOStatement mock
        $this->pdo->method('prepare')->willReturn($statement);

        // Execute deletePost and verify the result
        $result = $this->postRepository->deletePost($this->post);
        $this->assertTrue($result);
    }
}
