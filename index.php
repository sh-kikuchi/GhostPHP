<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

use app\aura\App;
use app\controllers\UserController;
use app\controllers\PostController;

/**
 * Application Entry Point
 *
 * This file serves as the front controller of the application.
 * It is responsible for:
 *
 * - Bootstrapping the application environment
 * - Defining all HTTP routes
 * - Mapping routes to controller layer actions
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
$app->router->get('signin',  [UserController::class, 'showSignInForm']);
$app->router->get('signup',  [UserController::class, 'showSignUpForm']);
$app->router->get('index',   [UserController::class, 'myPage']);
$app->router->get('complete',[UserController::class, 'complete']);

$app->router->post('signin',  [UserController::class, 'signin']);
$app->router->post('signup',  [UserController::class, 'signup']);
$app->router->post('signout', [UserController::class, 'signout']);
$app->router->post('mail',    [UserController::class, 'mail']);
$app->router->post('upload',  [UserController::class, 'upload']);
$app->router->post('pdf',     [UserController::class, 'pdf']);

/**
 * ----------------------------------------
 * Post Routes
 * ----------------------------------------
 */
$app->router->get('post',        [PostController::class, 'index']);
$app->router->get('post/create', [PostController::class, 'showCreateForm']);
$app->router->get('post/update', [PostController::class, 'showUpdateForm']);
$app->router->post('post/create',[PostController::class, 'create']);
$app->router->post('post/update',[PostController::class, 'update']);
$app->router->post('post/delete',[PostController::class, 'delete']);

/**
 * ----------------------------------------
 * Run Application
 * ----------------------------------------
 *
 * Dispatches the incoming HTTP request
 * and executes the matched route handler.
 */
$app->run();