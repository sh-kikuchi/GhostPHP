<?php
// vendor/bin/phpunit --display-warnings tests/services/PostServiceTest.php

use PHPUnit\Framework\TestCase;
use app\services\PostService;
use app\aura\Logger;
use app\aura\utils\Session;
use app\repositories\PostRepository;
use app\repositories\UserRepository;
use app\requests\PostRequest;

require 'bootstrap.php';

/**
 * Test cases for PostService.
 */
class PostServiceTest extends TestCase {
    private PostService $service;
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private Logger $logger;
    private Session $session;

    /**
     * Set up test dependencies.
     *
     * @return void
     */
    protected function setUp(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        $_POST = [];

        $this->logger = $this->createMock(Logger::class);
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->session = $this->createMock(Session::class);

        $this->service = new PostService(
            $this->logger,
            $this->postRepository,
            $this->userRepository,
            $this->session
        );
    }

    /**
     * Test retrieving the post list.
     *
     * @return void
     */
    public function testIndex(): void {
        $posts = [
            [
                'id' => 1,
                'title' => 'Post1',
                'body' => 'Body'
            ]
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('checkSign')
            ->willReturn(true);

        $this->postRepository
            ->expects($this->once())
            ->method('show')
            ->willReturn($posts);

        $result = $this->service->index();

        $this->assertEquals($posts, $result);
    }

    /**
     * Test creating a PostEntity from a request.
     *
     * @return void
     */
    public function testMakePost(): void {
        $request = new PostRequest();

        $request->fill([
            'user_id' => 1,
            'title' => 'Test Title',
            'body' => 'Test Body'
        ]);

        $entity = $this->service->makePost($request);

        $this->assertEquals(1, $entity->getUserId());
        $this->assertEquals('Test Title', $entity->getTitle());
        $this->assertEquals('Test Body', $entity->getBody());
    }

    /**
     * Test successful post creation.
     *
     * @return void
     */
    public function testCreateSuccess(): void {
        $request = new PostRequest();

        $request->fill([
            'user_id' => 1,
            'title' => 'Test Title',
            'body' => 'Test Body'
        ]);

        $this->postRepository
            ->expects($this->once())
            ->method('createPost')
            ->willReturn(true);

        $this->assertTrue(
            $this->service->create($request)
        );
    }

    /**
     * Test failed post creation.
     *
     * @return void
     */
    public function testCreateFailure(): void {
        $request = new PostRequest();

        $request->fill([
            'user_id' => 1,
            'title' => 'Test Title',
            'body' => 'Test Body'
        ]);

        $this->postRepository
            ->expects($this->once())
            ->method('createPost')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->create($request)
        );
    }

    /**
     * Test successful post update.
     *
     * @return void
     */
    public function testUpdateSuccess(): void {
        $_SESSION['csrf_token']['post_update'] = 'token';
        $_POST['csrf_token'] = 'token';

        $request = new PostRequest();

        $request->fill([
            'id' => 1,
            'user_id' => 1,
            'title' => 'Updated',
            'body' => 'Body',
            'csrf_token' => 'token'
        ]);

        $this->postRepository
            ->expects($this->once())
            ->method('updatePost')
            ->willReturn(true);

        $this->assertTrue(
            $this->service->update($request)
        );
    }

    /**
     * Test successful post deletion.
     *
     * @return void
     */
    public function testDeleteSuccess(): void {
        $_SESSION['csrf_token']['post_delete'] = 'token';
        $_POST['csrf_token'] = 'token';

        $this->postRepository
            ->expects($this->once())
            ->method('deletePost')
            ->with(1)
            ->willReturn(true);

        $this->assertTrue(
            $this->service->delete(1)
        );
    }

    /**
     * Test retrieving a post by ID.
     *
     * @return void
     */
    public function testGetPost(): void {
        $post = [
            'id' => 1,
            'title' => 'Post1',
            'body' => 'Body'
        ];

        $this->postRepository
            ->expects($this->once())
            ->method('getPost')
            ->with(1)
            ->willReturn($post);

        $this->assertEquals(
            $post,
            $this->service->getPost(1)
        );
    }
}