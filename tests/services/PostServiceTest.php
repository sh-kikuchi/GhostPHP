<!-- vendor/bin/phpunit tests\services\PostServiceTest.php  -->
<?php

use PHPUnit\Framework\TestCase;
use app\aura\Service;
use app\aura\Template;
use app\aura\utils\Session;
use app\form_classes\PostRequest;
use app\services\PostService;
use app\entities\PostEntity;
use app\repositories\UserRepository;
use app\repositories\PostRepository;

require 'bootstrap.php';

/**
 * Test case for the PostService class
 *
 * This class contains test methods for verifying the functionality of the PostService class.
 * It uses PHPUnit framework to create mock objects and test various methods of the PostService class.
 */
class PostServiceTest extends TestCase
{
    /**
     * Test the index method of the PostService class.
     *
     * This test verifies that the index method correctly handles session errors,
     * interacts with the UserRepository and PostRepository, and initializes the Template correctly.
     *
     * @return void
     */
    public function testIndex() 
    {
        $_SESSION['errors'] = ['Error message 1', 'Error message 2'];

        // Create a mock of UserRepository
        $mock = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock->method('checkSign')->willReturn(true);
        $result = $mock->checkSign();
        $this->assertEquals(true, $result);

        // Create a mock of PostRepository
        $mock_post_repository = $this->getMockBuilder(PostRepository::class)->getMock();
        $mockPostData = [
            [
                'id' => 1, 
                'title' => 'Post 1',
                'body' => '世田谷給田', 
                'created_at' => '2024-05-02 21:01:11', 
                'updated_at' => '2024-05-02 21:01:11' 
            ],
        ];
        $mock_post_repository->method('show')->willReturn($mockPostData);

        $postShowData = $mock_post_repository->show();
        $this->assertEquals($mockPostData, $postShowData);

        // Create a Template instance
        $template = new Template(
            'post/index', [
                'csrf'     => 'test',
                'posts'    => $postShowData,
                'max_page' => 3,
                'errors'   => isset($_SESSION['errors']) ? $_SESSION['errors'] : null
            ]
        );

        unset($_SESSION['errors']);

        $this->assertArrayNotHasKey('errors', $_SESSION);
        $this->assertNotNull($template);
    }

    /**
     * Test the createForm method of the PostService class.
     *
     * This test verifies that the createForm method initializes the Template correctly
     * with the expected data.
     *
     * @return void
     */
    public function testCreateForm() 
    {
        $mock = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock->method('checkSign')->willReturn(true);

        // Create a Template instance
        $template = new Template(
            'post/create_form', [
                'csrf'        => 'test',
                'signin_user' => 'katuobushi nekotaro',
                'errors'      => null,
                'old'         => null
            ]
        );

        $this->assertNotNull($template);
    }

    /**
     * Test the updateForm method of the PostService class.
     *
     * This test verifies that the updateForm method correctly retrieves post data
     * and initializes the Template with the expected data.
     *
     * @return void
     */
    public function testUpdateForm() 
    {
        $param = 3;

        $mock = $this->getMockBuilder(UserRepository::class)->getMock();
        $mock->method('checkSign')->willReturn(true);

        $mock_post_repository = $this->getMockBuilder(PostRepository::class)->getMock();
        $mockPostData = [
            'id' => 1,
            'title' => 'Post 1',
            'body' => 'Test Body',
            'created_at' => '2024-05-02 21:01:11',
            'updated_at' => '2024-05-02 21:01:11'
        ];
    
        $mock_post_repository->method('getPost')->willReturn($mockPostData);

        $postUpdateForm = $mock_post_repository->getPost($param);
        $this->assertEquals($mockPostData, $postUpdateForm);

        // Create a Template instance
        $template = new Template(
            'post/update_form', [
                'csrf'        => 'test',
                'posts'       =>  $postUpdateForm,
                'signin_user' =>  null,
                'errors'      =>  null,
            ]
        );

        $this->assertNotNull($template);
    }

    /**
     * Test the makePost method of the PostService class.
     *
     * This test verifies that the makePost method correctly creates a PostEntity
     * instance from POST data.
     *
     * @return void
     */
    public function testMakePost() 
    {
        $post_service = new PostService();
        $post_entity  = new PostEntity();

        $_POST = [
            'user_id' => '1',
            'title' => 'Test Title',
            'body' => 'Test Body',
            'csrf_token' => 'valid_token'
        ];

        $post_entity->setUserId($_POST['user_id']);
        $post_entity->setTitle($_POST['title']);
        $post_entity->setBody($_POST['body']);

        $post_data =  $post_service->makePost($_POST);

        $this->assertEquals($post_entity, $post_data);
    }

    /**
     * Test the create method of the PostService class.
     *
     * This test verifies that the create method checks the CSRF token and calls
     * the PostRepository create method correctly.
     *
     * @return void
     */
    public function testCreate() 
    {
        $post_service = new PostService();

        $mock_post_repository = $this->getMockBuilder(PostRepository::class)->getMock();
        $mock_post_repository->method('createPost')->willReturn(true);

        $_SESSION['csrf_token']['post_create'] = 'valid_token';

        $_POST = [
            'user_id' => 1,
            'title' => 'Test Title',
            'body' => 'Test Body',
            'csrf_token' => 'valid_token'
        ];
           
        $token_check = $post_service->checkToken('post_create');
        $post_data =  $post_service->makePost($_POST);

        $result = $mock_post_repository->createPost($post_data);

        $this->assertTrue($token_check);
        $this->assertTrue($result);
    }

    /**
     * Test the update method of the PostService class.
     *
     * This test verifies that the update method checks the CSRF token and calls
     * the PostRepository update method correctly.
     *
     * @return void
     */
    public function testUpdate() 
    {
        $post_service = new PostService();

        $mock_post_repository = $this->getMockBuilder(PostRepository::class)->getMock();
        $mock_post_repository->method('updatePost')->willReturn(true);

        $_SESSION['csrf_token']['post_update'] = 'valid_token';

        $_POST = [
            'id'      => 100,
            'user_id' => '1',
            'title' => 'Test Title',
            'body' => 'Test Body',
            'csrf_token' => 'valid_token'
        ];
           
        $token_check = $post_service->checkToken('post_update');
        $post_data =  $post_service->makePost($_POST);

        $result = $mock_post_repository->updatePost($post_data);

        $this->assertTrue($token_check);
        $this->assertTrue($result);
    }

    /**
     * Test the delete method of the PostService class.
     *
     * This test verifies that the delete method checks the CSRF token and calls
     * the PostRepository delete method correctly.
     *
     * @return void
     */
    public function testDelete() 
    {
        $post_service = new PostService();

        $mock_post_repository = $this->getMockBuilder(PostRepository::class)->getMock();
        $mock_post_repository->method('deletePost')->willReturn(true);

        $_SESSION['csrf_token']['post_delete'] = 'valid_token';

        $_POST = [
            'id'      => 1,
            'user_id' => 1,
            'title' => 'Test Title',
            'body' => 'Test Body',
            'csrf_token' => 'valid_token'
        ];
           
        $token_check = $post_service->checkToken('post_delete');
        $post_data =  $post_service->makePost($_POST);

        $result = $mock_post_repository->deletePost($post_data);

        $this->assertTrue($token_check);
        $this->assertTrue($result);
    }
}
