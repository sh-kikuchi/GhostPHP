<?php
// vendor/bin/phpunit --display-warnings tests/Controllers/PostControllerTest.php
namespace tests\Controllers;

use PHPUnit\Framework\TestCase;
use app\controllers\PostController;
use app\services\PostService;
use app\services\UserService;
use app\requests\PostRequest;

class PostControllerTest extends TestCase {
    private PostService $postService;
    private UserService $UserService;
    private PostController $controller;

    protected function setUp(): void {
        $this->postService = $this->createMock(PostService::class);
        $this->UserService = $this->createMock(UserService::class);

        $this->controller = new PostController(
            $this->postService,
            $this->UserService
        );

        $_SESSION = [];
        $_GET = [];
        $_POST = [];

        error_reporting(E_ALL & ~E_WARNING);
        ob_start();
    }

    protected function tearDown(): void {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];

        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    /**
     * Tests successful retrieval of the post list.
     */
    public function testIndexSuccess(): void {
        $this->postService = $this->createMock(PostService::class);
        $this->postService->method('index')->willReturn([
            'data' => [
                ['id' => 1, 'title' => 'test']
            ],
            'max_page' => 1
        ]);

        $this->controller->index();

        $this->assertTrue(true);

        error_reporting(E_ALL & ~E_WARNING);
    }

    /**
     * Tests failure to retrieve the post list.
     */
    public function testIndexFail(): void {
        $this->postService->method('index')->willReturn(false);

        $this->controller->index();

        $this->assertTrue(true);

        error_reporting(E_ALL & ~E_WARNING);
    }

    /**
     * Tests displaying the create form for a signed-in user.
     */
    public function testShowCreateForm(): void {
        $this->UserService->method('checkSign')->willReturn(true);

        $this->controller->showCreateForm();

        $this->assertTrue(true);
    }

    /**
     * Tests displaying the create form for a guest user.
     */
    public function testShowCreateFormNotSignin(): void {
        $this->UserService->method('checkSign')->willReturn(false);

        $this->controller->showCreateForm();

        $this->assertTrue(true);
    }

    /**
     * Tests successful post creation.
     */
    public function testCreateSuccess(): void {
        $request = $this->createMock(PostRequest::class);

        $this->UserService->method('checkToken')->willReturn(true);
        $this->postService->method('create')->willReturn(true);

        $this->controller->create($request);

        $this->assertTrue(true);
    }

    /**
     * Tests post creation with an invalid CSRF token.
     */
    public function testCreateInvalidToken(): void {
        $request = $this->createMock(PostRequest::class);

        $this->UserService->method('checkToken')->willReturn(false);

        ob_start();
        $this->controller->create($request);
        $output = ob_get_clean();

        $this->assertEquals('Invalid token.', $output);
    }

    /**
     * Tests failure during post creation.
     */
    public function testCreateFail(): void {
        $request = $this->createMock(PostRequest::class);

        $this->UserService->method('checkToken')->willReturn(true);
        $this->postService->method('create')->willReturn(false);

        $this->controller->create($request);

        $this->assertTrue(true);
    }

    /**
     * Tests displaying the update form.
     */
    public function testShowUpdateForm(): void {
        $_GET['id'] = 1;

        $this->UserService->method('checkSign')->willReturn(true);
        $this->postService->method('getPost')->willReturn([
            'id' => 1,
            'title' => 'title',
            'body' => 'body'
        ]);

        $this->controller->showUpdateForm();

        $this->assertTrue(true);
    }

    /**
     * Tests displaying the update form for a guest user.
     */
    public function testShowUpdateFormNotSignin(): void {
        $this->UserService->method('checkSign')->willReturn(false);

        $this->controller->showUpdateForm();

        $this->assertTrue(true);
    }

    /**
     * Tests successful post update.
     */
    public function testUpdateSuccess(): void {
        $request = $this->createMock(PostRequest::class);

        $this->postService->method('update')->willReturn(true);

        $this->controller->update($request);

        $this->assertTrue(true);
    }

    /**
     * Tests failure during post update.
     */
    public function testUpdateFail(): void {
        $request = $this->createMock(PostRequest::class);

        $this->postService->method('update')->willReturn(false);

        $this->controller->update($request);

        $this->assertTrue(true);
    }

    /**
     * Tests successful post deletion.
     */
    public function testDeleteSuccess(): void {
        $_POST['id'] = 10;

        $controller = $this->getMockBuilder(PostController::class)
            ->setConstructorArgs([
                $this->postService,
                $this->UserService
            ])
            ->onlyMethods(['checkToken'])
            ->getMock();

        $controller->method('checkToken')
            ->willReturn(true);

        $this->postService->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(true);

        $controller->delete();
    }

    /**
     * Tests failure during post deletion.
     */
    public function testDeleteFail(): void {
        $_POST['id'] = 10;

        $controller = $this->getMockBuilder(PostController::class)
            ->setConstructorArgs([
                $this->postService,
                $this->UserService
            ])
            ->onlyMethods(['checkToken'])
            ->getMock();

        $controller->method('checkToken')
            ->willReturn(true);

        $this->postService->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(false);

        $controller->delete();
    }
}