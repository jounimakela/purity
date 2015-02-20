<?php
/**
 * View.php
 *
 * Populates view with given data by binding parameters to view file.
 *
 * This class acts as an object wrapper for HTML pages with embedded PHP,
 * called 'views'. Variables can be assigned with the view object and
 * referenced locally within the view.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 26.10.2014
 *
 * @package purity
 * @subpackage core
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 26.10.2014
 */

namespace purity\core;

class View
{
    /**
     * Holds current views path.
     *
     * @var string $file_name
     */
    protected $file_name = null;

    /**
     * Variable container for current views variables.
     *
     * @var array $data
     */
    protected $data = array();

    /**
     * Class constructor.
     *
     * @param string $file
     * @param array $data
     * @return boolean
     * @throws \Exception
     */
    public function __construct($file = null, $data = null) {
        if ($file !== null) {
            $this->setFilename($file);
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        } elseif ($data && !is_array($data)) {
            throw new \Exception('Data can only be an object or an array!');
        }

        if ($data !== null) {
            $this->data = $data;
        }

        return true;
    }

    /**
     * Magic method proxy for $this->get().
     *
     * @param string $key
     * @return mixed
     */
    public function & __get($key = null) {
        return $this->get($key);
    }

    /**
     * Magic method proxy for $this->set().
     *
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Magic method. Determines if variable has been set.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * Returns variable from current views variable container. If key not set,
     * returns whole array.
     *
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function & get($key = null) {
        if ($key === null) {
            return $this->data;
        } elseif (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            throw new \Exception('Variable is not set!');
        }
    }

    /**
     * Sets value to current views variable container.
     *
     * @param string $key
     * @param string $value
     * @return \purity\core\View $this
     */
    public function set($key, $value) {
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->data[$name] = $value;
            }
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Finds given view file from APP_PATH/views/ and sets it as current
     * view-file. Returns boolean to determine if file was found.
     *
     * @param type $file
     * @return boolean
     */
    public function setFilename($file) {
        $file_path = APP_PATH . 'views/' . $file;

        // Add no .php extension, add it
        if (strrpos($file_path, '.php') !== 0) {
            $file_path = $file_path . '.php';
        }

        if (!is_readable($file_path)) {
            return false;
        }

        $this->file_name = $file_path;
        return true;
    }

    /**
     * Processes view; anonymous function holds clean space to set variables,
     * then function includes file and variables are set. Throws exception
     * if include fails.
     *
     * @return string
     * @throws \Exception
     */
    protected function compileFile() {
        $render = function($__file, array $__data) {
            extract($__data, EXTR_REFS);

            // Capture the view output
            ob_start();

            try {
                include $__file;
            } catch (\Exception $e) {
                // Delete output buffer
                ob_end_clean();

                throw $e;
            }

            return ob_get_clean();
        };

        return $render($this->file_name, $this->get());
    }

    /**
     * Executes rendering of current view. Returns compiled view.
     *
     * @param string $file
     * @return string
     * @throws \Exception
     */
    public function render($file = null) {
        if ($file !== null) {
            $this->setFilename($file);
        }

        if (empty($this->file_name)) {
            throw new \Exception('You must set the file to use within your view before rendering!');
        }

        $return = $this->compileFile();

        return $return;
    }

    /**
     * Magic method. Returns the output of render.
     *
     * @return  string
     * @uses    View::render
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Expcetion $e) {
            Error::exceptionHandler($e);

            return '';
        }
    }
}