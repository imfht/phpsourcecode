<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/5
 * Time: 22:07
 */

namespace fastwork\db;


use fastwork\exception\DbException;
use Swoole\Coroutine\MySQL;
use traits\Pools;

class Query
{
    /**
     * @var Pools
     */
    protected $pool;
    //sql生成器
    protected $builder;

    //db参数
    protected $options = [
        'table' => '',
        'alias' => [],
        'where' => [],
        'field' => '*',
        'order' => [],
        'distinct' => false,
        'join' => '',
        'union' => '',
        'group' => '',
        'having' => '',
        'limit' => '',
        'lock' => false,
        'fetch_sql' => false,
        'data' => [],
        'prefix' => '',
        'setDefer' => true
    ];

    public function __construct()
    {
        // 创建Builder对象
        $this->builder = new Builder();
        $this->pool = app('db');
        $this->options['prefix'] = $this->pool->config['prefix'];
        $this->options['setDefer'] = $this->pool->config['setDefer'];

    }


    /**
     * @表名
     *
     * @param $tableName
     * @return $this
     */
    public function name($tableName = '')
    {
        $this->options['table'] = $this->options['prefix'] . $tableName;
        return $this;
    }

    //暂未实现
//    public function alias()
//    {
//
//    }

    /**
     * 查询缓存
     * @access public
     * @param  mixed $key 缓存key
     * @param  integer|\DateTime $expire 缓存有效期
     * @param  string $tag 缓存标签
     * @return $this
     */
    public function cache($key = true, $expire = null, $tag = null)
    {
        // 增加快捷调用方式 cache(10) 等同于 cache(true, 10)
        if ($key instanceof \DateTime || (is_numeric($key) && is_null($expire))) {
            $expire = $key;
            $key = true;
        }

        if (false !== $key) {
            $this->options['cache'] = ['key' => $key, 'expire' => $expire, 'tag' => $tag];
        }

        return $this;
    }

    /**
     * @查询字段
     *
     * @param string $field
     * @return $this
     */
    public function field($field = '')
    {
        if (empty($field)) {
            return $this;
        }
        $field_array = explode(',', $field);
        //去重
        $this->options['field'] = array_unique($field_array);
        return $this;
    }


    /**
     * @order by
     *
     * @param array $order
     * @return $this
     */
    public function order($order = [])
    {
        $this->options['order'] = $order;
        return $this;
    }


    /**
     * @group by
     *
     * @param string $group
     * @return $this
     */
    public function group($group = '')
    {
        $this->options['group'] = $group;
        return $this;
    }


    /**
     * @having
     *
     * @param string $having
     * @return $this
     */
    public function having($having = '')
    {
        $this->options['having'] = $having;
        return $this;
    }


    //暂未实现
//    public function join()
//    {
//
//    }


    /**
     * @distinct
     *
     * @param $distinct
     * @return $this
     */
    public function distinct($distinct)
    {
        $this->options['distinct'] = $distinct;
        return $this;
    }


    /**
     * @获取sql语句
     *
     * @return $this
     */
    public function fetchSql()
    {
        $this->options['fetch_sql'] = true;
        return $this;
    }


    /**
     * @where语句
     *
     * @param array $whereArray
     * @return $this
     */
    public function where(array $whereArray = [])
    {
        $this->options['where'] = $whereArray;
        return $this;
    }


    /**
     * @lock加锁
     *
     * @param bool $lock
     * @return $this
     */
    public function lock($lock = false)
    {
        $this->options['lock'] = $lock;
        return $this;
    }


    /**
     * @设置是否返回结果
     *
     * @param bool $bool
     * @return $this
     */
    public function setDefer(bool $bool = true)
    {
        $this->options['setDefer'] = $bool;
        return $this;
    }


    /**
     * @查询一条数据
     *
     * @return array|mixed
     */
    public function find()
    {
        $this->options['limit'] = 1;

        $result = $this->builder->select($this->options);

        if (!empty($this->options['fetch_sql'])) {
            return $this->getRealSql($result);
        }
        return $this->query($result);
    }


    /**
     * @查询
     *
     * @return bool|mixed
     */
    public function select()
    {
        // 生成查询SQL
        $result = $this->builder->select($this->options);

        if (!empty($this->options['fetch_sql'])) {
            return $this->getRealSql($result);
        }

        return $this->query($result);
    }


    /**
     * @ 添加
     *
     * @param array $data
     * @return mixed|string
     */
    public function insert($data = [])
    {
        $this->options['data'] = $data;

        $result = $this->builder->insert($this->options);

        if (!empty($this->options['fetch_sql'])) {
            return $this->getRealSql($result);
        }
        return $this->query($result);
    }


    public function insertAll($data = [])
    {
        $this->options['data'] = $data;

        $result = $this->builder->insertAll($this->options);

        if (!empty($this->options['fetch_sql'])) {
            return $this->getRealSql($result);
        }
        return $this->query($result);
    }


    public function update($data = [])
    {
        $this->options['data'] = $data;

        $result = $this->builder->update($this->options);

        if (!empty($this->options['fetch_sql'])) {
            return $this->getRealSql($result);
        }
        return $this->query($result);
    }


    public function delete()
    {
        // 生成查询SQL
        $result = $this->builder->delete($this->options);

        if (!empty($this->options['fetch_sql'])) {
            return $this->getRealSql($result);
        }

        return $this->query($result);
    }

    /**
     * @执行sql
     *
     * @param $result
     * @return bool
     */
    public function query($result)
    {
        $chan = new \chan(1);
        go(function () use ($chan, $result) {
            $mysql = $this->pool->pop();
            if (is_string($result)) {
                $rs = $mysql->query($result);
                $this->pool->push($mysql);
                if ($this->options['setDefer']) {
                    $chan->push($rs);
                }
            } else {
                if (is_array($result)) {
                    $stmt = $mysql->prepare($result['sql']);
                    if ($stmt) {
                        $rs = $stmt->execute($result['bind']);
                        $this->pool->push($mysql);
                        if ($this->options['setDefer']) {
                            if ($this->options['limit'] == 1) {
                                $chan->push($rs[0]);
                            } else {
                                $chan->push($rs);
                            }
                        }
                    }
                }
            }
        });

        if ($this->options['setDefer']) {
            return $chan->pop();
        }
    }

    /**
     * @sql语句
     *
     * @param $result
     * @return mixed
     */
    protected function getRealSql($result)
    {
        if (count($result['bind']) > 0) {
            foreach ($result['bind'] as $v) {
                $result['sql'] = substr_replace($result['sql'], "'{$v}'", strpos($result['sql'], '?'), 1);
            }
        }

        return $result['sql'];
    }

    /**
     * 事物
     * @param \Closure $success 成功的回调
     * @param \Closure $fail 失败的回调
     * @return mixed
     */
    public function transaction(\Closure $success, \Closure $fail)
    {
        $chan = new \chan(1);
        go(function () use ($chan, $success, $fail) {
            $db = $this->pool->pop();
            defer(function () use ($db) {
                $this->pool->push($db);
            });
            $db->begin();
            try {
                $success($db, $chan);
            } catch (MySQL\Exception $exception) {
                $fail($exception, $chan);
            }
        });
        return $chan->pop();
    }

    /**
     * @获取连接
     *
     * @return mixed
     */
    public function instance(): MySQL
    {
        return $this->pool->pop();
    }


    /**
     * $入池
     * @param $mysql
     * @throws DbException
     */
    public function put($mysql)
    {
        if ($mysql instanceof MySQL) {
            $this->pool->push($mysql);
        } else {
            throw new DbException('传入的$mysql不属于该连接池');
        }
    }


    public function __destruct()
    {
        unset($this->builder);
    }
}