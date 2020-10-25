<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Orm;


use Exception;
use PDO;
use Timo\Config\Config;

class Connection
{
    /**
     * @var array 数据库连接池
     */
    protected static $instances = [];

    /**
     * @var array PDO对象池
     */
    protected static $pdos = [];

    /**
     * @var PDO 当前数据库连接对象
     */
    protected $pdo = null;

    /**
     * @var \PDOStatement 执行SQL语句后的返回对象
     */
    protected $stmt = null;

    /**
     * @var array 数据库配置
     */
    protected $config = [];

    /**
     * @var bool 是否启用读写分离
     */
    protected $rwSeparate = false;

    /**
     * @var string 当前执行的SQL语句
     */
    protected $sql = '';

    /**
     * @var string 数据库名
     */
    protected $database = '';

    /**
     * @var string 数据表名前缀
     */
    protected $prefix = null;

    /**
     * @var array 数据表信息
     */
    protected $tables = [];

    /**
     * @var array 查询选项
     */
    public $qos = [];

    /**
     * 获取数据库连接实例
     *
     * @param $conf string|array 数据库名称|配置
     * @param array $options
     * @return Connection
     */
    public static function instance($conf, $options = [])
    {
        if (!is_array($conf)) {
            $conf = Config::runtime('mysql.' . $conf);
        }
        $name = md5(serialize($conf) . implode(',', array_keys($options)));
        if (!isset(self::$instances[$name])) {
            $connection = new self();
            $connection->pdo = self::connect($conf, $options);
            $connection->rwSeparate = isset($conf['rw_separate']) ? $conf['rw_separate'] : false;
            $connection->config = $conf;
            $connection->database = $conf['database'];
            $connection->prefix = $conf['prefix'];
            $connection->resetOptions();
            self::$instances[$name] = $connection;
        }
        return self::$instances[$name];
    }

    /**
     * 连接数据库
     *
     * @param array $conf
     * @param array $options
     * @return PDO
     * @throws Exception
     */
    public static function connect(array $conf, $options = [])
    {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $conf['host'], $conf['port'], $conf['database'], $conf['charset']);
        $name = md5(serialize($conf) . implode(',', array_keys($options)));

        if (isset(self::$pdos[$name]) && is_a(self::$pdos[$name], 'PDO')) {
            return self::$pdos[$name];
        }

        $conf += [
            'options' => [],
            'persistence' => false,
            'user' => null,
            'password' => null
        ];

        //数据库连接
        try {
            $options += $conf['options'] + [
                    PDO::ATTR_PERSISTENT => $conf['persistence'],
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ];

            //实例化数据库连接
            $conn = new PDO($dsn, $conf['user'], $conf['password'], $options);
            self::$pdos[$name] = $conn;

        } catch (\PDOException $exception) {
            //抛出异常信息
            throw new Exception('Database connect error: ' . $exception->getMessage() . ' code: ' . $exception->getCode(), 60002);
        }
        return $conn;
    }

    /**
     * 获取数据库的配置参数
     *
     * @param string $config
     * @return array|mixed
     */
    public function getConfig($config = '')
    {
        return $config ? $this->config[$config] : $this->config;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * 设置表名
     *
     * @param $table
     * @param $is_full
     * @return $this
     */
    public function table($table)
    {
        $this->qos['table'] = $table;
        return $this;
    }

    /**
     * 设置表名（不带前缀）
     *
     * @param $table
     * @param $is_full
     * @return $this
     */
    public function name($table)
    {
        $this->qos['table'] = $this->prefix . $table;
        return $this;
    }

    /**
     * 设置字段
     *
     * @param $fields
     * @return $this
     */
    public function fields($fields)
    {
        $this->qos['fields'] = $fields;
        return $this;
    }

    /**
     * 设置条件
     *
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        if ($column instanceof \Closure) {
            if (empty($column())) {
                return $this;
            }
            $this->qos['where'][] = ' AND (';
            $this->qos['where'][] = $column;
            $this->qos['where'][] = ')';
        } else {
            if (!is_array($column) && is_null($operator) && is_null($value)) {
                $operator = '=';
                $value = $column;
                $column = $this->getPrimaryKey($this->qos['table']);
            }
            $this->qos['where'] = array_merge($this->qos['where'], $this->parseWhere($column, $operator, $value));
        }

        return $this;
    }

    /**
     * 设置或条件
     *
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        if ($column instanceof \Closure) {
            if (empty($column())) {
                return $this;
            }
            $this->qos['where'][] = ' OR (';
            $this->qos['where'][] = $column;
            $this->qos['where'][] = ')';
        } else {
            if (!is_array($column) && is_null($operator) && is_null($value)) {
                $operator = '=';
                $value = $column;
                $column = $this->getPrimaryKey($this->qos['table']);
            }
            $this->qos['where'] = array_merge($this->qos['where'], $this->parseWhere($column, $operator, $value, 'OR'));
        }
        return $this;
    }

    /**
     * 排它锁（X锁）
     *
     * 排它锁：加锁之前请开启事务，加锁之后，其它事务只能读取不能更新，如果其它事务也加了for update，
     * 那其它事务会阻塞等待前一个加锁的事务提交之后才能读取并加锁
     *
     * @return $this
     */
    public function forUpdate()
    {
        $this->qos['forUpdate'] = true;
        return $this;
    }

    /**
     * 分组
     *
     * @param $group
     * @return $this
     */
    public function group($group)
    {
        $this->qos['groupBy'] = ' GROUP BY ' . $group;
        return $this;
    }

    /**
     * 排序
     *
     * @param $order
     * @return $this
     */
    public function order($order)
    {
        if (!empty($order)) {
            $this->qos['orderBy'] = ' ORDER BY ' . $order;
        }
        return $this;
    }

    public function having($having)
    {
        if (!empty($having)) {
            $this->qos['having'] = ' HAVING ' . $having;
        }
        return $this;
    }

    /**
     * 限制行数
     *
     * @param $limit int|string
     * @return $this
     */
    public function limit($limit)
    {
        if ($limit > 0) {
            $this->qos['limit'] = $limit;
        }
        return $this;
    }

    /**
     * 分页
     *
     * @param array $page
     * @return $this
     */
    public function page(array &$page)
    {
        $this->qos['page'] = &$page;
        $this->qos['limit'] = ($page['p'] - 1) * $page['limit'] . ',' . $page['limit'];
        return $this;
    }

    /**
     * 设置表别名
     *
     * @param $alias
     * @return $this
     */
    public function alias($alias)
    {
        $this->qos['alias'] = $alias;
        return $this;
    }

    /**
     * 连表操作
     *
     * @param $table
     * @param $condition
     * @param $type
     * @return $this
     */
    public function join($table, $condition, $type = 'LEFT')
    {
        $this->qos['join'] .= ' ' . $type . ' JOIN ' . $table . ' ON ' . $condition;
        return $this;
    }

    public function getTable()
    {
        return $this->qos['table'];
    }

    public function mode($mode)
    {
        $this->qos['mode'] = $mode;
        return $this;
    }

    /**
     * 获取一行
     *
     * @param string $fields
     * @return array
     */
    public function row($fields = '')
    {
        if (!empty($fields)) {
            $this->qos['fields'] = $fields;
        }
        $this->qos['limit'] = 1;
        $this->buildQuery();
        $ret = $this->query($this->qos['sql'], $this->qos['params'], 'one', $this->qos['mode']);
        $this->resetOptions();
        return $ret;
    }

    /**
     * 获取一列
     *
     * @param string $fields * |name |name, avatar
     * @param string $key
     * @return array
     */
    public function column($fields, $key = null)
    {
        $count = 0;
        $column = null;
        if ($fields != '*') {
            $field_arr = explode(',', $fields);
            $count = count($field_arr);
            if (!is_null($key) && $count == 1) {
                $column = $fields;
            }
            if (!is_null($key) && !in_array($key, $field_arr)) {
                array_unshift($field_arr, $key);
                $count++;
            }
            $this->qos['fields'] = implode(',', $field_arr);
        }

        $this->buildQuery();
        $this->_execute($this->qos['sql'], $this->qos['params']);
        if ($count == 1) {
            $rows = $this->stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $rows = array_column($this->stmt->fetchAll(PDO::FETCH_ASSOC), $column, $key);
        }
        $this->resetOptions();
        return $rows;
    }

    /**
     * 获取字段值
     *
     * @param string $field
     * @return int|string|false|null
     */
    public function value($field = '')
    {
        if (!empty($field)) {
            $this->qos['fields'] = $field;
        }

        $this->qos['limit'] = 1;
        $this->buildQuery();

        $this->_execute($this->qos['sql'], $this->qos['params'], true);
        $this->resetOptions();
        if (!$this->stmt) {
            return NULL;
        }

        $value = $this->stmt->fetchColumn();
        return $value;
    }

    /**
     * 获取多列
     *
     * @param $need_page bool
     * @return array
     */
    public function select($need_page = true)
    {
        $this->buildQuery();

        if (!empty($this->qos['page'])) {
            if ($need_page) {
                $table = empty($this->qos['alias']) ? $this->qos['table'] : $this->qos['table'] . ' ' . $this->qos['alias'];
                if (empty($this->qos['groupBy'])) {
                    $sql = 'SELECT COUNT(*) as total FROM ' . $table . $this->qos['join'] . $this->qos['condition'] . ' LIMIT 1';
                } else {
                    $sql = 'SELECT COUNT(*) as total FROM (SELECT count(*) FROM ' . $table . $this->qos['join'] . $this->qos['condition'] . $this->qos['groupBy'] . ') c LIMIT 1';
                }
                $total = (int)$this->query($sql, $this->qos['params'], 'one')['total'];
                $this->qos['page']['total'] = $total;
                $this->qos['page']['total_page'] = ceil($this->qos['page']['total'] / $this->qos['page']['limit']);
            } else {
                $this->qos['page']['total'] = 0;
                $this->qos['page']['total_page'] = 0;
            }
        }

        $rows = $this->query($this->qos['sql'], $this->qos['params'], 'all');
        $this->resetOptions();
        return $rows;
    }

    /**
     * 自增
     *
     * @param string $field
     * @param int $step
     * @return bool|int
     */
    public function inc($field, $step = 1)
    {
        $data = [$field => ['+', $step]];
        return $this->update($data);
    }

    /**
     * 自减
     *
     * @param string $field
     * @param int $step
     * @return bool|int
     */
    public function dec($field, $step = 1)
    {
        $data = [$field => ['-', $step]];
        return $this->update($data);
    }

    /**
     * 统计条数
     *
     * @param $field
     * @return int
     */
    public function count($field = '*')
    {
        return (int)$this->converge('COUNT', $field);
    }

    /**
     * 求和
     *
     * @param $field
     * @return int|mixed
     */
    public function sum($field)
    {
        return $this->converge('SUM', $field);
    }

    /**
     * 求平均值
     *
     * @param $field
     * @return int|mixed
     */
    public function avg($field)
    {
        return $this->converge('AVG', $field);
    }

    /**
     * 求最大值
     *
     * @param $field
     * @return int|mixed
     */
    public function max($field)
    {
        return $this->converge('MAX', $field);
    }

    /**
     * 求最小值
     *
     * @param $field
     * @return int|mixed
     */
    public function min($field)
    {
        return $this->converge('MIN', $field);
    }

    /**
     * 聚合统计
     *
     * @param $type
     * @param $field
     * @return int|mixed
     */
    protected function converge($type, $field)
    {
        $this->qos['fields'] = $type . '(' . $field . ') ret';
        $row = $this->row();
        return $row['ret'] ? $row['ret'] : 0;
    }

    /**
     * 组装SQL语句
     *
     * @return string
     * @throws Exception
     */
    public function buildQuery()
    {
        if (empty($this->qos['table'])) {
            throw new Exception('not set table in db query build');
        }
        $table = $this->qos['table'];
        if (!empty($this->qos['alias'])) {
            $table = '`' . $table . '` ' . $this->qos['alias'];
        }

        $sql = 'SELECT ' . $this->qos['fields'] . ' FROM ' . $table;
        if (!empty($this->qos['join'])) {
            $sql .= $this->qos['join'];
        }

        $this->qos['condition'] = !empty($this->qos['where']) ? ' WHERE ' . $this->buildWhere($this->qos['where'], $this->qos['params']) : '';
        $sql .= $this->qos['condition'];

        if (!empty($this->qos['groupBy'])) {
            $sql .= $this->qos['groupBy'];
        }
        if (!empty($this->qos['having'])) {
            $sql .= $this->qos['having'];
        }
        if (!empty($this->qos['orderBy'])) {
            $sql .= $this->qos['orderBy'];
        }
        if (!empty($this->qos['limit'])) {
            $sql .= ' LIMIT ' . $this->qos['limit'];
        }
        if ($this->qos['forUpdate']) {
            $sql .= ' FOR UPDATE';
        }

        $this->qos['sql'] = $sql;

        return $sql;
    }

    /**
     * 组装where条件
     *
     * @param $where
     * @param array $params
     * @return string
     */
    public function buildWhere($where, array &$params)
    {
        $condition = '';
        foreach ($where as $item) {
            if (is_string($item)) {
                $condition .= $item;
                continue;
            } elseif ($item instanceof \Closure) {
                $condition .= $this->buildWhere($this->parseWhere($item()), $params);
                continue;
            }
            if ($item['column'] == '_string') {
                $condition .= sprintf(' %s %s', $item['logic'], $item['value']);
                continue;
            }
            if (strpos($item['column'], '.') === false) {
                $item['column'] = sprintf('`%s`', $item['column']);
            } else {
                $arr = explode('.', $item['column']);
                $item['column'] = sprintf('`%s`.%s', $arr[0], $arr[1]);
            }
            switch ($item['operator']) {
                case '=':
                case '<':
                case '<=':
                case '>':
                case '>=':
                case '<>':
                case 'like':
                    $condition .= sprintf(' %s %s %s ?', $item['logic'], $item['column'], $item['operator']);
                    array_push($params, $item['value']);
                    break;
                case 'between':
                    $condition .= sprintf(' %s %s BETWEEN ? AND ?', $item['logic'], $item['column']);
                    $params = array_merge($params, $item['value']);
                    break;
                case 'in':
                    $item['value'] = is_array($item['value']) ? "'" . implode("','", $item['value']) . "'" : $item['value'];
                    $condition .= sprintf(" %s %s IN(" . $item['value'] . ")", $item['logic'], $item['column']);
                    break;
                case 'find_in_set':
                    $condition .= sprintf(' %s FIND_IN_SET(?, %s)', $item['logic'], $item['column']);
                    array_push($params, $item['value']);
                    break;
            }
        }

        return trim($condition, 'AND ');
    }

    /**
     * 解析where条件
     *
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $logic
     * @return array
     */
    public function parseWhere($column, $operator = null, $value = null, $logic = 'AND')
    {
        $where = [];
        if (is_array($column)) {
            if (key($column) === 0) { //索引数组
                foreach ($column as $key => $item) {
                    if (count($item) == 2) {
                        $operator = '=';
                        $value = $item[1];
                    } else {
                        $operator = $item[1];
                        $value = $item[2];
                    }
                    $where[] = ['column' => $item[0], 'operator' => $operator, 'value' => $value, 'logic' => $logic];
                }
            } else { //关联数组
                foreach ($column as $key => $value) {
                    $_logic = $logic;
                    $operator = '=';
                    if (is_array($value)) {
                        if ($value['0'] == 'OR' || $value[0] == 'AND') {
                            $_logic = array_shift($value);
                        }
                        $operator = $value[0];
                        if (count($value) == 2) {
                            $value = $value[1];
                        } else {
                            $value = array_slice($value, 1);
                        }
                    }
                    $where[] = ['column' => $key, 'operator' => $operator, 'value' => $value, 'logic' => $_logic];
                }
            }
        } else {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }
            $where[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'logic' => $logic];
        }
        return $where;
    }

    /**
     * 重置查询选项
     */
    private function resetOptions()
    {
        $this->qos = [
            'table' => '',
            'alias' => '',
            'fields' => '*',
            'join' => '',
            'where' => [],
            'forUpdate' => false,
            'params' => [],
            'groupBy' => '',
            'orderBy' => '',
            'having' => '',
            'limit' => '',
            'page' => [],
            'mode' => PDO::FETCH_ASSOC,
            'condition' => '',
            'sql' => '',
        ];
    }

    /**
     * 执行查询语句
     *
     * @param string $sql
     * @param array $params
     * @param string $row_type
     * @param int $mode
     * @return array
     */
    public function query($sql, $params = null, $row_type = null, $mode = PDO::FETCH_ASSOC)
    {
        $this->_execute($sql, $params, true);
        if (!$this->stmt) {
            $this->resetOptions();
            return [];
        }

        if ($row_type == 'one') {
            $rows = $this->stmt->fetch($mode);
        } else {
            $rows = $this->stmt->fetchAll($mode);
        }

        $this->stmt->closeCursor();
        return $rows ? $rows : [];
    }

    /**
     * 查询一条记录
     *
     * @param $sql
     * @param null $params
     * @param int $mode
     * @return array
     */
    public function queryOne($sql, $params = null, $mode = PDO::FETCH_ASSOC)
    {
        return $this->query($sql, $params = null, 'one', $mode = PDO::FETCH_ASSOC);
    }

    /**
     * 执行非查询SQL语句
     *
     * @param $sql
     * @param null $params
     * @return bool
     */
    public function execute($sql, $params = null)
    {
        $this->_execute($sql, $params);
        if (!$this->stmt) {
            return false;
        }

        return true;
    }

    /**
     * 插入单条数据
     *
     * @param array $data
     * @return bool|string
     */
    public function insert(array $data)
    {
        $table = $this->qos['table'];
        if (empty($table) || empty($data)) {
            return false;
        }

        $this->filterFields($table, $data);
        $params = array_values($data);

        $fields = '`' . implode('`,`', array_keys($data)) . '`';
        $values = rtrim(str_repeat('?,', count($params)), ',');

        $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table, $fields, $values);

        $ret = $this->execute($sql, $params);
        $this->resetOptions();
        if (!$ret) {
            return false;
        }

        if ($this->getPkAutoIncrement($table)) {
            return $this->lastInsertId();
        } else {
            return $data[$this->getPrimaryKey($table)];
        }
    }

    /**
     * 批量插入数据
     *
     * @param $data
     * @param bool $returnId
     * @return bool|string
     */
    public function insertMulti($data, $returnId = false)
    {
        $table = $this->qos['table'];
        if (empty($table) || empty($data) || !isset($data[0]) || !is_array($data[0])) {
            return false;
        }

        $fields = '`' . implode('`,`', array_keys($data[0])) . '`';

        $values = '(' . rtrim(str_repeat('?,', count($data[0])), ',') . ')';
        $values = rtrim(str_repeat($values . ',', count($data)), ',');

        $params = [];
        foreach ($data as $item) {
            $params = array_merge($params, array_values($item));
        }

        //组装SQL语句
        $sql = sprintf('INSERT INTO `%s` (%s) VALUES %s', $table, $fields, $values);

        $this->resetOptions();
        $result = $this->execute($sql, $params);

        //当返回数据需要返回insert id时
        if ($result && $returnId === true) {
            return $this->lastInsertId();
        }

        return $result;
    }

    /**
     * 数据表更新操作
     *
     * @param array $data
     * @return bool|int
     * @throws Exception
     */
    public function update(array $data)
    {
        if (empty($this->qos['table'])) {
            throw new \Exception('db update need appoint table name');
        }
        if (empty($this->qos['where'])) {
            throw new Exception('db update need where condition');
        }
        if (empty($data)) {
            throw new \Exception('db update need data not empty');
        }

        $this->filterFields($this->qos['table'], $data);
        $updateString = '';
        $update_params = [];
        foreach ($data as $key => $val) {
            if (!is_array($val)) {
                $updateString .= '`' . $key . '` = ?,';
                $update_params[] = $val;
            } else {
                $pl = count($val);
                if ($val[0] == 'raw') {
                    $updateString .= '`' . $key . '` = ' . $val[1] . ',';
                } elseif ($pl == 2) {
                    $updateString .= '`' . $key . '` = ' . $key . $val[0] . '?,';
                    $update_params[] = $val[1];
                } else {
                    $updateString .= '`' . $key . '` = ' . $val[0] . $val[1] . $val[2] . ',';
                }
            }
        }
        $updateString = rtrim($updateString, ',');

        $params = [];
        $where = $this->buildWhere($this->qos['where'], $params);
        $params = array_merge($update_params, $params);

        $sql = 'UPDATE ' . $this->qos['table'] . ' SET ' . $updateString . ' WHERE ' . $where;
        $ret = $this->execute($sql, $params);
        $this->resetOptions();
        if ($ret) {
            return $this->stmt->rowCount();
        }
        return $ret;
    }

    /**
     * 数据表删除操作
     *
     * @return bool|int
     * @throws Exception
     */
    public function delete()
    {
        if (empty($this->qos['table'])) {
            throw new Exception('db operation need appoint table name');
        }
        if (empty($this->qos['where'])) {
            throw new Exception('db operation need where condition');
        }

        $params = [];
        $where = $this->buildWhere($this->qos['where'], $params);

        $sql = sprintf("DELETE FROM %s WHERE %s", $this->qos['table'], $where);
        $ret = $this->execute($sql, $params);
        $this->resetOptions();
        if ($ret) {
            return $this->stmt->rowCount();
        }
        return $ret;
    }

    /**
     * 开启事务处理
     *
     * @return boolean
     */
    public function startTrans()
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
        return true;
    }

    /**
     * 提交事务处理
     *
     * @return boolean
     */
    public function commit()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
        return true;
    }

    /**
     * 事务回滚
     *
     * @return boolean
     */
    public function rollback()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        return true;
    }

    /**
     * 过虑数据表字段信息
     *
     * @param string $table 表名
     * 用于insert|update里的字段信息进行过虑，删除掉非法的字段信息。
     * @param array $data
     */
    protected function filterFields($table, array &$data = [])
    {
        //获取数据表字段
        $tableFields = $this->getTableFields($table);

        foreach ($data as $key => $value) {
            if (!in_array($key, $tableFields)) {
                unset($data[$key]);
            }
        }
    }

    /**
     * 获取数据表主键
     *
     * @param $table
     * @return string
     */
    public function getPrimaryKey($table)
    {
        $this->loadTableCache($table);
        return $this->tables[$table]['primaryKey'];
    }

    /**
     * 获取主键是否自增
     *
     * @return bool
     */
    public function getPkAutoIncrement($table)
    {
        $this->loadTableCache($table);
        return $this->tables[$table]['pkAutoIncrement'];
    }

    /**
     * 获取表字段信息
     *
     * @return array
     */
    public function getTableFields($table)
    {
        $this->loadTableCache($table);
        return $this->tables[$table]['fields'];
    }

    /**
     * 创建表信息缓存
     *
     * @param string $table 数据表名称
     * @return bool
     */
    protected function loadTableCache($table)
    {
        //参数分析
        if (!$table) {
            return false;
        }

        if (isset($this->tables[$table])) {
            return true;
        }

        $cacheFile = $this->getCacheFile($table);
        if (is_file($cacheFile)) {
            $this->tables[$table] = include $cacheFile;
            return true;
        }

        //获取数据表字段信息
        $tableInfo = $this->getTableInfo($table);
        $this->tables[$table] = [
            'primaryKey' => $tableInfo['primaryKey'][0],
            'pkAutoIncrement' => $tableInfo['pkAutoIncrement'],
            'fields' => $tableInfo['fields'],
        ];

        if (APP_DEBUG || IS_CLI) {
            return true;
        }

        //缓存文件内容
        $cacheContent = "<?php\nreturn " . var_export($this->tables[$table], true) . ";";

        //分析缓存目录
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        //将缓存内容写入缓存文件
        file_put_contents($cacheFile, $cacheContent, LOCK_EX);

        return true;
    }

    /**
     * 删除表缓存文件
     *
     * @param string $tableName 数据表名
     * @return boolean
     */
    public function removeCache($tableName)
    {
        $cacheFile = $this->getCacheFile($tableName);
        if (!is_file($cacheFile)) {
            return true;
        }
        return unlink($cacheFile);
    }

    /**
     * 获取表缓存文件的路径
     *
     * @param string $table 数据表名
     * @return string 缓存文件的路径
     */
    protected function getCacheFile($table)
    {
        return CACHE_PATH . 'models' . DS . $this->database . DS . $table . '.cache.php';
    }

    /**
     * 根据数据表名获取该数据表的字段信息
     *
     * @param string $tableName 数据表名
     * @param boolean $extItem 数据返回类型选项，即是否返回完成的信息(包含扩展信息)。true:含扩展信息/false:不含扩展信息
     * @return array|bool
     */
    public function getTableInfo($tableName, $extItem = false)
    {
        //参数分析
        if (!$tableName) {
            return false;
        }

        $fieldList = $this->query("SHOW FIELDS FROM {$tableName}");
        if ($extItem === true) {
            return $fieldList;
        }

        //过滤掉杂数据
        $primaryArray = [];
        $pkAutoIncrement = 0;
        $fieldArray = [];

        foreach ($fieldList as $line) {
            //分析主键
            if ($line['Key'] == 'PRI') {
                $primaryArray[] = $line['Field'];
                if ($line['Extra'] == 'auto_increment') {
                    $pkAutoIncrement = 1;
                }
            }
            //分析字段
            $fieldArray[] = $line['Field'];
        }

        return ['primaryKey' => $primaryArray, 'pkAutoIncrement' => $pkAutoIncrement, 'fields' => $fieldArray];
    }

    /**
     * 获取当前数据库中的所有的数据表名的列表
     *
     * @return array
     */
    public function getTableList()
    {
        //执行SQL语句，获取数据信息
        $tableList = $this->mode(PDO::FETCH_COLUMN)->query("SHOW TABLES");
        if (!$tableList) {
            return [];
        }

        return array_values($tableList);
    }

    /**
     * 获取最新的insert_id
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function getLastSql()
    {
        return $this->sql;
    }

    /**
     * 检测连接是否可用
     *
     * @return bool
     */
    public function ping()
    {
        try {
            $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * 对字符串进行转义,提高数据库操作安全
     *
     * @param string $value 待转义的字符串内容
     * @return string
     */
    public function escape($value = null)
    {
        //参数分析
        if (is_null($value)) {
            return null;
        }

        if (!is_array($value)) {
            return trim($this->pdo->quote($value));
        }

        //当参数为数组时
        return array_map([$this, 'escape'], $value);
    }

    /**
     * 获取执行SQL语句的返回结果$stmt
     *
     * @param string $sql SQL语句内容
     * @param array $params 待转义的参数值
     * @param bool $readonly 是读还是写
     *
     * @return bool
     */
    protected function _execute($sql, array $params = null, $readonly = false)
    {
        $this->sql = $this->_parseQuerySql($sql, $params);

        $pdo = $this->pdo;

        if ($readonly && $this->rwSeparate === true) {
            $slave_config = $this->config['slave'];
            $slave_key = array_rand($slave_config);
            $pdo = self::connect($slave_config[$slave_key]);
        }

        try {
            //执行SQL语句
            $this->stmt = $pdo->prepare($sql);
            $result = $this->stmt->execute($params);

            //分析执行结果
            if (!$result) {
                $this->stmt->closeCursor();
                return false;
            }

            return true;
        } catch (\PDOException $e) {

            //抛出异常信息
            $this->throwException($e, $sql, $params);
            return false;
        }
    }

    /**
     * 分析组装所执行的SQL语句
     * 用于prepare()与execute()组合使用时，组装所执行的SQL语句
     *
     * @param string $sql SQL语句
     * @param array $params 参数值
     *
     * @return string
     */
    protected function _parseQuerySql($sql, array $params = null)
    {
        if (!$sql) {
            return false;
        }
        $sql = trim($sql);

        //当所要转义的参数值为空时
        if (!$params) {
            return $sql;
        }

        foreach ($params as &$param) {
            if (is_string($param)) {
                $param = sprintf("'%s'", $param);
            }
        }

        $sql = str_replace('?', '%s', $sql);
        return vsprintf($sql, $params);
    }

    /**
     * 抛出异常提示信息处理
     * 用于执行SQL语句时，程序出现异常时的异常信息抛出
     *
     * @param $exception \PDOException
     * @param $sql
     * @param array $params
     * @return bool
     * @throws Exception
     */
    protected function throwException(\PDOException $exception, $sql, $params = [])
    {
        //参数分析
        if (!is_object($exception) || !$sql) {
            return false;
        }

        $code = 60003;
        if (strpos($exception->getMessage(), 'MySQL server has gone away')) {
            $code = 60004;
        }
        $sql = $this->_parseQuerySql($sql, $params);
        $message = 'SQL execute error: ' . $sql . ' |' . $exception->getMessage() . ' Code: ' . $exception->getCode();

        //抛出异常信息
        throw new Exception($message, $code);
    }
}
