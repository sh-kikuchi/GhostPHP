<?php

namespace app\aura\routes;

use app\aura\https\Request;

/**
 * Class Router
 *
 * Handles routing for HTTP requests, mapping paths to their respective callbacks.
 */
class Router {
    public Request $request;
    protected array $routes = [];

    /**
     * Router constructor.
     *
     * @param Request $request The HTTP request instance.
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Registers a GET route.
     *
     * @param string $path The path for the route.
     * @param callable $callback The callback to execute for this route.
     * @return void
     */
    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Registers a PUT route.
     *
     * @param string $path The path for the route.
     * @param callable $callback The callback to execute for this route.
     * @return void
     */
    public function put($path, $callback) {
        $this->routes['put'][$path] = $callback;
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $path The path for the route.
     * @param callable $callback The callback to execute for this route.
     * @return void
     */
    public function delete($path, $callback) {
        $this->routes['delete'][$path] = $callback;
    }

    /**
     * Registers a POST route.
     *
     * @param string $path The path for the route.
     * @param callable $callback The callback to execute for this route.
     * @return void
     */
    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * Resolves the current request to the appropriate route.
     *
     * @return void
     */
    public function resolve() {
        $path   = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        
        if ($callback === false) {
            echo "Not found";
            exit;
        }

        echo call_user_func($callback);
    }
}
