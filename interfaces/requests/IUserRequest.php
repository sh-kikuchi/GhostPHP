<?php

namespace app\requests;

/**
 * Interface IUserRequest
 *
 * Defines the contract for UserRequest classes.
 */
interface IUserRequest {
    public function setId($id);
    public function setName($name);
    public function setEmail($email);
    public function setPassword($password);
    public function setPasswordConf($password_conf);
    public function setCsrfToken($csrf_token);

    public function getId();
    public function getName();
    public function getEmail();
    public function getPassword();
    public function getPasswordConf();
    public function getCsrfToken();

    public function getArrayData();

    public function signInValidation();
    public function signUpValidation();
}
