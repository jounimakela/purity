<?php
/**
 * AdminController.php
 * 
 * Short description for file
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   CategoryName
 * @package    PackageName
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */

namespace purity\core;

use Katzgrau\KLogger\Logger as Logger;
use purity\core\Request as Request;
use purity\core\Response as Response;
use purity\core\Session as Session;

abstract class AdminController extends \purity\core\Controller {

	/**
	 * Router
	 *
	 * @var \AltoRouter
	 **/
	protected $router;

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function __construct(Session $session, \AltoRouter $router)
	{
		$this->router = $router;
		$authentication = $session->get('authenticated');

		if ($authentication) {
		} else {
			$loginpage = $router->generate('dashboard_login');
			Response::redirect($loginpage);
		}
	}

}