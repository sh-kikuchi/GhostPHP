<?php

namespace app\controllers;

use app\aura\Controller;
use app\aura\Template;
use app\aura\https\Redirect;
use app\services\UserService;
use app\services\PostService;
use app\requests\PostRequest;

/**
 * Class PostController
 *
 * Handles HTTP requests related to posts.
 *
 * @package app\controllers
 */
class PostController extends Controller {
    private PostService $post_service;
    private UserService $home_service;

    /**
     * PostController constructor.
     *
     * @param PostService $postService
     * @param UserService $UserService
     */
    public function __construct(
        PostService $postService,
        UserService $UserService) {
        parent::__construct();

        $this->post_service = $postService;
        $this->home_service = $UserService;
    }

    /**
     * Display post list.
     *
     * @return void
     */
    public function index(): void {
        $data = $this->post_service->index();

        if (!$data) {
            Redirect::to('signin');
            return;
        }

        $pagination = paginate($data, 10);

        $template = new Template(
            'post/index',
            [
                'csrf' => $this->setToken('post_delete'),
                'posts' => $pagination['data'],
                'max_page' => $pagination['max_page'],
                'errors' => $_SESSION['ERROR_MESSAGES'] ?? null,
            ]
        );

        unset($_SESSION['ERROR_MESSAGES']);

        $template->render();
    }

    /**
     * Display create form.
     *
     * @return void
     */
    public function showCreateForm(): void {
        if (!$this->home_service->checkSign()) {
            Redirect::to('signin');
            return;
        }

        $template = new Template(
            'post/form',
            [
                'csrf' => $this->setToken('post_create'),
                'signin_user' => $_SESSION['signin_user'] ?? null,
                'errors' => $_SESSION['ERROR_MESSAGES'] ?? null,
                'old' => $_SESSION['old'] ?? null,
            ]
        );

        unset(
            $_SESSION['ERROR_MESSAGES'],
            $_SESSION['old']
        );

        $template->render();
    }

    /**
     * Create a new post.
     *
     * @param PostRequest $post_request
     * @return void
     */
    public function create(PostRequest $post_request): void
    {
        if (!$this->checkToken('post_create')) {
            echo 'Invalid token.';
            return;
        }

        if (!$this->post_service->create($post_request)) {
            Redirect::to('post/create');
            return;
        }

        Redirect::to('post');
        return;
    }

    /**
     * Display update form.
     *
     * @return void
     */
    public function showUpdateForm(): void {
        if (!$this->home_service->checkSign()) {
            Redirect::to('signin');
            return;
        }

        $post = $this->post_service->getPost(
            \intval($_GET['id'])
        );

        $template = new Template(
            'post/form',
            [
                'csrf' => $this->setToken('post_update'),
                'post' => $post,
                'signin_user' => $_SESSION['signin_user'] ?? null,
                'errors' => $_SESSION['ERROR_MESSAGES'] ?? null,
            ]
        );

        unset(
            $_SESSION['ERROR_MESSAGES'],
            $_SESSION['old']
        );

        $template->render();
    }

    /**
     * Update an existing post.
     *
     * @param PostRequest $post_request
     * @return void
     */
    public function update(PostRequest $post_request): void
    {
        if (!$this->checkToken('post_update')) {
            echo 'Invalid token.';
            return;
        }

        if (!$this->post_service->update($post_request)) {
            Redirect::to('post');
            return;
        }

        Redirect::to('post');
        return;
    }

    /**
     * Delete a post.
     *
     * @return void
     */
    public function delete(): void {
        if (!$this->checkToken('post_delete')) {
            return;
        }

        $id = \intval($_POST['id'] ?? 0);

        if (!$this->post_service->delete($id)) {
            Redirect::to('post');
            return;
        }

        Redirect::to('post');
        return;
    }
}