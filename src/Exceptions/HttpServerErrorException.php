<?php
/**
 * HttpServerErrorException.php
 * 
 * Short description for HttpServerErrorException class.
 * 
 * Longer description for HttpServerErrorException class, if any.
 * 
 * PHP version 5.6
 * 
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 28.10.2014
 * 
 * @package 
 * @subpackage 
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 28.10.2014
 */

namespace purity\core\Exceptions;

use purity\core\View as View;

class HttpServerErrorException extends HttpException
{
    public function response() {
        return new \purity\core\Response(new View('errors/500'), 500);
    }
}