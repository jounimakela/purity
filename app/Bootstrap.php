<?php
/**
 * This file is used to bootstrap purity framework.
 * Don't make any changes if you don't know what you are doing!
 *
 * @package purity
 * @subpackage app
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 23.10.2014
 */

// Define directory paths as constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT_PATH')) {
    $parts = explode(DS, realpath(__DIR__ . DS . '..'));
    define('ROOTPATH', implode(DS, $parts) . DS);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', ROOTPATH . 'app' . DS);
}

if (!defined('SRC_PATH')) {
    define('SRC_PATH', ROOTPATH . 'src' . DS);
}

if (!defined('LOG_PATH')) {
    define('LOG_PATH', ROOTPATH . 'logs' . DS);
}

if (!defined('WEB_PATH')) {
    define('WEB_PATH', ROOTPATH . 'web' . DS);
}

if (!defined('VENDOR_PATH')) {
    define('VENDOR_PATH', ROOTPATH . 'vendor' . DS);
}

// Setup Error and exception handlers
register_shutdown_function(function() {
    return purity\core\Error::shutdownHandler();
});

set_exception_handler(function (\Exception $e) {
    if (!class_exists('Error')) {
        include SRC_PATH . 'Error.php';
        include SRC_PATH . 'Exceptions/PhpErrorException.php';

        class_alias('\purity\core\Error', 'Error');
        class_alias('\purity\core\exceptions\PhpErrorException', 'PhpErrorException');
    }

    return \Error::exceptionHandler($e);
});

set_error_handler(function ($severity, $message, $filepath, $line) {
    if (!class_exists('Error')) {
        include SRC_PATH . 'Error.php';
        include SRC_PATH . 'Exceptions/PhpErrorException.php';

        class_alias('\purity\core\Error', 'Error');
        class_alias('\purity\core\exceptions\PhpErrorException', 'PhpErrorException');
    }

    return \Error::errorHandler($severity, $message, $filepath, $line);
});

// Initialize autoloaders
require_once VENDOR_PATH . 'autoload.php';
require_once SRC_PATH . 'Autoloader.php';

$loader = new purity\core\Autoloader();

// Register namespaces
$loader->registerNamespace('purity\app', APP_PATH);
$loader->registerNamespace('purity\core', SRC_PATH);

// Register autoloader
$loader->register();
