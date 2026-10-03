<?php

namespace app\config;

/**
 * Class MessagesEn
 *
 * English validation messages for app\aura\https\Validator.
 */
class MessagesEn {
    const VALIDATOR = [
        'MAIL_FORMAT'               => 'Invalid email address',
        'REQUIRED'                  => ':field is required',
        'HAS_FILE'                  => ':field is required',
        'PASSWORD_FORMAT'           => 'The password must be at least 8 alphanumeric characters and no more than 100 characters.',
        'PASSWORD_CONFIRM'          => 'Password and confirmation password do not match.',
        'VALIDATE_STRING_REQUIRED'  => ':field is required',
        'MIN_LENGTH'                => 'Minimum length is :min characters',
        'MAX_LENGTH'                => 'Maximum length is :max characters',
        'NUMERIC'                   => ':field must be a number',
        'INTEGER'                   => ':field must be an integer',
        'MIN'                       => 'Minimum value is :min',
        'MAX'                       => 'Maximum value is :max',
        'BETWEEN'                   => 'Value must be between :min and :max',
        'GREATER_THAN_OR_EQUAL'     => ':field must be greater than or equal to :compare_field',
        'SIGNIN_FAILED'             => 'The email address or password is incorrect.',
    ];
}
