<?php

namespace app\aura\https;

/**
 * Class Request
 *
 * Represents an HTTP request, providing methods to retrieve the request path and method.
 */
class Request {
    /** @var Router $router The router instance. */
    public Router $router;

    /**
     * Retrieves the request path from the URI.
     *
     * @return string The request path without query parameters.
     */
    public function getPath() {
        preg_match('|' . dirname($_SERVER['SCRIPT_NAME']) . '/([\w%/]*)|', $_SERVER['REQUEST_URI'], $matches);
        $path     = $matches[1];
        $position = strpos($_SERVER['REQUEST_URI'], '?');

        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * Retrieves the request method (GET, POST, etc.).
     *
     * @return string The request method in lowercase.
     */
    public function getMethod() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}
