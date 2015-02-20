<?php
/**
 * index.php
 *
 * Index controller. Front page of Katevat Kapalat website.
 * Returns error/maintenance in case site has been set to offline mode.
 * Dashboard is still accessible.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 24.10.2014
 *
 * @package purity
 * @subpackage app\controllers
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 24.10.2014
 */

namespace purity\app\controllers;

use purity\core\Config as Config;
use purity\core\Response as Response;
use purity\core\View as View;

class index extends \purity\core\Controller
{

	/**
	 * Application configuration
	 *
	 * @var purity\core\Config
	 **/
	private $config;

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

    public function index() {
    	if ($this->config->offline)
    	{
    		$view = new View('errors/maintenance');
    	} else {
        	$view = new View('home/index');
    	}

        $response = new Response($view);
        return $response;
    }

}