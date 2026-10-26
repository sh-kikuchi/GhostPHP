<?php

namespace app\aura\routes;

use app\aura\https\HttpRequest;

/**
 * Class Router
 *
 * Handles HTTP routing and dispatches
 * requests to the appropriate service methods.
 */
class Router {
    /**
     * Current HTTP request instance.
     */
    public HttpRequest $httpRequest;

    /**
     * Registered route definitions.
     */
    protected array $routes = [];

    /**
     * Router constructor.
     *
     * @param HttpRequest $httpRequest The HTTP request instance.
     */
    public function __construct(
        HttpRequest $httpRequest
    ) {
        $this->httpRequest = $httpRequest;
    }

    /**
     * Registers a GET route.
     */
    public function get(
        string $path,
        mixed $callback
    ): void {

        $this->routes['get'][$path] = $callback;
    }

    /**
     * Registers a POST route.
     */
    public function post(
        string $path,
        mixed $callback
    ): void {

        $this->routes['post'][$path] = $callback;
    }

    /**
     * Registers a PUT route.
     */
    public function put(
        string $path,
        mixed $callback
    ): void {

        $this->routes['put'][$path] = $callback;
    }

    /**
     * Registers a PATCH route.
     */
    public function patch(
        string $path,
        mixed $callback
    ): void {

        $this->routes['patch'][$path] = $callback;
    }

    /**
     * Registers a DELETE route.
     */
    public function delete(
        string $path,
        mixed $callback
    ): void {

        $this->routes['delete'][$path] = $callback;
    }
    
    /**
     * Returns the HTTP methods registered for the given path.
     *
     * @param string $path
     * @return string[] Lowercase method names, e.g. ['get', 'post'].
     */
    private function allowedMethods(string $path): array {
        $methods = [];

        foreach ($this->routes as $method => $paths){
            if(isset($paths[$path])){
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * Creates an instance and automatically resolves constructor dependencies.
     *
     * @param string $className
     * @return object
     */
    private function make(string $className): object {
        $reflection = new \ReflectionClass($className);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {

            if ($parameter->allowsNull()) {
                $dependencies[] = null;
                continue;
            }

            $type = $parameter->getType();

            if (
                !$type instanceof \ReflectionNamedType ||
                $type->isBuiltin()
            ) {
                continue;
            }

            $dependencies[] = $this->make(
                $type->getName()
            );
        }

        return $reflection->newInstanceArgs(
            $dependencies
        );
    }

    /**
     * Resolves the current request.
     */
    public function resolve(): void
    {
        $path = $this->httpRequest->getPath();

        $httpMethod = $this->httpRequest->getMethod();

        // Handle HEAD with the GET route (the body is discarded by PHP/Apache)
        if ($httpMethod === 'head') {
            $httpMethod = 'get';
        }

        $callback =
            $this->routes[$httpMethod][$path]
            ?? false;

        if ($callback === false) {

            $allowed = $this->allowedMethods($path);

            // The path exists but not for this method -> 405
            if ($allowed !== []) {

                http_response_code(405);

                header(
                    'Allow: ' . strtoupper(implode(', ', $allowed))
                );

                echo 'Method Not Allowed';

                return;
            }

            http_response_code(404);

            echo 'Not found';

            return;
        }

        if (\is_array($callback)) {

            [$className, $methodName] = $callback;

            $service = $this->make($className);

            $reflection =
                new \ReflectionMethod(
                    $className,
                    $methodName
                );

            $args = [];

            foreach (
                $reflection->getParameters()
                as $parameter
            ) {

                $type = $parameter->getType();

                if (!$type) {
                    continue;
                }

                if (
                    $type instanceof \ReflectionNamedType
                    && $type->isBuiltin()
                ) {

                    $args[] =
                        $this->httpRequest->input(
                            $parameter->getName()
                        );

                    continue;
                }

                $parameterClassName =
                    $type->getName();

                $instance =
                    new $parameterClassName();

                if ($instance) {

                    $inputData =
                        $this->httpRequest->all();

                    $inputData['_files'] =
                        $this->httpRequest->files();

                    $instance->fill(
                        $inputData
                    );

                    $errorMessages =
                        $instance->validate();

                    if (!empty($errorMessages)) {

                        if ($this->httpRequest->isJson()) {

                            http_response_code(400);

                            header(
                                'Content-Type: application/json'
                            );

                            echo json_encode([
                                'errorMessages' => $errorMessages
                            ]);

                            return;
                        }

                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        $_SESSION['ERROR_MESSAGES'] = $errorMessages;

                        header(
                            'Location: ' .
                            $_SERVER['HTTP_REFERER']
                        );

                        return;
                    }
                }

                $args[] = $instance;
            }

            echo $service->$methodName(
                ...$args
            );

            return;
        }

        echo \call_user_func($callback);
    }
}