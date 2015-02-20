<?php
/**
 * PDOStatement.php
 *
 * Short description for PDOStatement class.
 *
 * Longer description for PDOStatement class, if any.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 8.11.2014
 *
 * @package
 * @subpackage
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 8.11.2014
 */

namespace purity\core;

class Statement
{
    /**
     * Variable bindings.
     *
     * @var array
     */
    private $binds = array();

    /**
     * Holds database instance.
     *
     * @var \purity\core\Database
     */
    private $db = null;

    /**
     * The prepared statement.
     *
     * @var \PDOStatement
     */
    private $statement = null;

    /**
     * Sets database instance and prepared statement.
     *
     * @param \purity\core\Database $db         Database instance
     * @param \PDOStatement         $statement  Original prepared statement
     */

    public function __construct(Database $db, \PDOStatement $statement) {
        $this->db = $db;
        $this->statement = $statement;
    }

    /**
     * Relay all calls.
     *
     * @param string $name      The method name to call
     * @param array  $arguments The arguments for the call
     *
     * @return mixed The call results
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->statement, $name),
                                    $arguments);
    }

    /**
    * Relay all gets.
    *
    * @param string $name The property name.
    *
    * @return mixed The property value.
    */
    public function __get($name) {
        return $this->statement->$name;
    }

    /**
     * @see \PDOStatement::bindParam
     */
    public function bindParam($paramenter, &$value) {
        $this->binds[$paramenter] = &$value;

        return call_user_func_array(array($this->statement, 'bindParam'), func_get_args());
    }

    /**
    * @see \PDOStatement::bindValue
    */
    public function bindValue($parameter, $value, $type = \Pdo::PARAM_STR)
    {
        $this->binds[$parameter] = $value;
        return $this->statement->bindValue($parameter, $value, $type);
    }

    /**
     * @see \PDOStatement::execute
     */
    public function execute(array $input_parameters = array()) {
        $start = microtime(true);
        $result = $this->statement->execute($input_parameters);

        if ($this->db->logger) {
            $this->db->logger->log(\Psr\Log\LogLevel::INFO, "Statement: '" .
                                   $this->statement->queryString .
                                   "' executed in " . (microtime(true) - $start),
                                   array_merge($this->binds, $input_parameters)
            );
        }


        return $result;
    }

    /**
    * Returns the real `PDOStatement` instance.
    *
    * @return \PDOStatement The instance.
    */
    public function getPdoStatement()
    {
        return $this->statement;
    }

}