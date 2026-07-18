<?php
namespace app\requests;

use app\aura\https\Request;
use app\aura\https\Validator;
use app\aura\utils\Session;

/**
 * Class UserRequest
 * 
 * This class handles user request data and validation for sign-in and sign-up processes.
 */
class UserRequest extends Request {
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $password_conf;

    // リスト一覧
    public array $user = [];

    protected function rules(Validator $validator): void {
        $validator->required($this->email, 'email');
        $validator->mailFormat($this->email, 'email');
        $validator->passwordFormat($this->password, 'password');
        
        if (!empty($this->name)) {
            $validator->required($this->name,'name');
        }

        if (!empty($this->password_conf)) {
            $validator->passwordConfirm($this->password, $this->password_conf );
        }

        if (!empty($_SESSION['ERROR_MESSAGES'])) {
            $session   = new Session;

            $session->oldPostValue($this->all());

        }
    }
}   