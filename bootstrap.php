<?php

use app\aura\AutoloadManager;

/**
 * Application Bootstrap
 *
 * Initializes the application environment and core infrastructure.
 *
 * Responsibilities:
 * - Initialize and configure the autoloader
 * - Load environment variables from the .env file
 * - Register application directories for autoloading
 *
 * Execution Flow:
 * 1. Instantiate the AutoloadManager
 * 2. Load environment variables from .env
 * 3. Register directories (utils, interfaces, config) for autoloading
 * 4. Activate the autoloader
 *
 * Notes:
 * - The .env file must be located at the project root
 * - Directories should be registered using absolute paths
 * - This file is intended to run at application startup
 */

// Initialize autoloader
$autoloader = new AutoloadManager();

// Load environment variables (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Register directories for autoloading
$autoloader->registerDir(__DIR__ . '/aura/utils/functions');
$autoloader->registerDir(__DIR__ . '/interfaces/requests');
$autoloader->registerDir(__DIR__ . '/interfaces/repositories');
$autoloader->registerDir(__DIR__ . '/interfaces/services');
$autoloader->registerDir(__DIR__ . '/config');

// Activate autoloader
$autoloader->autoload();