<?php

use app\aura\AutoloadManager;

$autoloader = new AutoloadManager();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$autoloader->registerDir(dirname(__FILE__).'/aura/utils/functions');
$autoloader->registerDir(dirname(__FILE__).'/interfaces/form_classes');
$autoloader->registerDir(dirname(__FILE__).'/interfaces/repositories');
$autoloader->registerDir(dirname(__FILE__).'/interfaces/services');
$autoloader->registerDir(dirname(__FILE__).'/config');

$autoloader->autoload();




