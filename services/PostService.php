<?php

namespace app\services;

use app\aura\Service;
use app\aura\Logger;
use app\aura\https\Redirect;
use app\aura\utils\Session;
use app\requests\PostRequest;
use app\entities\PostEntity as Post;
use app\repositories\PostRepository;
use app\repositories\UserRepository;

/**
 * Class PostService
 *
 * Handles post-related business logic.
 *
 * @package app\services
 */
class PostService extends Service {
    /**
     * PostService constructor.
     *
     * @param Logger $logger
     * @param PostRepository $postRepository
     * @param UserRepository $userRepository
     * @param Session $session
     */
    public function __construct(
        private Logger $logger,
        private PostRepository $postRepository,
        private UserRepository $userRepository,
        private Session $session) {
        parent::__construct();
    }

    /**
     * Retrieve the post list.
     *
     * @return array|void
     */
    public function index() {
        $result = $this->userRepository->checkSign();

        if (!$result) {
            $this->logger->warn(
                'User not signed in, redirecting to signin page.'
            );

            return Redirect::to('signin');
        }

        return $this->postRepository->show();
    }

    /**
     * Create a new post.
     *
     * @param PostRequest $postRequest
     * @return bool
     */
    public function create(PostRequest $postRequest): bool {
        $this->beginTransaction();

        try {

            $post = $this->makePost($postRequest);

            $result =
                $this->postRepository
                    ->createPost($post);

            if (!$result) {
                throw new \Exception(
                    'Post creation failed.'
                );
            }

            $this->commit();

            return true;

        } catch (\Exception $e) {

            $this->rollBack();

            $this->logger->error(
                $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Update an existing post.
     *
     * @param PostRequest $postRequest
     * @return bool
     */
    public function update(PostRequest $postRequest): bool {
        $this->beginTransaction();

        try {

            $post = $this->makePost($postRequest);

            $result =
                $this->postRepository
                    ->updatePost($post);

            if (!$result) {
                throw new \Exception(
                    'Post update failed.'
                );
            }

            $this->commit();

            return true;

        } catch (\Exception $e) {

            $this->rollBack();

            $this->logger->error(
                $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Delete a post.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $this->beginTransaction();

        try {

            $result =
                $this->postRepository
                    ->deletePost($id);

            if (!$result) {
                throw new \Exception(
                    'Post deletion failed.'
                );
            }

            $this->commit();

            return true;

        } catch (\Exception $e) {

            $this->rollBack();

            $this->logger->error(
                $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Retrieve a post by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getPost(int $id): ?array {
        $this->logger->info(
            'Fetching post.'
        );

        return $this->postRepository
            ->getPost($id);
    }

    /**
     * Create a Post entity from the request.
     *
     * @param PostRequest $postRequest
     * @return Post
     */
    public function makePost(PostRequest $postRequest): Post {
        $post = new Post();

        if (!empty($postRequest->input('id'))) {
            $post->setId($postRequest->input('id'));
        }

        $post->setUserId($postRequest->input('user_id'));
        $post->setTitle($postRequest->input('title'));
        $post->setBody($postRequest->input('body'));

        return $post;
    }
}