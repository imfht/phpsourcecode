<?php
/*
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/9/3
 * Time: 21:38
 */

namespace Yan\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public static function initialize()
    {
        $capsule = new Capsule;

        $dbConfigs = Config::get('db');

        foreach ($dbConfigs as $connectionName => $config) {
            $driver = $config['db_driver'] ?: '';
            $host = $config['db_host'] ?: '';
            $user = $config['db_user'] ?: '';
            $password = $config['db_password'] ?: '';
            $database = $config['db_database'] ?: '';
            $charset = $config['db_charset'] ?: '';
            $collation = $config['db_collation'] ?: '';
            $prefix = $config['db_prefix'] ?: '';


            $capsule->addConnection([
                'driver' => $driver,
                'host' => $host,
                'database' => $database,
                'username' => $user,
                'password' => $password,
                'charset' => $charset,
                'collation' => $collation,
                'prefix' => $prefix,
            ], $connectionName);
        }


        // Set the event dispatcher used by Eloquent models... (optional)

        $capsule->setEventDispatcher(new Dispatcher(new Container));

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
    }
}