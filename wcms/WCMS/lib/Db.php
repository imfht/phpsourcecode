<?php
/**
 * 数据模型基类
 * @author wolf [Email: 116311316@qq.com]
 * @since 2011-10-01
 * @version 1.0
 */
class Db
{
    /**
     * 数据库连接实例缓存
     *
     * @var array
     * @return Db
     */
    static $instanceInternalCache = array();
    /**
     * 连接数据库
     *
     * @param array $config        	
     */
    private function __construct ($dbConfig = NULL)
    {}
    private function __clone ()
    {}
    /**
     * 无数据返回
     *
     * @param string $sql        	
     * @return int $rows 影响列数
     */
    protected function exec ($sql)
    {
        return Conn::getInstance()->exec($sql);
    }
    public static function query ($sql)
    {
        $statement = Conn::getInstance()->query($sql);
        if (! $statement && NONOBJECT == 1) {
            echo 'Fatal error:fetch non-object ', $sql;
            exit();
        }
        return $statement;
    }
    /**
     * 返回一条数据
     *
     * @param
     * $sql
     */
    protected function fetch ($sql, $fetch_style = 'PDO::FETCH_ASSOC')
    {
        return self::query($sql)->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * 返回多条数据
     *
     * @param string $sql        	
     */
    public function fetchAll ($sql)
    {
        return self::query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * 返回最后一条数据id
     */
    protected function lastInsertId ()
    {
        return Conn::getInstance()->lastInsertId();
    }
    /**
     * Execute a prepared statement by passing an array of values
     *
     * @param string $sql        	
     * @param array $where        	
     */
    protected function prepares ($sql, $where = array())
    {
        $sth = Conn::getInstance()->prepare($sql, 
        array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        Conn::getInstance()->execute($where);
        return Conn::getInstance()->fetchAll();
    }
    /**
     * 
     * 返回受上一个sql影响的条数
     * @param string $sql        	
     */
    protected function rowCount ($sql)
    {
        return self::query($sql)->rowCount();
    }
    /**
     * 添加
     *
     * @param string $table
     * 数据库表
     * @param array $params
     * 表单参数
     * @return array or int
     */
    protected function add ($table, $params)
    {
        $arr = $this->batchAddArr($params);
         $sql = "INSERT INTO $table $arr";
        $this->exec($sql);
        return $this->lastInsertId();
    }
    /**
     *
     *
     *
     *
     * 删除
     *
     * @param array $where        	
     */
    protected function delete ($table, $where)
    {
        $where = $this->batchWhere($where);
        $sql = "DELETE FROM $table WHERE $where";
        return $this->exec($sql);
    }
    /**
     * 更新
     *
     * @param string $table
     * 表
     * @param array $v
     * 更新值
     * @param array $where
     * 条件
     */
    protected function update ($table, $v = array(), $where = array())
    {
        $v = $this->batchValue($v);
        $where = $this->batchWhere($where);
        $sql = "UPDATE $table SET $v WHERE $where";
        return $this->exec($sql);
    }
    /**
     * 对添加的数组进行处理
     *
     * @param array $array        	
     */
    protected function batchAddArr ($array)
    {
        $string = '';
        if (! is_array($array)) {
            throw new Exception('SQL语句错误！');
        }
        $i = count($array);
        foreach ($array as $k => $v) {
            if ($i == 1) {
                $string = "(`" . $k . "`)" . "VALUES ('" . $v . "')";
            } else {
                $string1 .= "`" . $k . "`,";
                $string2 .= "'" . $v . "',";
            }
        }
        $i != 1 && $string1 = substr($string1, 0, - 1);
        $i != 1 && $string2 = substr($string2, 0, - 1);
        if ($string == '') {
            $string = "(" . $string1 . ") VALUES (" . $string2 . ")";
        }
        return $string;
    }
    /**
     * 分页
     *
     * @param int $start
     * 开始条数
     * @param int $num
     * 取几条
     * @param null|string $key
     * 键名
     * @param null|array $where
     * 条件
     * @return array false
     */
    protected function getPage ($start, $num, $table, $key = NULL, $where = NULL, 
    $order_by = '')
    {
        isset($where) && $where = $this->batchWhere($where);
        if ($key != NULL && $where != NULL) {
            $sql = "SELECT $key FROM $table  WHERE $where ";
        } elseif ($key != NULL && $where == NULL) {
            $sql = "SELECT $key FROM $table ";
        } elseif ($key == NULL && $where == NULL) {
            $sql = "SELECT * FROM $table ";
        } elseif ($key == NULL && $where != NULL) {
            $sql = "SELECT * FROM $table WHERE $where ";
        }
        if ($order_by != '') {
            $sql = $sql . " ORDER BY " . $order_by;
        }
        $sql = $sql . " LIMIT $start,$num";
        return $this->fetchAll($sql);
    }
    /**
     * 获取一条记录
     *
     * @param array $where        	
     * @param string $key        	
     * @return array false
     */
    protected function getOne ($table, $where = array(), $key = NULL)
    {
        $where = $this->batchWhere($where);
        if ($key != NULL) {
            $sql = "SELECT $key FROM $table WHERE $where";
        } else {
            $sql = "SELECT * FROM $table WHERE $where";
        }
        return $this->fetch($sql);
    }
    /**
     * 获取多条记录
     *
     * @param string $table        	
     * @param array $where        	
     * @param string $key        	
     * @return array false
     */
    protected function getAll ($table, $where = NULL, $key = NULL, $order_by = '', 
    $limit = 0)
    {
        if ($key != NULL && $where != NULL) {
            $where = $this->batchWhere($where);
            $sql = "SELECT $key FROM $table WHERE $where";
        } else 
            if ($where != NULL && $key == NULL) {
                $where = $this->batchWhere($where);
                $sql = "SELECT * FROM $table WHERE $where";
            } else 
                if ($where == NULL && $key == NULL) {
                    $sql = "SELECT * FROM $table";
                } else 
                    if ($where == NULL && $key != NULL) {
                        $sql = "SELECT $key FROM $table";
                    }
        if ($order_by != '') {
            $sql = $sql . " ORDER BY " . $order_by;
        }
        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }
        return $this->fetchAll($sql);
    }
    /**
     * 获取数目
     *
     * @param string $key        	
     * @param array $where        	
     * @return int false
     */
    protected function getNum ($table, $key, $where = NULL)
    {
        if ($where != NULL) {
            $where = $this->batchWhere($where);
            $sql = "SELECT $key FROM $table WHERE $where";
        } else {
            $sql = "SELECT $key FROM $table";
        }
        $num = $this->fetchAll($sql);
        return ! isset($num) ? 0 : count($num);
    }
    /**
     *
     *
     *
     *
     * 数组转化 用于更新多个参数
     *
     * @param array $where        	
     */
    protected function batchValue ($where)
    {
        $string = '';
        if (! is_array($where)) {
            throw new Exception('SQL语句错误！');
        }
        $i = count($where);
        foreach ($where as $k => $v) {
            $f = '\'';
            if ($i == 1) {
                $string = "`" . $k . "`" . "=$f" . $v . "$f";
            } else {
                $string .= "`" . $k . "`" . "=$f" . $v . "$f,";
            }
        }
        $i != 1 && $string = substr($string, 0, - 1);
        return $string;
    }
    /**
     * 处理条件 转化为sql语句
     *
     * @param array|string $rows        	
     * @return string
     */
    protected function batchWhere ($where)
    {
        $string = '';
        if (! is_array($where)) {
            throw new Exception('SQL语句错误！');
        }
        $i = count($where);
        // 如果是int类型 不需要加引号 2013-4-17
        foreach ($where as $k => $v) {
            if (is_int($v)) {
                $f = '';
            } else {
                $f = '\'';
            }
            //处理关键词
            if ($k == 'key' || $k == 'varchar' || $k == 'decimal') {
                $quote = '`';
            } else {
                $quote = "";
            }
            if ($i == 1) {
                $string = "$quote" . $k . "$quote" . "=$f" . $v . "$f";
            } else {
                $string .= "$quote" . $k . "$quote" . "=$f" . $v . "$f and" . " ";
            }
        }
        $i != 1 && $string = substr($string, 0, - 4);
        return $string;
    }
    /**
     * 记录错误
     * Enter description here .
     *
     *
     * ..
     *
     * @param unknown_type $content        	
     * @param unknown_type $sql        	
     */
    protected function savelog ($content, $sql)
    {
        if ($this->config['debug']) {
            $log = ROOT . 'log' . DIRECTORY_SEPARATOR . "Db" .
             date("Ymd", time()) . '.txt';
            $handle = fopen($log, "a");
            $content = '[' . date("m-d H:i", time()) . ']    ' . $content . ' ' .
             $sql . "\n";
            fwrite($handle, $content);
            fclose($handle);
        }
    }
    /**
     * 单例模式 子类重写
     * return parent::getInstance(__CLASS__)
     *
     * @param
     * $model
     * @throws Exception
     * @return Db
     */
    public static function _instance ($model)
    {
        if (self::$instanceInternalCache[$model] == NULL) {
            self::$instanceInternalCache[$model] = new $model();
        }
        return self::$instanceInternalCache[$model];
    }
}