<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

use app\aura\App;
use app\services\UserService;
use app\services\PostService;

/**
 * Application Entry Point
 *
 * This file serves as the front controller of the application.
 * It is responsible for:
 *
 * - Bootstrapping the application environment
 * - Defining all HTTP routes
 * - Mapping routes to service layer actions
 * - Executing the application lifecycle
 *

 *
 * Execution Flow:
 * 1. Load Composer autoloader
 * 2. Load application bootstrap (environment, autoloading, config)
 * 3. Instantiate the application (App)
 * 4. Define routes using the router
 * 5. Dispatch the request via $app->run()
 *
 * Notes:
 * - Each route uses a closure to delegate execution to a service
 * - Templates are directly included for simple view rendering
 * - This structure is suitable for lightweight or custom frameworks
 */

// Initialize application
$app = new App();

/**
 * ----------------------------------------
 * Basic Routes
 * ----------------------------------------
 */
$app->router->get('', function () { include "templates/welcome.php"; });
$app->router->get('error', function () { include "templates/errors/error.php";});

/**
 * ----------------------------------------
 * User Routes
 * ----------------------------------------
 */
$app->router->get('signin',  function () {(new UserService)->showSignInForm();});
$app->router->get('signup',  function () {(new UserService)->showSignUpForm();});
$app->router->get('index', function () {(new UserService)->myPage();});
$app->router->get('complete', function () {(new UserService)->complete();});
$app->router->post('signin', function () {(new UserService)->signin();});
$app->router->post('signup', function () {(new UserService)->signup();});
$app->router->post('signout',function () {(new UserService)->signout();});
$app->router->post('mail',   function () {(new UserService)->mail();});
$app->router->post('upload', function () {(new UserService)->upload();});
$app->router->post('pdf',    function () {(new UserService)->pdf();});

/**
 * ----------------------------------------
 * Post Routes
 * ----------------------------------------
 */
$app->router->get('post',         function () { (new PostService)->index(); });
$app->router->get('post/create',  function () { (new PostService)->showCreateForm(); });
$app->router->get('post/update',  function () { (new PostService)->showUpdateForm();  });
$app->router->post('post/create', function () { (new PostService)->create(); });
$app->router->post('post/update', function () { (new PostService)->update(); });
$app->router->post('post/delete', function () { (new PostService)->delete();  });

/**
 * ----------------------------------------
 * Run Application
 * ----------------------------------------
 *
 * Dispatches the incoming HTTP request
 * and executes the matched route handler.
 */
$app->run();