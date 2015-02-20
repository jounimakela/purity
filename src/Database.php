<?php
/**
 * Database.php
 *
 * Short description for Database class.
 *
 * Longer description for Database class, if any.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 31.10.2014
 *
 * @package
 * @subpackage
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 31.10.2014
 */

namespace purity\core;

class Database extends \PDO implements \Psr\Log\LoggerAwareInterface
{
    /**
     * PDO Instance
     *
     * @var \PDO
     */
    protected $instance = null;

    /**
     * Database adapter
     *
     * @var string
     */
    protected $adapter = null;

    /**
     * Holds database configuration info
     *
     * @var array
     */
    protected $config = array();

    /**
     * Generated DSN string
     *
     * @var string
     */
    protected $dsn = null;

    /**
     * Holds logger instance
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger = null;

    /**
     * Indicates whether queries are being logged.
     *
     * @var bool
     */
    protected $loggingQueries = false;

    /**
     * Class constructor
     *
     * @param string $adapter
     * @param array $config
     * @param bool $log
     */
    public function __construct($adapter, Config $config, $log = false) {
        $this->adapter = $adapter;
        $this->config = $config;
        $this->loggingQueries = $log;

        $this->dsn = $this->generateDSN($adapter, $config);
    }

    public function connect($username = null, $password = null) {
        $username = $username ?: $this->config->username;
        $password = $password ?: $this->config->password;

        $options = array(
            \PDO::ATTR_PERSISTENT    => true,
            \PDO::ATTR_ERRMODE       => \PDO::ERRMODE_EXCEPTION
        );

        $this->instance = new parent($this->dsn, $username, $password, $options);
    }

    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        if (!$this->loggingQueries) {
            return;
        }

        $this->logger = $logger;
    }

    private function generateDSN($adapter, $config) {
        if (!$config->host || !$config->database) {
            throw new InvalidArgumentException("Invalid configuration.");
        }

        $host    = $config->host;
        $db      = $config->database;

        return $adapter . ':host=' . $host . ';dbname=' . $db;
    }

    public function exec($statement) {
        $start = microtime(true);
        $result = $this->instance->exec($statement);

        if ($this->loggingQueries) {
            $this->logger->log(\Psr\Log\LogLevel::INFO, "Statement: '" .
                               $statement . "' executed in " . (microtime(true) - $start));
        }

        return $result;
    }

    public function prepare($statement, $options = array()) {
        $result = $this->instance->prepare($statement, $options);

        if ($result instanceof \PDOStatement) {
            return new Statement($this, $result);
        }

        return $result;
    }
}