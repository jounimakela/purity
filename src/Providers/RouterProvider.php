<?php
/**
 * RouterProvider.php
 *
 * Short description for RouterProvider class.
 *
 * Longer description for RouterProvider class, if any.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 30.10.2014
 *
 * @package
 * @subpackage
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 30.10.2014
 */

namespace purity\core\Providers;

class RouterProvider extends \purity\core\ServiceProvider
{
    public function register() {
	    $router = new \AltoRouter();
	    $config = $this->app['Config'];

	    $router->setBasePath($config->base_path);
	    $router->addRoutes(include APP_PATH . 'config/Routes.php');

	    $this->app->instance('AltoRouter', $router);
    }
}