<?php
/**
 * LoggerProvider.php
 *
 * Short description for LoggerProvider class.
 *
 * Longer description for LoggerProvider class, if any.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 30.10.2014
 *
 * @package
 * @subpackage
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 30.10.2014
 */

namespace purity\core\Providers;

use Katzgrau\KLogger\Logger as Logger;

class LoggerProvider extends \purity\core\ServiceProvider
{
    public function register() {
        $logger = new Logger(LOG_PATH . 'App');

        $this->app->instance('Katzgrau\KLogger\Logger', $logger);
        $this->app->alias('Katzgrau\KLogger\Logger', 'Logger');
    }
}