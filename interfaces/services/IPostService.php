<?php

namespace app\services;

use app\entities\PostEntity as Post;

/**
 * Interface IPostService
 *
 * Defines the contract for Post service classes.
 */
interface IPostService {
    public function index();
    public function showCreateForm();
    public function showUpdateForm();
    public function create();
    public function update();
    public function delete();
    public function makePost(array $post_form):Post;
}
