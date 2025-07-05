<?php

namespace app\entities;

/**
 * Class UserEntity
 * 
 * Represents a User entity with properties and methods to get and set those properties.
 */
class UserEntity {
    /**
     * @var int The ID of the user.
     */
    private $id;

    /**
     * @var string The name of the user.
     */
    private $name;

    /**
     * @var string The email of the user.
     */
    private $email;

    /**
     * @var string The password of the user.
     */
    private $password;

    /**
     * @var string The timestamp when the user was created.
     */
    private $created_at;

    /**
     * @var string The timestamp when the user was last updated.
     */
    private $updated_at;

    /**
     * Get the ID of the user.
     * 
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the name of the user.
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the email of the user.
     * 
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Get the password of the user.
     * 
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Get the creation timestamp of the user.
     * 
     * @return string
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Get the last update timestamp of the user.
     * 
     * @return string
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set the ID of the user.
     * 
     * @param int $id
     * @return void
     */
    public function setId(int $id) {
        $this->id = $id;
    }

    /**
     * Set the name of the user.
     * 
     * @param string $name
     * @return void
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * Set the email of the user.
     * 
     * @param string $email
     * @return void
     */
    public function setEmail(string $email) {
        $this->email = $email;
    }

    /**
     * Set the password of the user.
     * 
     * @param string $password
     * @return void
     */
    public function setPassword(string $password) {
        $this->password = $password;
    }

    /**
     * Set the creation timestamp of the user.
     * 
     * @param string $created_at
     * @return void
     */
    public function setCreatedAt(string $created_at) {
        $this->created_at = $created_at;
    }

    /**
     * Set the last update timestamp of the user.
     * 
     * @param string $updated_at
     * @return void
     */
    public function setUpdatedAt(string $updated_at) {
        $this->updated_at = $updated_at;
    }
}
