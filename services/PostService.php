<?php

namespace app\services;

use app\aura\Service;
use app\aura\Template;
use app\aura\Logger;
use app\aura\https\Redirect;
use app\aura\utils\Session;
use app\requests\PostRequest;
use app\entities\PostEntity as Post;
use app\repositories\PostRepository;
use app\repositories\UserRepository;

class PostService extends Service implements IPostService {

    /**
     * PostService constructor.
     * Initializes the base service and session handler.
     */
    public function __construct() {
        parent::__construct();
        $session = new Session;
    }

    /**
     * Display the list of posts with pagination.
     *
     * @return string|null Rendered HTML view of post index, or null if redirected.
     */
    public function index() {
        $logger = new Logger();
        $csrf = $this->setToken('post_delete');
    
        $user = new UserRepository();
        $result = $user->checkSign();
    
        if (!$result) {
            $logger->warn('User not signed in, redirecting to signin page.');
            Redirect::to('signin');
            return;
        }
    
        $repository = new PostRepository();
        $showData = $repository->show();
        $pagination = paginate($showData, 10);
    
        $logger->info('Posts fetched and pagination applied.');
    
        $template = new Template(
            'post/index', [
                'csrf' => $this->setToken('post_delete'),
                'posts' => $pagination['data'],
                'max_page' => $pagination['max_page'],
                'errors' => $_SESSION['errors'] ?? null
            ]
        );
    
        unset($_SESSION['errors']);
    
        $logger->info('Rendering post index template.');
    
        return $template->render();
    }

    /**
     * Show the form to create a new post.
     *
     * @return string|null Rendered HTML form or null if redirected.
     */
    public function showCreateForm() {
        new Session;
        $user = new UserRepository();

        // Check authorization
        $result = $user->checkSign();
        if (!$result) {
            Redirect::to('signin');
            return;
        }

        // Rendering
        $template = new Template(
            'post/form', [
                'csrf' => $this->setToken('post_create'),
                'signin_user' => $_SESSION['signin_user'] ?? null,
                'errors' => $_SESSION['errors'] ?? null,
                'old' => $_SESSION['old'] ?? null
            ]
        );
        unset($_SESSION['errors']);
        unset($_SESSION['old']);

        return $template->render();
    }

    /**
     * Show the form to update an existing post.
     *
     * @return string|null Rendered HTML form or null if redirected.
     */
    public function showUpdateForm() {
        new Session;
        $user = new UserRepository();
        $result = $user->checkSign();

        if (!$result) {
            Redirect::to('signin');
            return;
        }

        $repository = new PostRepository();
        $post = $repository->getPost(intval($_GET["id"]));

        // Rendering
        $template = new Template(
            'post/form', [
                'csrf' => $this->setToken('post_update'),
                'post' => $post,
                'signin_user' => $_SESSION['signin_user'] ?? null,
                'errors' => $_SESSION['errors'] ?? null,
            ]
        );
        unset($_SESSION['errors']);
        unset($_SESSION['old']);

        return $template->render();
    }

    /**
     * Create a new post from form data.
     *
     * @return bool True on success, false on failure.
     */
    public function create() {
        // Check token
        if (!$this->checkToken('post_create')) {
            echo 'Invalid token.';
            return false;
        }

        // Start transaction
        $this->beginTransaction();

        try {
            // Create an instance
            $post = new PostRepository();
            $post_request = $this->makePost($_POST);

            // Execute query
            $result = $post->createPost($post_request);

            if (!$result) {
                throw new \Exception('Post creation failed.');
            }

            // Commit transaction
            $this->commit();

            // Redirect
            Redirect::to('post');
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            $this->rollBack();
            Redirect::error(500);
        }
    }

    /**
     * Update an existing post from form data.
     *
     * @return bool True on success, false on failure.
     */
    public function update() {
        // Check token
        if (!$this->checkToken('post_update')) {
            echo 'Invalid token.';
            return false;
        }

        // Start transaction
        $this->beginTransaction();

        try {
            // Create an instance
            $post = new PostRepository();
            $post_request = $this->makePost($_POST);

            // Execute query
            $result = $post->updatePost($post_request);

            if (!$result) {
                throw new \Exception('Post update failed.');
            }

            // Commit transaction
            $this->commit();

            // Redirect
            Redirect::to('post');
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            $this->rollBack();
            Redirect::error(500);
        }
    }

    /**
     * Delete a post based on form input.
     *
     * @return bool True on success, false on failure.
     */
    public function delete() {
        // Check token
        if (!$this->checkToken('post_delete')) {
            echo 'Invalid token.';
            return false;
        }

        // Start transaction
        $this->beginTransaction();

        try {
            // Create an instance
            $post = new PostRepository();
            $post_request = $this->makePost($_POST);

            // Execute query
            $result = $post->deletePost($post_request);

            if (!$result) {
                throw new \Exception('Post deletion failed.');
            }

            // Commit transaction
            $this->commit();

            // Redirect
            Redirect::to('post');
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            $this->rollBack();
            Redirect::error(500);
        }
    }

    /**
     * Create a Post entity instance from submitted form data.
     *
     * @param array $post_form Form data from POST request.
     * @return Post The constructed Post entity.
     */
    public function makePost(array $post_form): Post {
        $post = new Post();
        $post_request = new PostRequest($post_form);

        if ($post_request->getId() !== null) {
            $post->setId($post_request->getId());
        }
        $post->setUserId($post_request->getUserId());
        $post->setTitle($post_request->getTitle());
        $post->setBody($post_request->getBody());

        return $post;
    }
}
