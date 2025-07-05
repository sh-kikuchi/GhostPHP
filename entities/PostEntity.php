<?php

namespace app\entities;

/**
 * Class PostEntity
 * 
 * Represents a Post entity with properties and methods to get and set those properties.
 */
class PostEntity {
    /**
     * @var int The ID of the post.
     */
    private $id;

    /**
     * @var int The ID of the user who created the post.
     */
    private $userId;

    /**
     * @var string The title of the post.
     */
    private $title;

    /**
     * @var string The body content of the post.
     */
    private $body;

    /**
     * @var string The timestamp when the post was created.
     */
    private $created_at;

    /**
     * @var string The timestamp when the post was last updated.
     */
    private $updated_at;

    /**
     * Get the ID of the post.
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the user ID of the post.
     * 
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Get the title of the post.
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the body content of the post.
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the creation timestamp of the post.
     * 
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Get the last update timestamp of the post.
     * 
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set the ID of the post.
     * 
     * @param int $id
     * @return void
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * Set the user ID of the post.
     * 
     * @param int $user_id
     * @return void
     */
    public function setUserId(int $user_id)
    {
        $this->userId = $user_id;
    }

    /**
     * Set the title of the post.
     * 
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Set the body content of the post.
     * 
     * @param string $body
     * @return void
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * Set the creation timestamp of the post.
     * 
     * @param string $created_at
     * @return void
     */
    public function setCreatedAt(string $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * Set the last update timestamp of the post.
     * 
     * @param string $updated_at
     * @return void
     */
    public function setUpdatedAt(string $updated_at)
    {
        $this->updated_at = $updated_at;
    }
}
