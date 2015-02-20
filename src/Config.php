<?php
/**
 * Config.php
 *
 * With Config-class you can view and change application configuration.
 *
 * Config-class uses array as configuration input. See app/config/App.php which
 * is provided within this class. Config-class itself is influenced greatly
 * by Zend_Config.
 * By default, configuration data made available through Config are read-only
 * and an assignment results in a thrown exception.
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

class Config
{
    /**
     * Array to hold current configuration.
     *
     * @var array $config
     */
    protected $config = array();

    /**
     * Number of individual keys in current configuration.
     *
     * @var int $count
     */
    protected $count;

    /**
     * Determines if modifications to configuration data are allowed.
     *
     * @var bool $readOnly
     */
    protected $readOnly = true;

    /**
     * Class constructor. Modifications are disallowed by default.
     *
     * @param array $config
     * @param bool $readOnly
     * @throws \Exception
     */
    public function __construct(array $config, $readOnly = true) {
        $this->setReadOnly($readOnly);

        if (is_array($config)) {
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    $this->config[$key] = new static($value, $this->readOnly);
                } else {
                    $this->config[$key] = $value;
                }

                $this->count++;
            }
        } else {
            throw new \Exception();
        }
    }

    /**
     * Magic method. Gets configuration value by key.
     *
     * @param string $name
     * @return mixed Returns key if found, otherwise false.
     */
    public function __get($name) {
        if(array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }

        return false;
    }

    /**
     * Magic method. Sets configuration value by key.
     * Throws an exception if Config is read-only.
     *
     * @param string $name
     * @param string $value
     * @throws \Exception
     */
    public function __set($name, $value) {
        if(!$this->isReadOnly()) {

            if (is_array($value)) {
                $value = new static($value, true);
            }

            if (null === $name) {
                $this->config[] = $value;
            } else {
                $this->config[$name] = $value;
            }

            $this->count++;
        } else {
            throw new \Exception('Configuration is read-only.');
        }
    }

    /**
     * Returns boolean of read-only status.
     *
     * @return bool
     */
    public function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * Sets read-only status.
     *
     * @param bool $value
     * @return bool
     */
    public function setReadOnly($value) {
        $this->readOnly = $value;
        return $this->readOnly;
    }

    /**
     * Returns number of individual keys in current configuration.
     *
     * @return int
     */
    public function count() {
        return $this->count;
    }

    /**
     * Returns an array of current configuration.
     *
     * @return array
     */
    public function toArray() {
        $array = array();
        $data  = $this->config;

        /** @var self $value */
        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}