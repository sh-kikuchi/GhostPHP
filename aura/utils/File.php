<?php

namespace app\aura\utils;

use app\config\Message;
use app\aura\https\Redirect;

class File {
    /**
     * uploaded_file
     * @param array $file_data 
     * @return boolean $result
     */
    function uploadFile($file_data) {
      $result = false;
      if (empty($file_data['upfile']['full_path'])) {
          $_SESSION['msg'] = 'No files have been uploaded.';
          Redirect::to('index');
          exit();
      } 
  
      if ($file_data['upfile']['error'] !== UPLOAD_ERR_OK) {
          $msg = [
              UPLOAD_ERR_INI_SIZE   => Message::UPLOAD_ERR['INT_SIZE'],
              UPLOAD_ERR_PARTIAL    => Message::UPLOAD_ERR['PARTIAL'],
              UPLOAD_ERR_NO_FILE    => Message::UPLOAD_ERR['NO_FILE'],
              UPLOAD_ERR_NO_TMP_DIR => Message::UPLOAD_ERR['NO_TMP_DIR'],
              UPLOAD_ERR_CANT_WRITE => Message::UPLOAD_ERR['CANT_WRITE'],
              UPLOAD_ERR_EXTENSION  => Message::UPLOAD_ERR['EXTENSION'],
          ];
          $err_msg = $msg[$file_data['upfile']['error']] ?? 'Unknown upload error.';
      } else {
          // 拡張子チェック
          $extension = strtolower(pathinfo($file_data['upfile']['name'], PATHINFO_EXTENSION));
          $allowed_extensions = ['gif', 'jpg', 'jpeg', 'png'];
  
          if (!in_array($extension, $allowed_extensions)) {
              $err_msg = Message::UPLOAD_ERR['NOT_IMAGE'];
          } else {
              $src  = $file_data['upfile']['tmp_name']; // temporary file path
              $dest = 'storage/' . $file_data['upfile']['name'];
  
              if (!move_uploaded_file($src, $dest)) {
                  $err_msg = Message::UPLOAD_ERR['FAILED'];
              } else {
                  $result = true;
              }
          }
      }
      return $result;
  }
}
?>