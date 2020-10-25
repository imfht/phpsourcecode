<?php
/*
 * The application settings
 */
return new \Phalcon\Config([
    'appName'            => 'PhaService',
    'version'            => '1.0.181005',
    'rev_version'        => 'α', //α,β,R
    'website'            => 'https://www.qhbit.cn',

    /**
     * Database
     */
    'database'           => [
        'adapter'    => 'Mysql',
        'host'       => 'localhost',
        'username'   => 'root',
        'password'   => 'powerdev',
        'dbname'     => 'pha_service',
        'charset'    => 'utf8',
        'persistent' => TRUE,
    ],

    /**
     * Application
     */
    'application'        => [
        'modelsDir'      => BASE_PATH . '/app/models/',
        'migrationsDir'  => BASE_PATH . '/cli/migrations/',
        'controllersDir' => BASE_PATH . '/cli/tasks/',
        'libraryDir'     => BASE_PATH . '/cli/library/',
        //'middlewareDir'  => APP_PATH . '/middleware/',
        //'baseUri'        => '/',
    ],

    /**
     * Redis config
     */
    'redis'              => [
        'prefix'     => '',
        'lifetime'   => 86400,
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'auth'       => '',
        'persistent' => TRUE,
    ],

    /**
     * Message Queue
     */
    'beanstalk'          => [
        'host'       => '127.0.0.1',
        'port'       => 11300,
        'persistent' => TRUE,
    ],

    /**
     * tasksDir is the absolute path to your tasks directory
     * For instance, 'tasksDir' => realpath(dirname(dirname(__FILE__))).'/tasks',
     */
    'tasksDir'           => BASE_PATH . '/cli/tasks/',

    /**
     * annotationsAdapter is the choosen adapter to read annotations.
     * Adapter by default: memory
     */
    'annotationsAdapter' => 'memory',

    'printNewLine' => TRUE,

]);
