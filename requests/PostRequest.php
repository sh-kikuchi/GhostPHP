<?php

namespace app\requests;

use app\aura\https\Request;
use app\aura\utils\Session;
use app\aura\https\Validator;

/**
 * Class PostRequest
 *
 * Handles post request data and validation for creating and updating posts.
 */
class PostRequest extends Request{

    public int $id;
    public int $user_id;
    public string $title;
    public string $body;

    protected function rules(Validator $validator): void {

        $validator->required($this->title, 'title');
        $validator->maxLength($this->title,'title', 100);
       
        if (!empty($_SESSION['ERROR_MESSAGES'])) {
            $session = new Session;
            $session->oldPostValue($this->all());
        }
    }
}
