<?php

namespace app\aura\utils;

class Mail{
    /**
   * sendMail
   * @param array $post_data 
   * @return boolean $result
   */
    function sendMail($post_data){
        $result =  false;
        
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        $to      = $post_data['mail'];
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
          echo "Invalid email address.";
          return;
        }
        $title   = 'Letter from '.$post_data['username'];
        $message = $post_data['comment'];
        $headers =  'From:'. $post_data['mail'];
        
        if(mb_send_mail($to, $title, $message, $headers)){
          $result = true;
          echo "Email successfully sent.";
        }else{
          echo "Email transmission failed.";
        }
    }
}
?>