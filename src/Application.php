<?php

/**
 * Application.php
 *
 * Application class is the front controller of the application.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 23.10.2014
 *
 * @package purity
 * @subpackage core
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 23.10.2014
 */

namespace purity\core;

use Illuminate\Container\Container as Container;
use exceptions\PhpErrorException as PhpErrorException;

class Application extends Container
{
    /**
     * Constant used for when in development mode
     *
     * @var string
     */
    const DEVELOPMENT = 'development';

    /**
     * Constant used for when in production mode
     *
     * @var string
     */
    const PRODUCTION = 'production';

    /**
     * Environment
     */
    public static $env = Application::DEVELOPMENT;

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = array();

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = array();

    /**
     * Timezone
     *
     * @var string
     */
    private $timezone = 'UTC';

    /**
     * Locale
     *
     * @var string
     */
    private $locale = 'en_US';

    /**
     * Class constructor. Takes care of application initialization.
     */
    public function __construct() {
       if (static::$env == static::PRODUCTION) {
            ini_set('display_errors', 0);
        } elseif (static::$env == static::DEVELOPMENT) {
            ini_set('display_errors', 1);
        } else {
            throw new PhpErrorException("Invalid environment");
        }

        $config = new Config(include APP_PATH . 'config/App.php');

        // Register config
        $this->instance('purity\core\Config', $config);
        $this->alias('purity\core\Config', 'Config');

        // Register logger and router
        $this->register('purity\core\Providers\LoggerProvider');
        $this->register('purity\core\Providers\RouterProvider');

        // Register application
        $this->instance('purity\core\Application', $this);

        // Create session
        $this->singleton('purity\core\Session');

        // Set timezone
        try {
            $this->timezone = $config->timezone ? $config->timezone : date_default_timezone_get();
            date_default_timezone_set($this->timezone);
        } catch (\Exception $e) {
            date_default_timezone_set('UTC');
            throw new PhpErrorException($e->getMessage());
        }

        // Set locale
        $this->locale = ($config->locale) ? $config->locale : $this->locale;
        setlocale(LC_ALL, $this->locale); // Log if setlocale fails!
    }

    public function register($provider, $force = false) {
        if ($registered = $this->getRegistered($provider) && !$force) {
            return $registered;
        }

        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        $provider->register();

        return $provider;
    }

    public function getRegistered($provider) {
        $name = is_string($provider) ? $provider : get_class($provider);

        if (array_key_exists($name, $this->loadedProviders)) {
            return array_first($this->serviceProviders, function($key, $value) use ($name) {
                    return get_class($value) == $name;
            });
        }
    }

    public function dispatch(Request $request, $params = array()) {
        if (is_array($params)) {
            $params = array_merge($request->params, $params);
        }

        $class = $this->make($request->controller);
        $action = $request->action;

        $response = call_user_func(array($class, $action), $params);

        if ($response instanceof Response) {
            $request->response = $response;
        } else {
            // Changed from get_class($controller_instance)
            throw new \Exception($request->controller . '::'
                                 . $action . '() must return a Response object!');
        }

        // Stop and save session
        $session = $this->make('purity\core\Session');
        $session->stop();

        return $response;
    }
}