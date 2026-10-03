<?php
namespace app\aura\https;

use app\aura\Lang;

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
    protected function addError(string $field, string $message) {
        $this->errors[] = [$field => $message];
    }

    /**
     * Get the custom error message for a field if set, otherwise return the default message.
     *
     * @param string $field The field name.
     * @param string $defaultMessage The default error message.
     * @return string The custom or default error message.
     */
    protected function getCustomMessage(string $field, string $defaultMessage) {
        return $this->customMessages[$field] ?? $defaultMessage;
    }

    /**
     * Validate email format.
     *
     * @param string $email The email address to validate.
     * @param string $field The field name for the email address.
     * @return bool True if valid, false otherwise.
     */
    public function mailFormat(string $email, string $field = 'email') {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $message = $this->getCustomMessage($field, Lang::get('MAIL_FORMAT'));
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
    public function required($value, string $field = 'value') {
        if (empty($value)) {
            $message = $this->getCustomMessage($field, Lang::get('REQUIRED', ['field' => $field]));
            $this->addError($field, $message);
        }
    }

    /**
     * Check whether a file has been uploaded successfully.
     *
     * @param array|null $value The uploaded file data.
     * @param string $field The field name for the file.
     */
    public function hasFile($value, string $field = 'files') {
        $result = isset($value['error'][0])
            && $value['error'][0] === UPLOAD_ERR_OK;
    
        if(!$result){
            $message = $this->getCustomMessage($field, Lang::get('HAS_FILE', ['field' => $field]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate password format.
     *
     * @param string $password The password to validate.
     * @param string $field The field name for the password.
     */
    public function passwordFormat(string $password, string $field = 'password') {
        if (!preg_match("/\A[a-z\d]{8,100}+\z/i", $password)) {
            $message = $this->getCustomMessage($field, Lang::get('PASSWORD_FORMAT'));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that password and confirmation password match.
     *
     * @param string $password The password.
     * @param string $password_conf The confirmation password.
     */
    public function passwordConfirm(string $password, string $password_conf) {
        if ($password !== $password_conf) {
            $message = $this->getCustomMessage('password_conf', Lang::get('PASSWORD_CONFIRM'));
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
    public function validateString(
            string $value, 
            string $field = 'input',
            bool $required = false,
            int|null $minLength = null,
            int|null $maxLength = null
    ) {
        // Check if the field is required and empty
        if ($required && empty($value)) {
            $message = $this->getCustomMessage($field, Lang::get('VALIDATE_STRING_REQUIRED', ['field' => $field]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate a string with optional length constraints.
     *
     * @param string $value The string to validate.
     * @param string $field The field name for the string.
     * @param int|null $min The minimum length of the string.
     */
    public function minLength(string $value, string $field, int $min): void {
        if ($value === null || mb_strlen($value) < $min) {
            $message = $this->getCustomMessage($field, Lang::get('MIN_LENGTH', ['field' => $field, 'min' => $min]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate a string with optional length constraints.
     *
     * @param string $value The string to validate.
     * @param string $field The field name for the string.
     * @param int|null $max The maximum length of the string.
     */
    public function maxLength(string $value, string $field, int $max): void {
        if ($value !== null && mb_strlen($value) > $max) {
            $message = $this->getCustomMessage($field, Lang::get('MAX_LENGTH', ['field' => $field, 'max' => $max]));
            $this->addError($field, $message);
        }
    }
    
    /**
     * Validate that a value is numeric (integers, floats, and numeric strings such as "12", "-3.5").
     *
     * @param mixed $value The value to check.
     * @param string $field The field name for the value.
     */
    public function numeric($value, string $field = 'value'): void {
        if (!is_numeric($value)) {
            $message = $this->getCustomMessage($field, Lang::get('NUMERIC', ['field' => $field]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that a value is an integer (numeric strings without a decimal point are allowed).
     *
     * @param mixed $value The value to check.
     * @param string $field The field name for the value.
     */
    public function integer($value, string $field = 'value'): void {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $message = $this->getCustomMessage($field, Lang::get('INTEGER', ['field' => $field]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that a numeric value is greater than or equal to a minimum.
     *
     * @param mixed $value The value to check.
     * @param string $field The field name for the value.
     * @param int|float $min The minimum allowed value.
     */
    public function min($value, string $field, int|float $min): void {
        if (!is_numeric($value) || (float)$value < $min) {
            $message = $this->getCustomMessage($field, Lang::get('MIN', ['min' => $min]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that a numeric value is less than or equal to a maximum.
     *
     * @param mixed $value The value to check.
     * @param string $field The field name for the value.
     * @param int|float $max The maximum allowed value.
     */
    public function max($value, string $field, int|float $max): void {
        if (!is_numeric($value) || (float)$value > $max) {
            $message = $this->getCustomMessage($field, Lang::get('MAX', ['max' => $max]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that a numeric value is greater than or equal to another field's value
     * (e.g. an end page must not be before its start page).
     *
     * @param mixed $value The value to check (e.g. end_page).
     * @param string $field The field name for the value.
     * @param mixed $compareValue The value it must be greater than or equal to (e.g. start_page).
     * @param string $compareField The field name of the compared value.
     */
    public function greaterThanOrEqual($value, string $field, $compareValue, string $compareField): void {
        if (!is_numeric($value) || !is_numeric($compareValue) || (float)$value < (float)$compareValue) {
            $message = $this->getCustomMessage($field, Lang::get('GREATER_THAN_OR_EQUAL', [
                'field' => $field,
                'compare_field' => $compareField,
            ]));
            $this->addError($field, $message);
        }
    }

    /**
     * Validate that a numeric value falls within an inclusive range.
     *
     * @param mixed $value The value to check.
     * @param string $field The field name for the value.
     * @param int|float $min The minimum allowed value.
     * @param int|float $max The maximum allowed value.
     */
    public function between($value, string $field, int|float $min, int|float $max): void {
        if (!is_numeric($value) || (float)$value < $min || (float)$value > $max) {
            $message = $this->getCustomMessage($field, Lang::get('BETWEEN', ['min' => $min, 'max' => $max]));
            $this->addError($field, $message);
        }
    }

}