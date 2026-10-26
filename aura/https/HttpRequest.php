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
     * HTTP methods that a POST request may be overridden to via "_method".
     */
    private const OVERRIDABLE_METHODS = ['put', 'patch', 'delete'];

    /**
     * Cached form-encoded body for PUT / PATCH / DELETE requests.
     */
    private ?array $parsedBody = null;

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
        $method = strtolower(
            $_SERVER['REQUEST_METHOD'] ?? 'get'
        );

        if($method !== 'post'){
            return $method;
        }

        $override = strtolower(
            (string)($_POST['_method'] ?? '')  
        );

        return \in_array($override, self::OVERRIDABLE_METHODS, true)
        ? $override
        : $method;

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
            ...$_POST,
            ...$this->parsedBody()
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

    /**
     * Retrieves the request body only, without query string parameters.
     *
     * Use this for values that must not be accepted from the URL,
     * such as CSRF tokens.
     *
     * @return array
     */
    public function body(): array {
        if ($this->isJson()) {
            return $this->all();
        }

        return [
            ...$_POST,
            ...$this->parsedBody()
        ];
    }

    /**
     * Parses a form-encoded body sent with PUT / PATCH / DELETE.
     *
     * PHP only populates $_POST for real POST requests, so the body of
     * other methods has to be read from php://input.
     *
     * @return array
     */
    private function parsedBody(): array {
        if ($this->parsedBody !== null) {
            return $this->parsedBody;
        }

        $this->parsedBody = [];

        $realMethod = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (
            \in_array($realMethod, self::OVERRIDABLE_METHODS, true)
            && str_contains($contentType, 'application/x-www-form-urlencoded')
        ) {
            parse_str(
                file_get_contents('php://input'),
                $this->parsedBody
            );
        }

        return $this->parsedBody;
    }

}