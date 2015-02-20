<?php
/**
 * Request.php
 *
 * The Request class is used to create and manage new and existing requests.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 25.10.2014
 *
 * @package purity
 * @subpackage core
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 25.10.2014
 * @todo Abstract Request-class more. SPR principal. Make purity\core\Request
 *       not dependency of Router-class. Make a dispatcher to take care of
 *       controller dispatching.
 */

namespace purity\core;

use purity\core\Exceptions\HttpNotFoundException as HttpNotFoundException;

class Request
{
    /**
     * Router-class takes care of Request routing.
     *
     * @var object
     */
    public $router = null;

    /**
     * Holds the response object of the request.
     *
     * @var purity\core\Response
     */
    public $response = null;

    /**
     * Request URI
     *
     * @var string
     */
    private $uri = null;

    /**
     * Applications base path.
     *
     * @var string
     */
    private $base_path = '/';

    /**
     * Request method, usually grabbed from $_SERVER['REQUEST_METHOD']
     *
     * @var string
     */
    private $http_method = null;

    /**
     * Match object from router.
     *
     * @var object
     */
    private $match = null;

    /**
     * Request controller. Default is home/index.
     *
     * @var mixed
     */
    public $controller = 'home/index';

    /**
     * Controller instance once instantiated
     *
     * @var purity\core\Controller
     */
    public $controller_instance = null;

    /**
     * Request's controller action.
     *
     * @var string
     */
    public $action = 'index';

    /**
     * Request's method parameters.
     *
     * @var array
     */
    public $params = array();

    /**
     * Class constructor. Router instance has to be passed to object.
     *
     * @param object $router
     * @param string $uri
     * @param string $method
     * @return
     */
    public function __construct(\AltoRouter $router, Config $config, \Katzgrau\KLogger\Logger $logger) {
        $this->router = $router;
        $this->config = $config;
        $this->logger = $logger;
        
        $this->base_path   = $config->base_path;
        $uri               = $_SERVER['REQUEST_URI'];
        $this->uri         = $this->uriTrim($uri);
        $this->http_method = $_SERVER['REQUEST_METHOD'];

        // Find match for given request
        $this->match = $this->router->match($this->uri);

        if (!$this->match) {
            throw new HttpNotFoundException('Route not found!');
        }

        $this->controller = $this->setController($this->match['target']);
        $this->params     = $this->setParams($this->match['params']);
        $this->action     = $this->setAction($this->match['params']);
    }

    /**
     * Sets the controller for current request. Returns namespaced controller.
     *
     * @param srting $target
     * @return string
     * @todo Make function not depend of this projects namespacing
     */
    private function setController($target) {
        $controller = str_replace('/', '\\', $target);
        $controller = 'purity\\app\\controllers\\' . $controller;
        
        if (!class_exists($controller)) {
            throw new HttpNotFoundException('Class not found');
        }

        $class = new \ReflectionClass($controller);

        if ($class->isAbstract()) {
            throw new HttpNotFoundException('Class is abstract');
        }

        $this->controller_instance = $class;

        return $controller;
    }

    /**
     * Sets the action method for current request taken from $action['action'].
     * Default is index.
     *
     * @param array $action
     * @return string
     */
    private function setAction($action) {
        $action = isset($action['action']) ? $action['action'] : 'index';
        $class = $this->controller_instance;
        $method = $this->http_method . '_' . $action;

        if (!$class->hasMethod($method)) {
            $method = strtolower($this->http_method) . '_' . $action;

            if (!$class->hasMethod($method)) {
                $method = $action;
            }
        }

        if ($class->hasMethod($method)) {
            $action = $method;
            $method = $class->getMethod($method);

            if (!$method->isPublic()) {
                throw new HttpNotFoundException('Method is not public');
            }

            if (count($this->params) < $method->getNumberOfRequiredParameters()) {
                throw new HttpNotFoundException('Invalid number of parameters');
            }
        }

        return $action;
    }

    /**
     * Set action method parameters from $params['params'].
     *
     * @param array $params
     * @return array
     */
    private function setParams(array $params) {
        if (isset($params['action'])) {
            unset($params['action']);
        }

        if (isset($params['params'])) {
            return explode('/', $params['params']);
        }
        
        return array();
    }

    /**
     * Uses router's generate-method to generate routes by names.
     *
     * @param string $name
     * @param array $params
     * @return string
     * @deprecated
     */
    public function generate($name, array $params = array()) {
        return $this->router->generate($name, $params);
    }

    /**
     * Trims trailing slash to make sure our routes are compitable.
     *
     * @param string $uri
     * @return string
     */
    public function uriTrim($uri) {
        if ($uri !== $this->base_path && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }
}