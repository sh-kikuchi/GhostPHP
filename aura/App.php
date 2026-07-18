<?php

namespace app\aura;

use app\aura\https\HttpRequest;
use app\aura\routes\Router;

/**
 * Class App
 *
 * Main application class that initializes the request and router, and runs the application.
 */
class App {
    public Router  $router;
    public HttpRequest $request;

    /**
     * App constructor.
     *
     * Initializes the HttpRequest and Router instances.
     */
    public function __construct()
    {
        $this->request = new HttpRequest();
        $this->router  = new Router($this->request);
    }

    /**
     * Runs the application by resolving the current request.
     *
     * @return void
     */
    public function run()
    {
        $this->router->resolve();
    }
}
