<?php
/**
 * Controller.php
 *
 * This is the "base" controller class.
 * Other "real" controllers extend this class.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 24.10.2014
 *
 * @package purity
 * @subpackage core
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 24.10.2014
 */

namespace purity\core;

use Katzgrau\KLogger\Logger as Logger;

abstract class Controller
{

    /**
     * Holds database instance
     *
     * @var purity\core\Database
     */
    //protected $database = null;

    /**
     * Holds configuration instance
     *
     * @var purity\core\Config
     */
    //protected $config = null;

    /**
     * Creates read-only database connection.

    protected function databaseConnection() {
        $this->getConfig();

        $adapter   = $this->config->database->adapter;
        $dbConfig  = $this->config->database->read->toArray();
        $logPath   = $this->config->database->log;

        $config = new Config($dbConfig);

        $this->database = new Database($adapter, $config, false);
        $logger = new Logger($logPath);

        $this->database->setLogger($logger);
        $this->database->connect();
    }

     * Creates read-only configuration instance.
     *
     * @return \purity\core\Config

    protected function getConfig() {
        if (is_null($this->config)) {
            $config = new Config(include APP_PATH . 'config/App.php');
            $this->config = $config;
            return $config;
        }

        return $this->config;
    }
*/
    /**
     * All controllers MUST have index action.
     */
    //abstract public function index();

}