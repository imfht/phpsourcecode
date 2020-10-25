<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Orm;


use Timo\Config\Config;

/**
 * Class Model
 * @package Timo\Core
 */
class Model
{
    /**
     * @var string 数据库类型
     */
    protected $dbType = 'mysql';

    /**
     * @var array 连接参数
     */
    protected $options = [];

    /**
     * @var string 数据库名
     */
    protected $database = 'default';

    /**
     * @var string 数据表名前缀
     */
    protected $prefix = null;

    /**
     * @var string 数据表名
     */
    protected $table = '';

    /**
     * @var Connection 数据库连接
     */
    protected $connection = null;

    /**
     * @var bool 是否启用读写分离
     */
    protected $rwSeparate;

    /**
     * @var static 模型实例集合
     */
    protected static $instances;

    /**
     * 构造函数
     *
     * @param array $options 实例化数据库相关参数
     */
    public function __construct()
    {
        $this->connect($this->dbType, $this->database, $this->options);
    }

    /**
     * 返回模型单例
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
     * 连接数据库，获取数据库实例
     *
     * @param $dbType
     * @param $database
     * @param array $options
     */
    protected function connect($dbType, $database, $options = [])
    {
        $conf = Config::runtime($dbType . '.' . $database);

        if (!is_null($this->rwSeparate)) {
            $conf['rw_separate'] = $this->rwSeparate;
        }
        $this->connection = Connection::instance($conf, $options);
        if ($this->prefix === null) {
            $this->prefix = $conf['prefix'];
        }
    }

    /**
     * 获取当前模型所对应的数据表名称（包括表前缀）
     *
     * @return string
     */
    public function getTable()
    {
        if (!$this->table) {
            $class_name = get_class($this);
            $pos = strpos($class_name, 'Model') ? -5 : null;
            $params = [$class_name, strrpos($class_name, '\\') + 1];
            if (!is_null($pos)) {
                $params[] = $pos;
            }
            $table = lcfirst(substr(...$params));
            $table = preg_replace_callback('/[A-Z]?/', function ($match) {
                return !empty($match[0]) ? '_' . strtolower($match[0]) : '';
            }, $table);

            //当有前缀时，加上前缀
            $this->table = (!$this->prefix) ? $table : $this->prefix . $table;
        }

        return $this->table;
    }

    /**
     * 通过主键获取记录
     *
     * @param int|array $id 主键ID
     * @param string $fields
     * @return array
     */
    public static function find($id, $fields = '*')
    {
        $model = static::instance();
        $row = $model->connection->table($model->getTable())->fields($fields)->where($id)->row();
        return $row;
    }

    /**
     * 获取所有记录
     *
     * @param string $fields
     * @return array
     */
    public static function select($fields = '*')
    {
        $model = static::instance();
        return $model->connection->table($model->getTable())->fields($fields)->select();
    }

    /**
     * 设置条件
     *
     * @param $column
     * @param null $operator
     * @param null $value
     * @return Connection
     */
    public static function where($column = null, $operator = null, $value = null)
    {
        $model = static::instance();
        if (is_null($column)) {
            return $model->connection->table($model->getTable());
        }
        return $model->connection->table($model->getTable())->where($column, $operator, $value);
    }

    /**
     * 添加数据
     *
     * @param array $data
     *
     * @return bool|string
     */
    public static function insert(array $data)
    {
        $model = static::instance();
        return $model->connection->table($model->getTable())->insert($data);
    }

    /**
     * 批量插入数据
     *
     * @param array $data
     * @param bool $return_id
     * @return bool|int
     */
    public static function insertMulti(array $data, $return_id = true)
    {
        $model = static::instance();
        return $model->connection->table($model->getTable())->insertMulti($data, $return_id);
    }

    /**
     * 获取数据库连接
     *
     * @return Connection
     */
    public static function connection()
    {
        $model = static::instance();
        return $model->connection;
    }
}
