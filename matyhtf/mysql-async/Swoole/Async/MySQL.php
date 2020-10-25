<?php
namespace Swoole\Async;

class MySQL
{
    /**
     * max connections for mysql client
     * @var int
     */
    protected $pool_size;

    protected $connection_num;

    /**
     * idle connection
     * @var array
     */
    protected $idle_pool = array();

    /**
     * work connetion
     * @var array
     */
    protected $work_pool = array();

    protected $config;

    /**
     * wait connection
     * @var array
     */
    protected $wait_queue = array();

    protected $round_id;

    function __construct($config, $pool_size = 100)
    {
        if (empty($config['host']) or empty($config['database']) or empty($config['user']) or empty($config['password']))
        {
            throw new \Exception("require host, database, user, password config.");
        }

        if (!function_exists('swoole_get_mysqli_sock'))
        {
            throw new \Exception("require swoole_get_mysqli_sock function.");
        }

        if (empty($config['port']))
        {
            $config['port'] = 3306;
        }

        $this->config = $config;
        $this->pool_size = $pool_size;
    }

    /**
     * create mysql connection
     */
    protected function createConnection()
    {
        $config = $this->config;
        $db = new \mysqli;
        $db->connect($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
        if (!empty($config['charset']))
        {
            $db->set_charset($config['charset']);
        }
        $db_sock = swoole_get_mysqli_sock($db);
        swoole_event_add($db_sock, array($this, 'onSQLReady'));
        $this->idle_pool[] = array(
            'object' => $db,
            'socket' => $db_sock,
        );
        $this->connection_num ++;
    }

    function onSQLReady($db_sock)
    {
        $task = $this->work_pool[$db_sock];

        /**
         * @var \mysqli
         */
        $mysqli = $task['mysql']['object'];
        $callback = $task['callback'];

        if ($result = $mysqli->reap_async_query())
        {
            call_user_func($callback, $mysqli, $result);
            if (is_object($result))
            {
                mysqli_free_result($result);
            }
        }
        else
        {
            echo "MySQLi Error: " . mysqli_error($mysqli)."\n";
        }

        //release mysqli object
        $this->idle_pool[] = $task['mysql'];
        unset($this->work_pool[$db_sock]);

        //fetch a request from wait queue.
        if (count($this->wait_queue) > 0)
        {
            $idle_n = count($this->idle_pool);
            for ($i = 0; $i < $idle_n; $i++)
            {
                $new_task = array_shift($this->wait_queue);
                $this->doQuery($new_task['sql'], $new_task['callback']);
            }
        }
    }

    function query($sql, $callback)
    {
        //no idle connection
        if (count($this->idle_pool) == 0)
        {
            if ($this->connection_num < $this->pool_size)
            {
                $this->createConnection();
                $this->doQuery($sql, $callback);
            }
            else
            {
                $this->wait_queue[] = array(
                    'sql'  => $sql,
                    'callback' => $callback,
                );
            }
        }
        else
        {
            $this->doQuery($sql, $callback);
        }
    }

    protected function doQuery($sql, $callback)
    {
        //remove from idle pool
        $db = array_pop($this->idle_pool);

        /**
         * @var \mysqli
         */
        $mysqli = $db['object'];

        for ($i = 0; $i < 2; $i++)
        {
            $result = $mysqli->query($sql, MYSQLI_ASYNC);
            if ($result === false)
            {
                if ($mysqli->errno == 2013 or $mysqli->errno == 2006)
                {
                    $mysqli->close();
                    $r = $mysqli->connect();
                    if ($r === true)
                    {
                        continue;
                    }
                }
                else
                {
                    echo "server exception. \n";
                    $this->connection_num --;
                    $this->wait_queue[] = array(
                        'sql'  => $sql,
                        'callback' => $callback,
                    );
                }
            }
            break;
        }

        $task['sql'] = $sql;
        $task['callback'] = $callback;
        $task['mysql'] = $db;

        //join to work pool
        $this->work_pool[$db['socket']] = $task;
    }
}