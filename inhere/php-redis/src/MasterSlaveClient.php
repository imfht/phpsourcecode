<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/1/22
 * Time: 22:33
 * @referrer https://github.com/auraphp/Aura.Sql
 * File: MasterSlaveClient.php
 */

namespace inhere\redis;

/**
 *
 * ```php
 * $client = new MasterSlaveClient($config);
 * $config = [
 *     'master' => [
 *         'host' => '127.0.0.1',
 *         'port' => '6379',
 *         'database' => '0',
 *         'options' => []
 *     ],
 *     'slaves' => [
 *         'slave1' => [
 *             'host' => '127.0.0.1',
 *             'port' => '6379',
 *             'database' => '0',
 *             'options' => []
 *         ],
 *         'slave2' => [],
 *         'slave3' => [],
 *         ...
 *     ],
 * ]
 * ```
 */
class MasterSlaveClient extends AbstractClient
{
    const MODE = 'master-slave';

    const TYPE_WRITER = 'writer';
    const TYPE_READER = 'reader';

    /**
     * @var array
     */
    protected $typeNames = [
        'writer' => [
            // 'master'
        ],
        'reader' => [
            // 'slave1','slave2',
        ]
    ];

    /*
     * connection callback list
     * @var array
     */
    // protected $values = [
    // 'writer.master' => function(){},
    // 'reader.slave1' => function(){},
    //];

    /*
     * instanced connections
     * @var \Redis[]
     */
    //protected $connections = [
    // 'writer.master' => Object (\Redis),
    //];

    /*
     * @var array
     */
    // protected $config = [
    // 'writer.master' => [],
    // 'reader.slave1' => [],
    // 'reader.slave2' => [],
    // ];


    /**
     * @inheritdoc
     */
    public function setConfig(array $config)
    {
        /** @var array[] $config */
        if ($config) {
            // Compatible
            if (isset($config['master'])) {
                $this->config['writer.master'] = $config['master'];
            }

            if (isset($config['writers']) && is_array($config['writers'])) {
                foreach ($config['writers'] as $name => $conf) {
                    $this->config['writer.' . $name] = $conf;
                }
            }

            // Compatible
            if (isset($config['slaves']) && is_array($config['slaves'])) {
                foreach ($config['slaves'] as $name => $conf) {
                    $this->config['reader.' . $name] = $conf;
                }
            }

            if (isset($config['readers']) && is_array($config['readers'])) {
                foreach ($config['readers'] as $name => $conf) {
                    $this->config['reader.' . $name] = $conf;
                }
            }

            // create callbacks
            $this->setCallbacks($this->config);
        }
    }

    /**
     * @inheritdoc
     */
    protected function setCallback($name)
    {
        list($type, $rawName) = explode('.', $name, 2);

        $this->typeNames[$type][] = $rawName;

        parent::setCallback($name);
    }

    /**
     * @return \Redis
     */
    public function master()
    {
        return $this->writer('master');
    }

    /**
     * @param null|string $name
     * @return \Redis
     */
    public function slave($name = null)
    {
        return $this->reader($name);
    }

    /**
     * @param null|string $name
     * @return \Redis
     */
    public function reader($name = null)
    {
        if (!($typeNames = $this->typeNames[self::TYPE_READER])) {
            throw new \RuntimeException('Without any reader(slave) redis config!');
        }

        // return a random connection
        if (null === $name) {
            $key = array_rand($typeNames);
            $name = $typeNames[$key];
        }

        return $this->getConnection('reader.' . $name);
    }

    /**
     * @param null|string $name
     * @return \Redis
     */
    public function writer($name = null)
    {
        if (null === $name) {
            $name = 'master';
        }

        return $this->getConnection('writer.' . $name);
    }

    /**
     * getConnection
     * @param  string $name
     * @return \Redis
     */
    protected function getConnection($name = null)
    {
        if (!$name || strpos($name, '.') <= 0) {
            throw new \RuntimeException('Connection name don\'t allow empty or format error.');
        }

        // list($type, $rawName) = explode('.', $name, 2);

        return parent::getConnection($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, array $args)
    {
        $upperMethod = strtoupper($method);

        // exists and enabled
        if (
            isset($this->getSupportedCommands()[$upperMethod]) &&
            true === $this->getSupportedCommands()[$upperMethod]
        ) {
            return $this->execByMethod($upperMethod, $args);
        }

        throw new UnknownMethodException("Call the method [$method] don't exists OR don't allow access!");
    }

    /**
     * @param $upperMethod
     * @param $args
     * @return mixed
     */
    public function execByMethod($upperMethod, $args)
    {
        //// read operation
        if (
            isset($this->getReadOnlyOperations()[$upperMethod]) &&
            ($value = $this->getReadOnlyOperations()[$upperMethod]) &&
            is_array($value) &&
            $value[0]->$value[1]($args, $upperMethod) // [$this, 'isSortReadOnly']
            //call_user_func($value, $args, $upperMethod)
        ) {
            $operate = 'read';

            // trigger before execute event
            $this->fire(self::BEFORE_EXECUTE, [$upperMethod, $args, $operate]);

            $ret = $this->reader()->$upperMethod(...$args);

            //// write operation
        } else {
            $operate = 'write';

            // trigger before execute event
            $this->fire(self::BEFORE_EXECUTE, [$upperMethod, $args, $operate]);

            $ret = $this->writer()->$upperMethod(...$args);
        }

        // trigger after execute event
        $this->fire(self::AFTER_EXECUTE, [$upperMethod, ['args' => $args, 'ret' => $ret], $operate]);

        return $ret;
    }

/////////////////////////////////////////////////////////////////////////////////////
//// help method(referrer package predis)
/////////////////////////////////////////////////////////////////////////////////////

    /**
     * Checks if a SORT command is a readable operation by parsing the arguments
     * array of the specified command args.
     * @param array $arguments Command args.
     * @return bool
     */
    protected function isSortReadOnly(array $arguments)
    {
        $argc = count($arguments);

        if ($argc > 1) {
            for ($i = 1; $i < $argc; ++$i) {
                $argument = strtoupper($arguments[$i]);
                if ($argument === 'STORE') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks if BITFIELD performs a read-only operation by looking for certain
     * SET and INCRYBY modifiers in the arguments array of the command.
     * @param array $arguments Command args.
     * @return bool
     */
    protected function isBitfieldReadOnly(array $arguments)
    {
        $argc = count($arguments);

        if ($argc >= 2) {
            for ($i = 1; $i < $argc; ++$i) {
                $argument = strtoupper($arguments[$i]);
                if ($argument === 'SET' || $argument === 'INCRBY') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks if a GEORADIUS command is a readable operation by parsing the
     * arguments array of the specified command instance.
     * @param array $arguments Command args.
     * @param string $command Command name.
     * @return bool
     */
    protected function isGeoradiusReadOnly(array $arguments, $command)
    {
        $argc = count($arguments);
        $startIndex = $command === 'GEORADIUS' ? 5 : 4;

        if ($argc > $startIndex) {
            for ($i = $startIndex; $i < $argc; ++$i) {
                $argument = strtoupper($arguments[$i]);
                if ($argument === 'STORE' || $argument === 'STOREDIST') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns the default list of commands performing read-only operations.
     * @return array
     */
    protected function getReadOnlyOperations()
    {
        return [
            'EXISTS' => true,
            'TYPE' => true,
            'KEYS' => true,
            'SCAN' => true,
            'RANDOMKEY' => true,
            'TTL' => true,
            'GET' => true,
            'MGET' => true,
            'SUBSTR' => true,
            'STRLEN' => true,
            'GETRANGE' => true,
            'GETBIT' => true,
            'LLEN' => true,
            'LRANGE' => true,
            'LINDEX' => true,
            'SCARD' => true,
            'SISMEMBER' => true,
            'SINTER' => true,
            'SUNION' => true,
            'SDIFF' => true,
            'SMEMBERS' => true,
            'SSCAN' => true,
            'SRANDMEMBER' => true,
            'ZRANGE' => true,
            'ZREVRANGE' => true,
            'ZRANGEBYSCORE' => true,
            'ZREVRANGEBYSCORE' => true,
            'ZCARD' => true,
            'ZSCORE' => true,
            'ZCOUNT' => true,
            'ZRANK' => true,
            'ZREVRANK' => true,
            'ZSCAN' => true,
            'ZLEXCOUNT' => true,
            'ZRANGEBYLEX' => true,
            'ZREVRANGEBYLEX' => true,
            'HGET' => true,
            'HMGET' => true,
            'HEXISTS' => true,
            'HLEN' => true,
            'HKEYS' => true,
            'HVALS' => true,
            'HGETALL' => true,
            'HSCAN' => true,
            'HSTRLEN' => true,
            'PING' => true,
            'AUTH' => true,
            'SELECT' => true,
            'ECHO' => true,
            'QUIT' => true,
            'OBJECT' => true,
            'BITCOUNT' => true,
            'BITPOS' => true,
            'TIME' => true,
            'PFCOUNT' => true,
            'SORT' => [$this, 'isSortReadOnly'],
            'BITFIELD' => [$this, 'isBitfieldReadOnly'],
            'GEOHASH' => true,
            'GEOPOS' => true,
            'GEODIST' => true,
            'GEORADIUS' => [$this, 'isGeoradiusReadOnly'],
            'GEORADIUSBYMEMBER' => [$this, 'isGeoradiusReadOnly'],
        ];
    }
}
