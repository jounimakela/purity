<?php
/**
 * HttpException.php
 * 
 * Short description for HttpException class.
 * 
 * Longer description for HttpException class, if any.
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

abstract class HttpException extends \Exception
{
    /**
     * Must return a response object for the handle method
     *
     * @return  Response
     */
    abstract protected function response();

    /**
     * When this type of exception isn't caught this method is called by
     * Error::exception_handler() to deal with the problem.
     */
    public function handle()
    {
        // get the exception response
        $response = $this->response();

        // send the response out
        $response->send(true);
    }
}