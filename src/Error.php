<?php
/**
 * Error.php
 *
 * Short description for Error class.
 *
 * Longer description for Error class, if any.
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

namespace purity\core;

use Katzgrau\KLogger\Logger as Logger;

class Error
{
    /**
     * Severity constants as strings
     *
     * @var array
     */
    public static $levels = array(
        0                   => 'Error',
        E_ERROR             => 'Fatal Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parsing Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Runtime Recoverable error',
        E_DEPRECATED        => 'Runtime Deprecated code usage',
        E_USER_DEPRECATED   => 'User Deprecated code usage'
    );

    /**
     * Fatal severity level
     *
     * @var array
     */
    public static $fatal_levels = array(E_PARSE, E_ERROR, E_USER_ERROR, E_COMPILE_ERROR);

    /**
     * Holds application instance
     *
     * @var \purity\core\Application
     */
    private $app = null;

    /**
     * Class constructor.
     *
     * @param \purity\core\Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * PHP Exception handler
     *
     * @param \Exception $e The Exception
     * @return bool
     */
    public static function exceptionHandler(\Exception $e) {
        // If handle-method exists, use it
        if (method_exists($e, 'handle')) {
            return $e->handle();
        }

        $severity = (!isset(static::$levels[$e->getCode()]))
                    ? $e->getCode()
                    : static::$levels[$e->getCode()];

        $logger = new Logger(LOG_PATH . 'App');
        $logger->error($severity . " - " . $e->getMessage(), $e->getTrace());

        if (Application::$env == Application::PRODUCTION) {
            static::showProductionError();
        } else {
            // Let's just rethorw the exception so Xdebug can take care of it
            throw $e;
        }
    }

    /**
     * PHP Error handler
     *
     * @param int    $severity  The severity code
     * @param string $message   The error message
     * @param string $filepath  The path to the file throwing the error
     * @param int    $line      The line number of the error
     * @return bool             Whether to continue execution
     */
    public static function errorHandler($severity, $message, $filepath, $line) {
        // Don't do anything if error reporting is disabled
        if (error_reporting() !== 0) {
            throw new Exceptions\PhpErrorException($message, $severity, 0, $filepath, $line);
        }

        return true;
    }

    /**
     * Native PHP shutdown handler
     *
     * @return string;
     */
    public static function shutdownHandler() {
        $last_error = error_get_last();

        if ($last_error AND in_array($last_error['type'], static::$fatal_levels)) {
            $severity = static::$levels[$last_error['type']];

            $error = new \ErrorException($last_error['message'],
                                         $last_error['type'],
                                         0,
                                         $last_error['file'],
                                         $last_error['line']);

            if (Application::$env == Application::PRODUCTION) {
                static::showProductionError();
            }

            exit(1);
        }
    }

    /**
     * Shows the errors/production view. Script execution will be stopped.
     *
     * @return void
     */
    public static function showProductionError() {
        if (!headers_sent()) {
            $protocol = $_SERVER['SERVER_PROTOCOL'] ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($protocol . ' 500 Internal Server Error');
        }

        $errorView = new View('errors/500');

        exit($errorView->render());
    }
}