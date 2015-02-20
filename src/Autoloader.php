<?php

/**
 * autoloader.php
 *
 * PSR-4 standard class autoloader.
 *
 * Autoloader to load classes. This autoloader tries to follow PSR-4 standards.
 * Read more about PSR-4 standard at http://www.php-fig.org/psr/psr-4/
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

class Autoloader {

    /**
     * Holds an array of registered namespaces
     *
     * @var array
     */
    private $namespaces = array();

    /**
     * Registers a single namespaces to path.
     *
     * @param string $namespace
     * @param string $path
     */
    public function registerNamespace($namespace, $path) {
        $this->namespaces[$namespace] = $path;
    }

    /**
     * Registers multiple namespace-path pairs.
     *
     * @param array $namespaces
     */
    public function registerNamespaces($namespaces) {
        $this->namespaces = $namespaces + $this->namespaces;
    }

    /**
     * Gets registered namespaces.
     *
     * @return array A hash with namespaces as keys and directories as values.
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class to be loaded.
     */
    public function loadClass($class) {
        if (false !== $pos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $pos);
            $classname = substr($class, $pos + 1);
            $subdir = '';

            foreach ($this->namespaces as $ns => $path) {
                if (strpos($namespace, $ns) === 0) {
                    $subdir = substr($namespace, strlen($ns));
                    break;
                }
            }

            // Make sure last character is DIRECTORY_SEPARATOR
            if(substr($path, -1) !== DS) {
                $path = $path . DS;
            }

            // If subdir
            if(!empty($subdir) && substr($subdir, -1) !== DS) {
                $subdir = ltrim($subdir . DS, '\\');
                $subdir = str_replace('\\', DS, $subdir);
            }

            $file = $path . $subdir . $classname . '.php';

            if (is_file($file)) {
                require $file;
            }
        }
    }

    /**
     * Registers this instance as an autoloader.
     */
    public function register() {
        spl_autoload_register(array($this, 'loadClass'), true);
    }

}