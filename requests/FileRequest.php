<?php

namespace app\requests;

use app\aura\https\Request;
use app\aura\utils\Session;
use app\aura\https\Validator;

/**
 * Class UploadRequest
 *
 * Handles file upload request data.
 */
class FileRequest extends Request {
    public ?array $upfile = null;

    /**
     * Validation rules.
     * @return mixed
     */
    protected function rules(Validator $validator): void {
        $_SESSION['ERROR_MESSAGES'] = [];
        $files = $this->input('_files.upfile');
        $validator->hasFile($files, 'files');
    }
}