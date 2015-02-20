<?php
require_once 'Bootstrap.php';

use purity\core\Application as Application;

/**
 * Set environment as Application argument.
 *
 * Application::PRODUCTION
 * Application::DEVELOPMENT
 */
Application::$env = (isset($_SERVER['PURITY_ENV']) ? $_SERVER['PURITY_ENV'] : Application::DEVELOPMENT);

/**
 * Application code below
 */
$app = new Application();

$app->register('purity\core\Providers\DatabaseProvider');
$app->alias('purity\core\Request', 'Request');

$request  = $app->make('Request');
$response = $app->dispatch($request);

$response->send(true);