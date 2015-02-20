<?php
/**
 * DatabaseProvider.php
 *
 * Short description for DatabaseProvider class.
 *
 * Longer description for DatabaseProvider class, if any.
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

use purity\core\Config as Config;
use purity\core\Database as Database;

use Katzgrau\KLogger\Logger as Logger;

class DatabaseProvider extends \purity\core\ServiceProvider
{
    public function register() {
        $config = $this->app['Config'];

        $adapter   = $config->database->adapter;
        $dbConfig  = $config->database->read->toArray();
        $logPath   = $config->database->log;

        $config = new Config($dbConfig);

        $database = new Database($adapter, $config, false);
        $logger = new Logger($logPath);

        $database->setLogger($logger);
        $database->connect();

        $this->app->instance('purity\core\Database', $database);
        $this->app->alias('purity\core\Database', 'Database');
    }
}