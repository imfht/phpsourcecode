<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/29
 * Time: 上午10:36
 */

namespace Inhere\Queue;

use Inhere\Queue\Driver\DbQueue;
use Inhere\Queue\Driver\PhpQueue;
use Inhere\Queue\Driver\RedisQueue;
use Inhere\Queue\Driver\ShmQueue;
use Inhere\Queue\Driver\SysVQueue;

/**
 * Class Queue - queue factory
 * @package Inhere\Queue
 */
final class Queue
{
    const DRIVER_DB = 'db';
    const DRIVER_PHP = 'php';
    // const DRIVER_LDB = 'levelDb';
    // const DRIVER_RDB = 'rocksDb';
    const DRIVER_SHM = 'shm';
    const DRIVER_SYSV = 'sysv';
    const DRIVER_REDIS = 'redis';

    /**
     * driver map
     * @var array
     */
    private static $driverMap = [
        'db' => DbQueue::class,
        'php' => PhpQueue::class,
        'sysv' => SysVQueue::class,
        'shm' => ShmQueue::class,
        'redis' => RedisQueue::class,
    ];

    /**
     * @param string $driver
     * @param array $config
     * @return QueueInterface
     */
    public static function make(array $config = [], $driver = '')
    {
        if (!$driver && isset($config['driver'])) {
            $driver = $config['driver'];
            unset($config['driver']);
        }

        if ($driver && ($class = self::getDriverClass($driver))) {
            return new $class($config);
        }

        return new PhpQueue($config);
    }

    /**
     * @param $driver
     * @return mixed|null
     */
    public static function getDriverClass($driver)
    {
        return self::$driverMap[$driver] ?? null;
    }

    /**
     * @return array
     */
    public static function getDriverMap(): array
    {
        return self::$driverMap;
    }
}
