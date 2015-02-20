<?php
/**
 * Authentication.php
 *
 * Short description for Authentication class.
 *
 * Longer description for Authentication class, if any.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 24.10.2014
 *
 * @package
 * @subpackage
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 9.1.2014
 * @todo Documentation
 */

namespace purity\app\models;

use purity\core\Database as Database;
use purity\core\Session as Session;

use Katzgrau\KLogger\Logger as Logger;
use Aura\SqlQuery\QueryFactory;

class Authentication
{
	/**
	 * Database
	 *
	 * @var purity\core\Database
	 **/
	private $database;

	/**
	 * Session
	 *
	 * @var purity\core\Session
	 **/
	private $session;

	/**
	 * SQL query builder
	 *
	 * @var NilPortugues\SqlQueryBuilder\Builder\GenericBuilder
	 **/
	private $builder;

	/**
	 * Access.log-logger
	 *
	 * @var Katzgrau\KLogger\Logger
	 **/
	private $logger;

	/**
	 * Class constructor
	 *
	 * @return void
	 **/
	public function __construct(Database $database, Session $session, Logger $logger)
	{
		$this->database = $database;
		$this->session  = $session;
		$this->logger   = $logger;
    	$this->builder  = new QueryFactory('mysql');
	}

	/**
	 * Attempt login
	 *
	 * @return bool
	 **/
	public function login($username, $password)
	{
		$query = $this->builder->newSelect();
		//SELECT * FROM `users` WHERE `username`='administrator';
		$query
			->cols(array(
				'password'
				))
			->from('users')
			->where('username=:username')
			->bindValue(':username', $username);

		// Prepare and execute query
		$statement = $this->database->prepare($query->__toString());
        $statement->execute($query->getBindValues());

        $hash = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$hash)
        {
        	return false;
        }

        if (password_verify($password, $hash['password']))
        {
        	$this->session->set('authenticated', true);
        	$this->session->regenerate();
        	$this->session->stop();

        	// Update users lastlogin timestamp
        	$user = new User($this->database);
        	$user->updateLastLogin($username);

        	$this->logger->info($username . ' logged in.', array($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));

        	return true;
        }

        return false;
	}

	/**
	 * Destroy session
	 *
	 * @return void
	 **/
	public function logout()
	{
		$this->session->destroy();
	}

}