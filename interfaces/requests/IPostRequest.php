<?php

namespace app\requests;

/**
 * Interface IPostRequest
 *
 * Defines the contract for PostRequest classes.
 */
interface IPostRequest {
    public function setId($id);
    public function setUserId($user_id);
    public function setTitle($title);
    public function setBody($body);
    
    public function getId();
    public function getUserId();
    public function getTitle();
    public function getBody();
    
    public function getArrayData();
    
    public function validate($data);
}
