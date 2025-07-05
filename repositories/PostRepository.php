<?php

namespace app\repositories;

use app\aura\Repository;  // ★ 追加
use app\entities\PostEntity as Post;
use app\aura\database\DataBaseConnect;
require_once 'interfaces/repositories/IPostRepository.php';

class PostRepository extends Repository implements IPostRepository
{
    /**
     * PostRepository constructor.
     * Initializes the repository for the 'users' table.
     */
    public function __construct() {
        parent::__construct('posts');
    }

    /**
     * Retrieves all posts from the database.
     *
     * @return array An array of posts.
     */
    public function show(): array {
        return parent::findAll(); // ★ 親クラスの findAll() を使うだけ
    }

    /**
     * Retrieves a specific post by its ID.
     *
     * @param int $id The ID of the post.
     * @return array|null The post data as an associative array, or null if not found.
     */   
    public function getPost(int $id): ?array {
        return $this->findById($id);
    }

    /**
     * Creates a new post in the database.
     *
     * @param Post $post The post entity containing title, body, and user_id.
     * @return bool True on success, false on failure.
     */
    public function createPost(Post $post): bool {
        $data = [
            'title' => $post->getTitle(),
            'body' => $post->getBody(),
            'user_id' => $post->getUserId(),
        ];
        return parent::create($data);
    }

    
    /**
     * Updates an existing post in the database.
     *
     * @param Post $post The post entity with updated data.
     * @return bool True on success, false on failure.
     */
    public function updatePost(Post $post): bool
    {
        $data = [
            'title' => $post->getTitle(),
            'body' => $post->getBody(),
        ];
        return parent::update($post->getId(), $data);
    }

    /**
     * Deletes a post from the database.
     *
     * @param Post $post The post entity to delete.
     * @return bool True on success, false on failure.
     */
    public function deletePost(Post $post): bool
    {
        return parent::delete($post->getId());
    }
}
