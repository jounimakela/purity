<?php
return array(
    'domain'           => 'sitename.net',
    'base_path'         => '/',

    'database'          => array(
        'adapter'       => 'mysql',
        'log'           => LOG_PATH . 'Query',

        'write'         => array(
            'host'      => '',
            'username'  => '',
            'password'  => '',
            'database'  => ''
        ),

        'read'          => array(
            'host'      => '',
            'username'  => '',
            'password'  => '',
            'database'  => ''
        ),
    ),

    'email'            => array(
        'encoding'      => '8bit',
        'charset'       => 'utf-8',
        'newline'       => '\n',
        'addresses'        => array(
            'noreply'        => 'noreply@site.net',
        ),
    ),

    'session'           => array(
        'name'          => 'puritysid',
        'secure'        => false,
        'lifetime'      => 3600,  // Seconds
        'path'          => '/tmp' // File path on server
    ),

    'frontpage'         => 'home/index',
    'offline'           => false,
    'offline_page'      => 'errors/maintenance',
    'timezone'		    => 'Europe/Helsinki',
    'locale'            => 'en'
);
?>