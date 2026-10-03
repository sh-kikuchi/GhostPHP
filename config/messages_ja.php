<?php

namespace app\config;

/**
 * Class MessagesJa
 *
 * Japanese validation messages for app\aura\https\Validator.
 */
class MessagesJa {
    const VALIDATOR = [
        'MAIL_FORMAT'               => 'メールアドレスの形式が正しくありません。',
        'REQUIRED'                  => ':field は必須項目です。',
        'HAS_FILE'                  => ':field は必須項目です。',
        'PASSWORD_FORMAT'           => 'パスワードは8文字以上100文字以内の半角英数字で入力してください。',
        'PASSWORD_CONFIRM'          => 'パスワードと確認用パスワードが一致しません。',
        'VALIDATE_STRING_REQUIRED'  => ':field は必須項目です。',
        'MIN_LENGTH'                => '最小文字数は:min文字です。',
        'MAX_LENGTH'                => '最大文字数は:max文字です。',
        'NUMERIC'                   => ':field は数値で入力してください。',
        'INTEGER'                   => ':field は整数で入力してください。',
        'MIN'                       => '最小値は:minです。',
        'MAX'                       => '最大値は:maxです。',
        'BETWEEN'                   => ':min から :max の間の値を入力してください。',
        'GREATER_THAN_OR_EQUAL'     => ':field は :compare_field 以上の値を入力してください。',
        'SIGNIN_FAILED'             => 'メールアドレスまたはパスワードが正しくありません。',
    ];
}
