<?php
/**
 * ServiceProvider.php
 * 
 * Short description for ServiceProvider class.
 * 
 * Longer description for ServiceProvider class, if any.
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

namespace purity\core;

abstract class ServiceProvider
{
    /**
     * The application instance
     *
     * @var purity\core\Application
     */
    protected $app;

    /**
     * Create a new service provider instance.
     *
     * @param purity\core\Application $app
     * @return void
     */
    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register();
}