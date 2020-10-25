<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;

/**
 * Class Model
 * @package Timo\Core
 * @method Db db
 * @method string tablePrefix
 */
class Model
{
    /**
     * @var string 数据库名
     */
    protected $dbName = '';

    /**
     * @var string 数据表名前缀
     */
    protected $tablePrefix = null;

    /**
     * @var string 数据表名
     */
    protected $tableName = '';

    /**
     * @var string 数据表别名
     */
    protected $tableAlias = '';

    /**
     * @var string 数据表的主键
     */
    protected $primaryKey = '';

    /**
     * @var bool 主键是否自增
     */
    protected $pkAutoIncrement = null;

    /**
     * @var array 数据表字段信息
     */
    protected $tableFields = [];

    /**
     * @var Db 数据库实例
     */
    protected $db = null;

    /**
     * @var bool 是否启用读写分离
     */
    protected $rw_separate;

    /**
     * @var int 获取模式
     */
    protected $fetchMode = \PDO::FETCH_ASSOC;

    protected $queryOptions = [
        'fields' => '*',
        'join' => '',
        'where' => '',
        'params' => [],
        'groupBy' => '',
        'orderBy' => '',
        'limit' => '',
        'page' => [],
        'mode' => \PDO::FETCH_ASSOC,
        'condition' => '',
        'sql' => '',
    ];

    /**
     * @var static 模型实例集合
     */
    protected static $instances;

    /**
     * 构造函数
     *
     * @param string $dbType 数据库类型，如：mysql、postgres、sqlite、sql_server
     * @param string $dbName 数据库实例名称
     * @param array $options 实例化数据库相关参数
     */
    public function __construct($dbType = '', $dbName = '', $options = [])
    {
        $dbType = !$dbType ? 'mysql' : $dbType;
        $dbName = !$dbName ? 'master' : $dbName;
        $this->connect($dbType, $dbName, $options);
    }

    /**
     * 连接数据库，获取数据库实例
     *
     * @param $dbType
     * @param $dbName
     * @param array $options
     */
    protected function connect($dbType, $dbName, $options = [])
    {
        $conf = Config::runtime($dbType . '.' . $dbName);

        if (is_null($this->rw_separate)) {
            $this->rw_separate = !isset($conf['rw_separate']) ? false : $conf['rw_separate'];
        }
        $this->db = Db::getInstance($conf, $options, $this->rw_separate);
        $this->dbName = $dbName;
        if ($this->tablePrefix === null) {
            $this->tablePrefix = $conf['prefix'];
        }
    }

    /**
     * 返回模型实例
     *
     * @return static
     */
    public static function instance()
    {
        $called_class = get_called_class();
        if (!isset(static::$instances[$called_class])) {
            static::$instances[$called_class] = new static();
        }
        return static::$instances[$called_class];
    }

    /**
     * 获取当前模型所对应的数据表名称（包括表前缀）
     *
     * @param string $table
     * @return string
     */
    public function getTableName($table = '')
    {
        if (!empty($table)) {
            return (!$this->tablePrefix) ? $table : $this->tablePrefix . $table;
        }
        if (!$this->tableName) {

            $tableName = lcfirst(substr(get_class($this), strrpos(get_class($this), '\\') + 1, -5));
            $tableName = preg_replace_callback('/[A-Z]?/', function ($match) {
                return !empty($match[0]) ? '_' . strtolower($match[0]) : '';
            }, $tableName);

            //当有前缀时，加上前缀
            $this->tableName = (!$this->tablePrefix) ? $tableName : $this->tablePrefix . $tableName;

            if (strpos($this->tableName, ' ') === false) {
                $this->tableName = '`' . $this->tableName . '`';
            }
        }

        return $this->tableName;
    }

    /**
     * 获取当前模型（Model）文件所对应的数据表主键
     *
     * @return string
     */
    protected function getPrimaryKey()
    {
        if (!$this->primaryKey) {
            $tableName = $this->getTableName();
            //从缓存文件中读取
            if (!$this->loadCache($tableName)) {
                $this->createCache($tableName);
            }
        }

        return $this->primaryKey;
    }

    /**
     * 获取主键是否自增
     *
     * @return bool
     */
    protected function getPkAutoIncrement()
    {
        if (!$this->pkAutoIncrement) {
            $tableName = $this->getTableName();
            //从缓存文件中读取
            if (!$this->loadCache($tableName)) {
                $this->createCache($tableName);
            }
        }

        return $this->pkAutoIncrement;
    }

    /**
     * 获取当前模型（Model）文件所对应的数据表字段信息
     *
     * @return array
     */
    protected function getTableFields()
    {
        if (!$this->tableFields) {
            $tableName = $this->getTableName();
            //当回调方法未获取到数据表字段信息时，则从缓存文件中读取
            if (!$this->loadCache($tableName)) {
                $this->createCache($tableName);
            }
        }

        return $this->tableFields;
    }

    /**
     * 设置当前模型（Model）文件所对应的数据表的名称（不包含表前缀）
     *
     * @param $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $tableName = trim($tableName);
        $this->tableName = (!$this->tablePrefix) ? $tableName : $this->tablePrefix . $tableName;
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
        $this->tableAlias = $alias;
        return $this;
    }

    /**
     * 加载当前模型（Model）文件的缓存文件内容
     *
     * 注：缓存文件内容为：当前模型（Model）文件所对应的数据表的字段信息及主键信息。
     * @param $tableName
     * @return bool
     */
    protected function loadCache($tableName)
    {
        if (!$tableName) {
            return false;
        }

        $cacheFile = $this->getCacheFile($tableName);

        if (!is_file($cacheFile)) {
            return false;
        }

        $cache = include $cacheFile;

        $this->primaryKey = $cache['primaryKey'];
        $this->pkAutoIncrement = $cache['pkAutoIncrement'];
        $this->tableFields = $cache['fields'];

        return true;
    }

    /**
     * 创建当前模型信息的缓存
     *
     * @param string $tableName 数据表名称
     * @return bool
     */
    protected function createCache($tableName)
    {
        //参数分析
        if (!$tableName) {
            return false;
        }

        //获取数据表字段信息
        $tableInfo = $this->db->getTableInfo($tableName);

        $this->primaryKey = $tableInfo['primaryKey'][0];
        $this->pkAutoIncrement = $tableInfo['pkAutoIncrement'];
        $this->tableFields = $tableInfo['fields'];

        if (APP_DEBUG || IS_CLI) {
            return true;
        }

        //缓存文件内容
        $cacheDataArray = [
            'primaryKey' => $this->primaryKey,
            'pkAutoIncrement' => $this->pkAutoIncrement,
            'fields' => $this->tableFields,
        ];

        $cacheContent = "<?php\nreturn " . var_export($cacheDataArray, true) . ";";

        //分析缓存文件路径
        $cacheFile = $this->getCacheFile($tableName);

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
     * 删除当前模型（Model）缓存文件
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
     * 获取数据表写入时的最新的Insert Id
     *
     * @access public
     * @return integer
     */
    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * 获取PDOStatement
     *
     * @return $this
     */
    public function stmt()
    {
        $this->db->return_stmt = true;
        return $this;
    }

    /**
     * 更改获取模式
     *
     * @param $fetchMode
     * @return $this
     */
    public function mode($fetchMode)
    {
        $this->fetchMode = $fetchMode;
        return $this;
    }

    /**
     * 事务处理：开启事务处理
     *
     * @return boolean
     */
    public function startTrans()
    {
        return $this->db->startTrans();
    }

    /**
     * 事务处理：提交事务处理
     *
     * @return boolean
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * 事务处理：事务回滚
     *
     * @return boolean
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * 执行查询SQL语句
     *
     * 用于执行查询性的SQL语句（需要数据返回的情况）
     *
     * @param string $sql 所要执行的SQL语句
     * @param array $params
     * @return Db
     */
    public function query($sql, array $params = null)
    {
        $sql = str_replace('__TABLE__', $this->getTableName(), $sql);
        return $this->db->query($sql, $params);
    }

    /**
     * 执行非查询SQL语句
     *
     * 用于无需返回信息的操作。如：更改、删除、添加数据
     *
     * @param string $sql 所要执行的SQL语句
     * @param array $params 待转义的数据。注：本参数支持字符串及数组，如果待转义的数据量在两个或两个以上请使用数组
     * @return boolean
     */
    public function execute($sql, $params = null)
    {
        if (!$sql) {
            return false;
        }
        //转义数据表前缀、表名
        $sql = str_replace('__TABLE__', $this->getTableName(), $sql);
        return $this->db->execute($sql, $params);
    }

    /**
     * 添加数据
     *
     * @param array $data
     * @param string $table 数据表名称（包括表前缀）
     *
     * @return bool|int
     */
    public function insert(array $data, $table = '')
    {
        if (empty($table)) {
            $table = $this->getTableName();
            //数据过滤
            $data = $this->filterFields($data);
        }
        if (!$data) {
            return false;
        }

        if ($this->getPkAutoIncrement()) {
            return $this->db->insert($table, $data);
        } else {
            $ret = $this->db->insert($table, $data, false);
            if ($ret) {
                return $data[$this->getPrimaryKey()];
            }
            return false;
        }
    }

    /**
     * 批量插入数据
     *
     * @param array $data
     * @param bool $return_id
     * @param string $table
     * @return bool|int
     */
    public function insertMulti(array $data, $return_id = true, $table = '')
    {
        if (empty($data) || !isset($data[0]) || !is_array($data[0])) {
            return false;
        }
        if (empty($table)) {
            $table = $this->getTableName();
        }

        return $this->db->insertMulti($table, $data, $return_id);
    }

    /**
     * 删除数据
     *
     * @param int|string|array $where
     * @param string $table
     * @return bool
     */
    public function delete($where, $table = '')
    {
        if (empty($table)) {
            $table = $this->getTableName();
        }

        $params = [];
        $where = $this->parseWhere($where, $params);

        return $this->db->delete($table, $where, $params);
    }

    /**
     * 更新数据
     *
     * @param string|array $data
     * @param int|string|array $where
     * @param string $table
     * @return int|bool
     */
    public function update($data, $where = '', $table = '')
    {
        if (empty($table)) {
            $table = $this->getTableName();
            $data = $this->filterFields($data);
        }
        if (empty($where)) {
            $where = $this->queryOptions['where'];
        }
        $this->clearQuery();
        if (!$data) {
            return false;
        }

        $params = [];
        $where = $this->parseWhere($where, $params);

        return $this->db->update($table, $data, $where, $params);
    }

    /**
     * 通过主键获取记录
     *
     * @param int|array $id 主键ID
     * @param string $fields
     * @param string $table
     * @return array|object|bool
     */
    public function get($id, $fields = '*', $table = '')
    {
        if (empty($table)) {
            $table = $this->getTableName();
        }

        $primaryKey = $this->getPrimaryKey();

        $sql = 'SELECT ' . $fields . ' FROM ' . $table . ' WHERE ' . $primaryKey;

        if (is_array($id)) {
            $id = array_map('intval', $id);
            $sql .= " IN (" . implode(',', $id) . ")";
            $row = $this->db->all($sql);
        } else {
            $sql .= " = " . $this->db->escape($id);
            $fetch_mode = $this->fetchMode;
            $this->fetchMode = \PDO::FETCH_ASSOC;
            $row = $this->db->getOne($sql, null, $fetch_mode);
        }
        return $row;
    }

    /**
     * 获取一条记录
     *
     * @param string|int|array $where
     * @param string $fields
     * @param string $order
     * @param string $group
     * @param string $table
     * @return array|object|\PDOStatement
     */
    public function getRow($where, $fields = '*', $order = '', $group = '', $table = '')
    {
        if (empty($table)) {
            $table = $this->getTableName();
        }

        $params = [];
        $condition = !empty($where) ? ' WHERE ' . $this->parseWhere($where, $params) : '';

        $sql = 'SELECT ' . $fields . ' FROM ' . $table . $condition;
        if (!empty($group)) {
            $sql .= ' GROUP BY ' . $group;
        }
        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
        }
        $sql .= ' LIMIT 1';

        $fetch_mode = $this->fetchMode;
        $this->fetchMode = \PDO::FETCH_ASSOC;

        return $this->db->getOne($sql, $params, $fetch_mode);
    }

    /**
     * 获取一行
     *
     * @return array
     */
    public function row()
    {
        if (empty($this->queryOptions['limit'])) {
            $this->queryOptions['limit'] = 1;
        }
        $this->buildQuery();
        $ret = $this->db->getOne($this->queryOptions['sql'], $this->queryOptions['params'], $this->queryOptions['mode']);
        $this->clearQuery();
        return $ret;
    }

    /**
     * 获取多条数据
     *
     * @param string|array $where
     * @param string $fields
     * @param string $order
     * @param string $group
     * @param int $limit
     * @param string $table
     * @return array|\PDOStatement
     */
    public function all($where = '', $fields = '*', $order = '', $group = '', $limit = 0, $table = '')
    {
        $fetch_mode = $this->fetchMode;
        $this->fetchMode = \PDO::FETCH_ASSOC;

        if (empty($table)) {
            $table = $this->getTableName();
        }
        $params = [];
        $condition = !empty($where) ? ' WHERE ' . $this->parseWhere($where, $params) : '';

        $sql = 'SELECT ' . $fields . ' FROM ' . $table . $condition;

        if (!empty($group)) {
            $sql .= ' GROUP BY ' . $group;
        }
        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
        }
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        return $this->db->all($sql, $params, $fetch_mode);
    }

    /**
     * 分页获取多条数据 准备弃用2018-06-26
     *
     * @param string|array $where
     * @param string $fields
     * @param string $order
     * @param string $group
     * @param array $page
     * @param string $table
     * @return array|bool
     */
    public function find($where, $fields = '*', $order = '', $group = '', array &$page = ['p' => 1, 'limit' => 20], $table = '')
    {
        $fetch_mode = $this->fetchMode;
        $this->fetchMode = \PDO::FETCH_ASSOC;

        if (empty($table)) {
            $table = $this->getTableName();
        }

        $total = $this->getRow($where, 'COUNT(*) as total', '', '', $table);
        $page['total'] = (int)$total['total'];
        $page['total_page'] = ceil($page['total'] / $page['limit']);

        $params = [];
        $condition = !empty($where) ? ' WHERE ' . $this->parseWhere($where, $params) : '';

        $sql = 'SELECT ' . $fields . ' FROM ' . $table . $condition;

        if (!empty($group)) {
            $sql .= ' GROUP BY ' . $group;
        }
        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
        }

        if ($page['p'] <= $page['total_page']) {
            $sql .= ' LIMIT ' . ($page['p'] - 1) * $page['limit'] . ',' . $page['limit'];
            $row = $this->db->all($sql, $params, $fetch_mode);
        } else {
            $row = [];
        }
        return $row;
    }

    /**
     * 连表操作
     *
     * @param $table
     * @param $condition
     * @param $type
     * @return $this
     */
    public function join($table, $condition, $type)
    {
        $this->queryOptions['join'] .= ' ' . $type . ' JOIN ' . $table . ' ON ' . $condition;
        return $this;
    }

    /**
     * 设置字段
     *
     * @param $fields
     * @return Model
     */
    public function fields($fields)
    {
        $this->queryOptions['fields'] = $fields;
        return $this;
    }

    /**
     * 设置表名
     *
     * @param $name
     * @return $this
     */
    public function table($name)
    {
        $this->tableName = $name;
        return $this;
    }

    /**
     * 设置条件
     *
     * @param $where
     * @return Model
     */
    public function where($where)
    {
        $this->queryOptions['where'] = $where;
        return $this;
    }

    /**
     * 分组
     *
     * @param $group
     * @return Model
     */
    public function group($group)
    {
        if (!empty($group)) {
            $this->queryOptions['groupBy'] = ' GROUP BY ' . $group;
        }
        return $this;
    }

    /**
     * 排序
     *
     * @param $order
     * @return Model
     */
    public function order($order)
    {
        if (!empty($order)) {
            $this->queryOptions['orderBy'] = ' ORDER BY ' . $order;
        }
        return $this;
    }

    /**
     * 限制行数
     *
     * @param $limit int|string
     * @return Model
     */
    public function limit($limit)
    {
        if ($limit > 0) {
            $this->queryOptions['limit'] = $limit;
        }
        return $this;
    }

    /**
     * 分页
     *
     * @param array $page
     * @return Model
     */
    public function page(array &$page)
    {
        $this->queryOptions['page'] = &$page;
        $this->queryOptions['limit'] = ($page['p'] - 1) * $page['limit'] . ',' . $page['limit'];
        return $this;
    }

    /**
     * @return Model
     */
    public function object()
    {
        $this->queryOptions['mode'] = \PDO::FETCH_OBJ;
        return $this;
    }

    /**
     * 获取一列
     *
     * @param string $fields * | name | name, avatar
     * @param string $key 可指定的索引
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
            $this->queryOptions['fields'] = implode(',', $field_arr);
        }

        $this->buildQuery();
        $stmt = $this->stmt()->db->find($this->queryOptions['sql'], $this->queryOptions['params'], 'all');
        if ($count == 1) {
            $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } else {
            $rows = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), $column, $key);
        }
        $this->clearQuery();
        return $rows;
    }

    /**
     * 获取字段值
     *
     * @param string $field
     * @return int|string|null
     */
    public function value($field = '')
    {
        if (!empty($field)) {
            $this->queryOptions['fields'] = $field;
        }

        $this->queryOptions['limit'] = 1;
        $this->buildQuery();
        $ret = $this->db->value($this->queryOptions['sql'], $this->queryOptions['params']);
        $this->clearQuery();
        if (is_array($ret)) {
            return null;
        }
        return $ret;
    }

    /**
     * 获取多列
     *
     * @param $p int
     * @return array
     */
    public function select($p = 1)
    {
        $this->buildQuery();

        if (empty($this->queryOptions['page'])) {
            $rows = $this->db->all($this->queryOptions['sql'], $this->queryOptions['params'], $this->queryOptions['mode']);
            $this->clearQuery();
            return $rows;
        }

        if ($p == 1) {
            $table = empty($this->tableAlias) ? $this->getTableName() : $this->getTableName() . ' ' . $this->tableAlias;
            if (empty($this->queryOptions['groupBy'])) {
                $sql = 'SELECT COUNT(*) as total FROM ' . $table . $this->queryOptions['join'] . $this->queryOptions['condition'] . ' LIMIT 1';
            } else {
                $sql = 'SELECT COUNT(*) as total FROM (SELECT count(*) FROM ' . $table . $this->queryOptions['join'] . $this->queryOptions['condition'] . $this->queryOptions['groupBy'] . ') c LIMIT 1';
            }
            $total = (int)$this->db->getOne($sql, $this->queryOptions['params'])['total'];

            $this->queryOptions['page']['total'] = $total;
            $this->queryOptions['page']['total_page'] = ceil($this->queryOptions['page']['total'] / $this->queryOptions['page']['limit']);
        } else {
            $this->queryOptions['page']['total'] = 0;
            $this->queryOptions['page']['total_page'] = 0;
        }

        $rows = $this->db->all($this->queryOptions['sql'], $this->queryOptions['params'], $this->queryOptions['mode']);
        $this->clearQuery();
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
     * @param int|string|array $where
     * @return int
     */
    public function count($where = [])
    {
        if (!empty($where)) {
            $this->queryOptions['where'] = $where;
        }
        $this->queryOptions['fields'] = 'COUNT(' . $this->queryOptions['fields'] . ') total';
        $count = $this->row();
        return (int)$count['total'];
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
        $this->queryOptions['fields'] = $type . '(' . $field . ') ret';
        $row = $this->row();
        return $row['ret'] ? $row['ret'] : 0;
    }

    /**
     * 组装SQL语句
     */
    protected function buildQuery()
    {
        $table = $this->getTableName();
        if (!empty($this->tableAlias)) {
            $table .= ' ' . $this->tableAlias;
        }

        $sql = 'SELECT ' . $this->queryOptions['fields'] . ' FROM ' . $table;
        if (!empty($this->queryOptions['join'])) {
            $sql .= $this->queryOptions['join'];
        }

        $this->queryOptions['condition'] = !empty($this->queryOptions['where']) ? ' WHERE ' . $this->parseWhere($this->queryOptions['where'], $this->queryOptions['params']) : '';
        $sql .= $this->queryOptions['condition'];

        if (!empty($this->queryOptions['groupBy'])) {
            $sql .= $this->queryOptions['groupBy'];
        }
        if (!empty($this->queryOptions['orderBy'])) {
            $sql .= $this->queryOptions['orderBy'];
        }
        if (!empty($this->queryOptions['limit'])) {
            $sql .= ' LIMIT ' . $this->queryOptions['limit'];
        }
        $this->queryOptions['sql'] = $sql;
    }

    /**
     * 清除查询
     */
    protected function clearQuery()
    {
        $this->queryOptions['fields'] = '*';
        $this->queryOptions['join'] = '';
        $this->queryOptions['where'] = '';
        $this->queryOptions['params'] = [];
        $this->queryOptions['groupBy'] = '';
        $this->queryOptions['orderBy'] = '';
        $this->queryOptions['limit'] = '';
        $this->queryOptions['mode'] = \PDO::FETCH_ASSOC;
        $this->queryOptions['condition'] = '';
        $this->queryOptions['sql'] = '';
    }

    /**
     * 分析当前model缓存文件的路径
     *
     * @param string $tableName 数据表名
     * @return string 缓存文件的路径
     */
    protected function getCacheFile($tableName)
    {
        $tableName = trim($tableName, '`');
        return CACHE_PATH . 'models' . DS . $this->dbName . DS . $tableName . '.cache.php';
    }

    /**
     * 过虑数据表字段信息
     *
     * 用于insert()、update()里的字段信息进行过虑，删除掉非法的字段信息。
     * @param array $data
     * @return array
     */
    protected function filterFields(array $data = [])
    {
        //获取数据表字段
        $tableFields = $this->getTableFields();

        $filteredArray = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $tableFields)) {
                $filteredArray[$key] = $value;
            }
        }

        return $filteredArray;
    }

    /**
     * 解析where
     *
     * @param array|string|int $where
     * @param array|null $params
     * @return string
     */
    private function parseWhere($where, &$params)
    {
        if (!is_numeric($where) && !is_array($where)) {
            return $where;
        }

        if (!is_array($where)) {
            $pk = $this->getPrimaryKey();
            $where = [$pk => $where];
        }

        $condition = '';

        foreach ($where as $key => $value) {
            $arr = explode('.', $key);
            $key = !isset($arr[1]) ? '`' . $arr[0] . '`' : $arr[0] . '.`' . $arr[1] . '`';
            if (!is_array($value)) {
                $condition .= $key . '=? AND ';
                array_push($params, $value);
            } else {
                switch ($value[0]) {
                    case '<':
                    case '<=':
                    case '>':
                    case '>=':
                    case '<>':
                    case 'gt':
                    case 'lt':
                    case 'like':
                        $condition .= $key . ' ' . $value[0] . ' ? AND ';
                        array_push($params, $value[1]);
                        break;
                    case 'between':
                        $condition .= $key . ' BETWEEN ? AND ? AND ';
                        array_push($params, $value[1]);
                        array_push($params, $value[2]);
                        break;
                    case 'in':
                        $value[1] = is_array($value[1]) ? implode(',', $value[1]) : $value[1];
                        $condition .= $key . ' IN(' . $value[1] . ') AND ';
                        break;
                    case 'find_in_set':
                        $condition .= 'FIND_IN_SET(?, ' . $key . ') AND ';
                        array_push($params, $value[1]);
                        break;
                }
            }
        }
        return trim($condition, 'AND ');
    }

    public function __call($name, $arguments)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return false;
    }
}
