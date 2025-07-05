<?php
namespace app\aura\utils;

/**
 * Class Validator
 * 
 * This class provides various validation methods for validating input data.
 */
class Validator {
    private $errors = [];
    private $customMessages = [];

    /**
     * Set custom error messages for validation rules.
     *
     * @param array $customMessages Associative array of custom messages.
     */
    public function setCustomMessages(array $customMessages) {
        $this->customMessages = $customMessages;
    }

    /**
     * Get all validation errors.
     *
     * @return array Associative array of validation errors.
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Add an error message for a specific field.
     *
     * @param string $field The field name.
     * @param string $message The error message.
     */
    protected function addError($field, $message) {
        $this->errors[$field] = $message;
    }

    /**
     * Get the custom error message for a field if set, otherwise return the default message.
     *
     * @param string $field The field name.
     * @param string $defaultMessage The default error message.
     * @return string The custom or default error message.
     */
    protected function getCustomMessage($field, $defaultMessage) {
        return isset($this->customMessages[$field]) ? $this->customMessages[$field] : $defaultMessage;
    }

    /**
     * Validate email format.
     *
     * @param string $email The email address to validate.
     * @param string $field The field name for the email address.
     * @return bool True if valid, false otherwise.
     */
    public function mailFormat($email, $field = 'email') {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $message = $this->getCustomMessage($field, 'Invalid email address');
            $this->addError($field, $message);
            return false;
        }
        return true;
    }

    /**
     * Check if a value is required and not empty.
     *
     * @param mixed $value The value to check.
     * @param string $field The field name for the value.
     */
    public function required($value, $field = 'value') {
        if (empty($value)) {
            $message = $this->getCustomMessage($field, $field . ' is required');
            $this->addError($field, $message);
        }
    }

    /**
     * Validate password format.
     *
     * @param string $password The password to validate.
     * @param string $field The field name for the password.
     */
    public function passwordFormat($password, $field = 'password') {
        if (!preg_match("/\A[a-z\d]{8,100}+\z/i", $password)) {
            $message = $this->getCustomMessage($field, 'The password must be at least 8 alphanumeric characters and no more than 100 characters.');
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that password and confirmation password match.
     *
     * @param string $password The password.
     * @param string $password_conf The confirmation password.
     */
    public function passwordConfirm($password, $password_conf) {
        if ($password !== $password_conf) {
            $message = $this->getCustomMessage('password_conf', 'Password and confirmation password do not match.');
            $this->addError('password_conf', $message);
        }
    }

    /**
     * Validate a string with optional length constraints.
     *
     * @param string $value The string to validate.
     * @param string $field The field name for the string.
     * @param bool $required Whether the field is required.
     * @param int|null $minLength The minimum length of the string.
     * @param int|null $maxLength The maximum length of the string.
     */
    public function validateString($value, $field = 'input', $required = false, $minLength = null, $maxLength = null) {
        // Check if the field is required and empty
        if ($required && empty($value)) {
            $message = $this->getCustomMessage($field, $field . ' is required');
            $this->addError($field, $message);
        }

        // Check the minimum length of the string
        if ($minLength !== null && mb_strlen($value) < $minLength) {
            $message = $this->getCustomMessage($field, "Minimum length is $minLength characters");
            $this->addError($field, $message);
        }

        // Check the maximum length of the string
        if ($maxLength !== null && mb_strlen($value) > $maxLength) {
            $message = $this->getCustomMessage($field, "Maximum length is $maxLength characters");
            $this->addError($field, $message);
        }
    }
}