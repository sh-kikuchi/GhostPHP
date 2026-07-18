<?php

namespace app\requests;

use app\aura\https\Request;
use app\aura\https\Validator;

/**
 * Class UploadRequest
 *
 * Handles file upload request data.
 */
class MailRequest extends Request {
    public string $mail;
    public string $username;
    public string $comment;

    /**
     * Validation rules.
     * @return mixed
     */
  protected function rules(Validator $validator): void{
    $_SESSION['ERROR_MESSAGES'] = [];
    $address = $this->input('mail');
    $validator->mailFormat($address, 'mail');
  }

}