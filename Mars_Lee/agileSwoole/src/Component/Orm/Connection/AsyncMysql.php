<?php
/**
 * Created by Wenlong Li
 * User: wenlong
 * Date: 2018/9/21
 * Time: 上午11:13
 */

namespace Component\Orm\Connection;


use Kernel\Core\Conf\Config;
use Kernel\Core\IComponent\IConnection;

class AsyncMysql extends \Swoole\Coroutine\MySQL  implements IConnection
{
    use HashCode;
    use Free;
    public function __construct(Config $config)
    {
        $config = $config->get('mysql');
        $host = $config['host'];
        $user = $config['user'];
        $password = $config['password'];
        $options = $config['options'] ?? [];
        if(strpos(strtolower($host), 'charset=')!==false) {
            preg_match('/charset=([a-z0-9-]+)/i', $host, $match);
            $charset = isset($match[1]) ? $match[1] : 'utf8';
        } else {
            $charset = isset($options['charset']) ? $options['charset'] : 'utf8';
        }
        /**
         *
         * @example $server = array(
         *     'host' => '192.168.56.102',
         *     'user' => 'test',
         *     'password' => 'test',
         *     'database' => 'test',
         *     'charset' => 'utf8',
         * );
         */
        //var_dump($config);
        //$this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        try {
            parent::connect([
                'host' => $host,
                'user' => $user,
                'password' => $password,
                'charset' => $charset,
                'fetch_mode' => true,
                'database'  =>  'mysql'
            ]);
        }catch (\Exception $e) {
            throw new \InvalidArgumentException('Connection failed: '.$e->getMessage(), $e->getCode());
        }
        $this->HashCode();
    }



}