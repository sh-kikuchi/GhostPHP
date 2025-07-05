<?php
namespace app\form_classes;

use app\aura\utils\Session;
use app\aura\utils\Validator;
use app\aura\https\Redirect;

require_once 'interfaces\form_classes\IUserRequest.php';

/**
 * Class UserRequest
 * 
 * This class handles user request data and validation for sign-in and sign-up processes.
 */
class UserRequest implements IUserRequest {
    protected int $id;
    protected string $name;
    protected string $email;
    protected string $password;
    protected string $password_conf;
    protected string $csrf_token;

    /**
     * Constructor to initialize user request data.
     *
     * @param array|null $data Associative array of user data.
     */
    function __construct(?array $data) {
        $this->id            = $data['id']            ?? 0;
        $this->name          = $data['name']          ?? '';
        $this->email         = $data['email']         ?? '';
        $this->password      = $data['password']      ?? '';
        $this->password_conf = $data['password_conf'] ?? '';
        $this->csrf_token    = $data['csrf_token']    ?? '';
    }

    /**
     * Set the ID.
     *
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Set the name.
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Set the email.
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Set the password.
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Set the password confirmation.
     *
     * @param string $password_conf
     */
    public function setPasswordConf($password_conf) {
        $this->password_conf = $password_conf;
    }

    /**
     * Set the CSRF token.
     *
     * @param string $csrf_token
     */
    public function setCsrfToken($csrf_token) {
        $this->csrf_token = $csrf_token;
    }

    /**
     * Get the ID.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the email.
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Get the password confirmation.
     *
     * @return string
     */
    public function getPasswordConf() {
        return $this->password_conf;
    }

    /**
     * Get the CSRF token.
     *
     * @return string
     */
    public function getCsrfToken() {
        return $this->csrf_token;
    }

    /**
     * Get an associative array of the user request data.
     *
     * @return array
     */
    public function getArrayData() {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'password'      => $this->password,
            'password_conf' => $this->password_conf,
            'csrf_token'    => $this->csrf_token
        ];
    }

    /**
     * Validate POST data for sign-in.
     *
     * @return array The validated data array.
     */
    public function signInValidation() {
        unset($_SESSION['errors']);
        $validator = new Validator;
        $session = new Session;

        $errors = [];
        $email = $this->email;
        $password = $this->password;
        $csrf_token = $this->csrf_token;

        $validator->required($email, 'email');
        $validator->required($password, 'password');

        $errors = $validator->getErrors();

        if ($errors !== [] && count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            Redirect::to('signin');
            exit();
        }

        return $this->getArrayData();
    }

    /**
     * Validate POST data for sign-up.
     *
     * @return array The validated data array.
     */
    public function signUpValidation() {
        unset($_SESSION['errors']);
        $validator = new Validator;
        $session = new Session;

        $errors = [];

        $name = $this->name;
        $email = $this->email;
        $password = $this->password;
        $password_conf = $this->password_conf;
        $csrf_token = $this->csrf_token;

        $validator->required($name, 'name');
        $validator->required($email, 'email');
        $validator->mailFormat($email, 'email');
        $validator->passwordFormat($password, 'password');
        $validator->passwordConfirm($password, $password_conf);

        $errors = $validator->getErrors();

        if ($errors !== [] && count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $session->oldPostValue($this->getArrayData());
            Redirect::to('signup');
        }

        return $this->getArrayData();
    }
}
?>
