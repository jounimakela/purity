<?php
/**
 * Session.php
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

class Session {

	/**
	 * Session data
	 *
	 * @var array
	 **/
	protected $data = array();

	/**
	 * Session configuration
	 *
	 * @var purity\core\Config
	 **/
	private $config = null;

	/**
	 * Session lifetime time
	 *
	 * @var int
	 **/
	private $lifetime = null;

	/**
	 * ID used to identify this session
	 *
	 * @var string
	 **/
	private $id;

	/**
	 * Session (and cookie) name
	 *
	 * @var string
	 **/
	private $name;

	/**
	 * The path on the server in which the cookie will be available on
	 *
	 * @var string
	 **/
	private $path = '/';

	/**
	 * The domain that the cookie is available to
	 *
	 * @var string
	 **/
	private $domain = null;

	/**
	 * Indicates if the cookie should be transmitted over HTTPS connection
	 *
	 * @var bool
	 **/
	private $secure = false;

	/**
	 * Flag to indicate session state
	 *
	 * @var string
	 **/
	private $started = false;

	/**
	 * Class constructor. Creates new session.
	 *
	 * @return void
	 **/
	public function __construct(Config $config)
	{
		$this->config = $config;

		$this->name 	= $config->session->name;
		$this->lifetime	= $config->session->lifetime;
		$this->secure 	= $config->session->secure;

		$this->start();
	}

	/**
	 * Starts a new session
	 *
	 * @return bool
	 **/
	public function start()
	{
		$this->started = true;

		return $this->read();
	}

	/**
	 * Stop the session
	 *
	 * @return bool
	 **/
	public function stop()
	{
		if (!$this->started)
		{
			return false;
		}

		// Write session data
		$this->writeFile(serialize($this->buildPayload()));

		return $this->setCookie($this->name, $this->id);
	}

	/**
	 * Destroy the session
	 *
	 * @return void
	 **/
	public function destroy()
	{
		if ($this->started)
		{
			$this->started = false;

			$file = $this->config->session->path . DS . $this->name . '_' . $this->id;

			if (File::exists($file))
			{
				File::delete($file);
			}

			$this->deleteCookie($this->name);
		}
	}

	/**
	 * Reads session data
	 *
	 * @return bool
	 **/
	public function read()
	{
		if (!$this->started)
		{
			return false;
		}

		if ($sessionId = $this->findSessionId())
		{
			if ($payload = $this->readFile())
			{
				return $this->processPayload(unserialize($payload));
			}
		}

		// No session found, reset start flag
		$this->started = false;

		return $this->create();
	}

	/**
	 * Creates a new session
	 *
	 * @return void
	 **/
	public function create()
	{
		if (!$this->started)
		{
			$this->setId(null);

			if ($this->writeFile(serialize($this->buildPayload())))
			{
				$this->setCookie($this->name, $this->id);
				$this->started = true;
				return true;
			}
		}

		return false;
	}

	/**
	 * Writes the session file
	 *
	 * @return bool
	 **/
	private function writeFile($payload)
	{
		$file = $this->config->session->path . DS . $this->name . '_' . $this->id;
		File::update($file, $payload);

		return true;
	}

	/**
	 * Reads the session file
	 *
	 * @return mixed
	 **/
	private function readFile()
	{
		$file = $this->config->session->path . DS . $this->name . '_' . $this->id;

		if (File::exists($file))
		{
			$payload = File::read($file);
		} else {
			$payload = false;
		}

		return $payload;
	}

	/**
	 * Set session ID
	 *
	 * @param string $id
	 * @return void
	 **/
	public function setId($id)
	{
		if(!$this->isValidId($id))
		{
			$id = $this->generateSessionId();
		}

		$this->id = $id;
	}

	/**
	 * Attach key and value pair to session
	 *
	 * @return void
	 **/
	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Stores an item in the session
	 *
	 * @return void
	 **/
	public function put($key, $value)
	{
		if (!is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $arrKey => $arrValue)
		{
			$this->set($arrKey, $arrValue);
		}
	}

	/**
	 * Retrieves an item from the session
	 *
	 * @return mixed
	 **/
	public function get($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	/**
	 * Retrieves all data from the session
	 *
	 * @return array
	 **/
	public function all()
	{
		return $this->data;
	}

	/**
	 * Determines if an item exists in the session
	 *
	 * @return bool
	 **/
	public function has($key)
	{
		return isset($this->data)
			   ? array_key_exists($key, $this->data)
			   : false;
	}

	/**
	 * Removes an item from the session
	 *
	 * @return void
	 **/
	public function forget($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Removes all items from the session
	 *
	 * @return void
	 **/
	public function flush()
	{
		unset($this->data);
	}

	/**
	 * Migrate session
	 *
	 * @return void
	 * @author
	 **/
	public function migrate($destroy = false)
	{
		if ($destroy)
		{
			$this->destroy();
		}

		$this->id = $this->generateSessionId();

		return true;
	}

	/**
	 * Regenerates the session id
	 *
	 * @return void
	 **/
	public function regenerate($destroy = false)
	{
		return $this->migrate($destroy);
	}

	/**
	 * Get a new, random session ID
	 *
	 * @return string
	 **/
	private function generateSessionId()
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$id = '';

		for ($i=0; $i < 32; $i++)
		{
			$id .= substr($pool, mt_rand(0, strlen($pool)-1), 1);
		}

		return $id;
	}

	/**
	* Determine if this is a valid session ID.
	*
	* @param string $id
	* @return bool
	*/
	public function isValidId($id)
	{
		return is_string($id) && preg_match('/^[a-zA-Z0-9]{32}$/', $id);
	}

	/**
	 * Process the session payload
	 *
	 * @return array
	 **/
	private function buildPayload()
	{
		$lifetime = ($this->lifetime > 0) ? $this->lifetime + time() : 0;

		if (!$this->id)
		{
			$this->regenerate();
		}

		return array(
			'data' => $this->data,
			'security' => array(
				'ip' => $_SERVER['REMOTE_ADDR'],
				'ua' => $_SERVER['HTTP_USER_AGENT'],
				'ex' => $lifetime,
				'id' => $this->id
			)
		);
	}

	/**
	 * Process the session payload
	 *
	 * @return bool
	 **/
	private function processPayload($payload)
	{
		if (isset($payload['security']) && isset($payload['data']))
		{
			if ($payload['security']['ip'] !== $_SERVER['REMOTE_ADDR'])
			{
				echo '<pre>' . print_r('Invalid IP', 1) . '</pre>';
				return false;
			}

			if ($payload['security']['ua'] !== $_SERVER['HTTP_USER_AGENT'])
			{
				echo '<pre>' . print_r('Invalid UA', 1) . '</pre>';
				return false;
			}

			if ($payload['security']['ex'] < time())
			{
				echo '<pre>' . print_r('Session expired', 1) . '</pre>';
				return false;
			}

			// Restore session id and data
			$this->setId($payload['security']['id']);
			$this->data = $payload['data'];

			return true;
		}
	}

	/**
	 * Find the current session id
	 *
	 * @return mixed
	 **/
	private function findSessionId()
	{
		if (isset($_COOKIE[$this->name]))
		{
			$this->id = $_COOKIE[$this->name];
		}

		return $this->id ?: null;
	}

	/**
	 * Sets a cookie
	 *
	 * @return bool
	 **/
	private function setCookie($name, $value)
	{
		$lifetime = ($this->lifetime > 0) ? $this->lifetime + time() : 0;

		return setcookie($name, $value, $lifetime,
						 $this->config->base_path, $this->config->domain,
						 $this->secure, true);
	}

	/**
	 * Deletes a cookie by making the value null and expiring it
	 *
	 * @return bool
	 **/
	private function deleteCookie($name)
	{
		unset($_COOKIE[$name]);

		return setcookie($name, null, -86400,
						 $this->config->base_path);
	}

}