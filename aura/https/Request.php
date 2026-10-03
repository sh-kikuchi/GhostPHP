<?php

namespace app\aura\https;

use app\aura\https\Validator;

/**
 * Class Request
 *
 * Base request class for handling
 * request data and validation.
 */
abstract class Request {
    /**
     * Raw request data payload.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Populate the request object with incoming data.
     *
     * Additionally maps top-level keys to class properties when they exist.
     *
     * @param array<string, mixed> $data Incoming request data
     * @return void
     */
    public function fill(array $data): void {
        $this->data = $data;

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Retrieve all raw request data.
     *
     * @return array<string, mixed>
     */
    public function all(): array {
        return $this->data;
    }

    /**
     * Retrieve a single input value, supporting dot notation for nested arrays
     * (e.g. "user.name" reads $data['user']['name']).
     *
     * @param string $key Input key, optionally dot-separated for nested values
     * @param mixed $default Value returned when the key does not exist
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed {
        $keys = explode('.', $key);

        $value = $this->data;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

        /**
     * Retrieve uploaded file.
     *
     * @param string $key
     * @return array|null
     */
    public function file(string $key): ?array {
        $file = $this->data['_files'][$key] ?? null;

        if (!$file) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $file;
    }

    /**
     * Retrieve all uploaded files.
     *
     * @return array|null Uploaded files keyed by field name (empty array if none)
     */
    public function files(): ?array {
       return $this->data['_files'] ?? [];
    }

    /**
     * Execute validation rules and return validation errors.
     *
     * @return array<string, mixed> Validation errors (empty if valid)
     */
    public function validate(): array
    {
        $validator = new Validator();

        $this->rules($validator);

        return $validator->getErrors();
    }

    /**
     * Define validation rules for the request.
     *
     * This method must be implemented in child classes.
     *
     * @param Validator $validator
     * @return void
     */
    abstract protected function rules(Validator $validator): void;
}