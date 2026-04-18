<?php

namespace app\repositories;

use app\requests\PostRequest;
use app\entities\PostEntity as Post;

/**
 * Interface IPostRepository
 *
 * Defines the contract for Post repository classes.
 */
interface IPostRepository {

    public function show(): array;
    public function getPost(int $id): ?array;
    public function createPost(Post $post): bool;
    public function updatePost(Post $post): bool;
    public function deletePost(Post $post): bool;
}
