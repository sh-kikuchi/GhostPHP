<?php

namespace app\aura\https;

/**
 * Class HttpRequest
 *
 * Represents an HTTP request and provides access
 * to request path, method, and input data.
 */
class HttpRequest {
    /**
     * Retrieves the request path from the URI.
     *
     * @return string The request path without query parameters.
     */
    public function getPath(): string {
        preg_match(
            '|' . dirname($_SERVER['SCRIPT_NAME']) . '/([\w%/]*)|',
            $_SERVER['REQUEST_URI'],
            $matches
        );

        $path = $matches[1] ?? '';

        $position = strpos(
            $_SERVER['REQUEST_URI'],
            '?'
        );

        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * Retrieves the HTTP request method.
     *
     * @return string The request method in lowercase.
     */
    public function getMethod(): string {
        return strtolower(
            $_SERVER['REQUEST_METHOD']
        );
    }

    /**
     * Determines whether the request content type is JSON.
     *
     * @return bool True if the request is JSON.
     */
    public function isJson(): bool {
        $contentType =
            $_SERVER['CONTENT_TYPE'] ?? '';

        return str_contains(
            $contentType,
            'application/json'
        );
    }

    /**
     * Retrieves all request input data.
     *
     * Supports both JSON API requests and
     * standard form requests.
     *
     * @return array The merged request data.
     */
    public function all(): array {

        // API (JSON)
        if ($this->isJson()) {

            $json = file_get_contents(
                'php://input'
            );

            $jsonData = json_decode(
                $json,
                true
            );

            return \is_array($jsonData)
                ? $jsonData
                : [];
        }

        // WEB (Form)
        return [
            ...$_GET,
            ...$_POST
        ];
    }

    /**
     * Retrieves a single input value by key.
     *
     * @param string $key The input key.
     *
     * @return mixed The input value or null.
     */
    public function input(string $key): mixed {
        return $this->all()[$key]
            ?? null;
    }

    /**
     * Retrieve all uploaded files.
     *
     * Structure matches $_FILES superglobal.
     *
     * @return array
     */
    public function files(): array {
        return $_FILES ?? [];
    }

}