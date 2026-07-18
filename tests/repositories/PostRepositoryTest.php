<?php
// vendor/bin/phpunit --display-warnings tests/Repositories/PostRepositoryTest.php

use PHPUnit\Framework\TestCase;
use app\repositories\PostRepository;
use app\entities\PostEntity as Post;

class PostRepositoryTest extends TestCase {
    private PDO $pdo;
    private PDOStatement $statement;
    private PostRepository $repository;
    private Post $post;

    protected function setUp(): void {
        $this->pdo = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);

        $this->repository = new PostRepository($this->pdo);

        $this->post = $this->createMock(Post::class);
    }

    /**
     * Tests retrieving all posts.
     */
    public function testShow(): void {
        $expected = [
            [
                'id' => 1,
                'title' => 'Test Title',
                'body' => 'Test Body',
                'user_id' => 1
            ]
        ];

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `posts`')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute');

        $this->statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->repository->show());
    }

    /**
     * Tests retrieving a post by ID.
     */
    public function testGetPost(): void {
        $expected = [
            'id' => 1,
            'title' => 'Test',
            'body' => 'Body',
            'user_id' => 1
        ];

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `posts` WHERE id = ?')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->statement
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->repository->getPost(1));
    }

    /**
     * Tests creating a new post.
     */
    public function testCreatePost(): void {
        $this->post->method('getTitle')->willReturn('Test Title');
        $this->post->method('getBody')->willReturn('Test Body');
        $this->post->method('getUserId')->willReturn(5);

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `posts` (`title`, `body`, `user_id`) VALUES (?, ?, ?)')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'Test Title',
                'Test Body',
                5
            ])
            ->willReturn(true);

        $this->assertTrue($this->repository->createPost($this->post));
    }

    /**
     * Tests updating an existing post.
     */
    public function testUpdatePost(): void {
        $this->post->method('getId')->willReturn(10);
        $this->post->method('getTitle')->willReturn('New Title');
        $this->post->method('getBody')->willReturn('New Body');

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `posts` SET `title` = ?, `body` = ? WHERE id = ?')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'New Title',
                'New Body',
                10
            ])
            ->willReturn(true);

        $this->assertTrue($this->repository->updatePost($this->post));
    }

    /**
     * Tests deleting a post.
     */
    public function testDeletePost(): void {
        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM `posts` WHERE id = ?')
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([3])
            ->willReturn(true);

        $this->assertTrue($this->repository->deletePost(3));
    }
}