<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM;

use \HandlerSocketi;


/**
 * PHP调用MySQL插件HandlerSocket，基于 kjdev/php-ext-handlersocketi
 *
 * //连接数据库，决定表名和字段列表
 * $hs = new HandlerSocket('127.0.0.1', 9999);
 * $fields = ['id','username','score','modified_at','is_active'];
 * $hs->open('db_test', 't_users', $fields);
 * //插入一行数据
 * $now = date('Y-m-d H:i:s');
 * $row = [1,'ryan',60,$now,true];
 * $hs->insert(array_combine($fields, $row));
 * //更新
 * $hs->update([1, 'David', 80, $now, true], null, 1);
 * //删除，根据主键
 * $hs->delete(1);
 * //读取一行
 * $ryan = $hs->get(1); //按主键
 * $ryan = $hs->get('username', 'ryan'); //按索引
 * //读取多行
 * $users = $hs->all(null, '>=', 1, 3, 1); //WHERE id >= 1 LIMIT 1,3
 * $users = $hs->in('username', 'ryan', 'jane'); //WHERE username IN ('ryan', 'jane')
 * $users = $hs->in('username', ['ryan', 'jane']); //与上面的等价
 */
class HandlerSocket
{
    const HS_PORT_R = 9998;
    const HS_PORT_RW = 9999;

    protected $handler = null;
    protected $query = null;
    protected $read_only = false;
    protected $dbname = ''; //当前数据库
    protected $table = '';  //当前数据表
    protected $fields = []; //字段列表，插入或删除不需要用到
    protected $index = null;  //主键或索引，null时为主键

    public function __construct($host = '127.0.0.1', $port = 0, $read_only = false)
    {
        $this->read_only = $read_only;
        if (empty($port)) {
            $port = $this->read_only ? self::HS_PORT_R : self::HS_PORT_RW;
        }
        $this->handler = new HandlerSocketi($host, $port);
    }

    /**
     * 选定数据表、字段和索引
     * @param string $dbname 数据库名
     * @param string $table 数据表名
     * @param array /string $fields 字段列表
     * @return $this
     */
    public function open($dbname, $table, $fields)
    {
        $this->dbname = $dbname;
        $this->table = $table;
        if (is_string($fields)) {
            $fields = explode(',', str_replace(' ', '', $fields));
        }
        $this->fields = $fields;
        $this->query = null; //更换了表或字段，清除查询对象
        return $this;
    }

    /**
     * 插入一行数据
     * @param array $newbie 字段、值关联数组
     * @return bool
     */
    public function insert(array $newbie)
    {
        $key = $this->index;
        $result = $this->prepare($key)->insert($newbie);
        return $result !== false; //是否成功
    }

    /**
     * 准备查询的索引
     * @param string $index 索引名，null表示主键
     * @return object
     */
    protected function prepare($index = null)
    {
        if (empty($this->query) || $this->index !== $index) {
            $this->index = $index;
            $options = empty($this->index) ? [] : ['index' => $this->index];
            $this->query = $this->handler->open_index(
                $this->dbname, $this->table, $this->fields, $options
            );
        }
        return $this->query;
    }

    /**
     * 删除若干行数据
     * @param mixed $value_key 索引值/索引名
     * @param mixed $value 索引值（可选，当前一个为索引名）
     * @return int/false 删除行数
     */
    public function delete($value)
    {
        if (func_num_args() > 1) {
            $key = $value;
            $value = func_get_arg(1);
        } else {
            $key = null;
        }
        return $this->prepare($key)->remove($value); //影响行数
    }

    /**
     * 清空数据
     * @param mixed $id 主键值
     *          ... $id2 其他主键值
     * @return bool
     */
    public function truncate($ids)
    {
        $actions = [];
        $key = null;
        $ids = is_array($ids) ? $ids : func_get_args();
        foreach ($ids as $id) {
            $actions[] = ['remove', $id];
        }
        $result = $this->prepare($key)->multi($actions);
        $and = create_function('$a,$b', 'return $a && $b;');
        return array_reduce($result, $and, true);
    }

    /**
     * 修改若干行数据
     * @param array $changes 更新的值，向量数组，与$fields对应
     * @param string $key 索引名
     * @param mixed $value 索引值（可选，当前一个为操作符）
     * @return int/false 实际修改行数
     */
    public function update(array $changes, $key, $value)
    {
        return $this->prepare($key)->update($value, $changes); //影响行数
    }

    /**
     * 获取一行数据
     * @param mixed $value_key 索引值/索引名
     * @param mixed $value 索引值（可选，当前一个为索引名）
     * @return array/null/false
     */
    public function get($value)
    {
        if (func_num_args() > 1) {
            $key = $value;
            $value = func_get_arg(1);
        } else {
            $key = null;
        }
        //返回0-base数组成功/false失败
        $result = $this->prepare($key)->find($value);
        if (empty($result)) { //查询失败(false)或没找到(null)
            return ($result === false) ? false : null;
        }
        return array_combine($this->fields, $result[0]); //关联数组
    }

    /**
     * 获取多行数据
     * @param string $key 索引名
     * @param string $op 操作符
     * @param mixed $value 索引值
     * @param int $limit 限定行数
     * @param int $offset 偏移行数
     * @return array/false
     */
    public function all($key = null, $op = '>=', $value = 0,
                        $limit = 0, $offset = 0)
    {
        $extra = [];
        if ($limit > 0) {
            $extra['limit'] = $limit;
        }
        if ($offset > 0) {
            $extra['offset'] = $offset;
        }
        $value = [$op => $value];
        $result = $this->prepare($key)->find($value, $extra);
        if ($result === false) {
            return false; //查询失败
        }
        foreach ($result as & $row) {
            $row = array_combine($this->fields, $row);
        }
        return $result; //向量数组
    }

    /**
     * 获取范围内数据
     * @param string $key 索引名
     * @param mixed $value 索引值
     *          ... $value2 其他索引值
     * @return array/false
     */
    public function in($key, $value)
    {
        $actions = [];
        $values = array_slice(func_get_args(), 1);
        //兼容第二个参数传索引值数组的用法
        if (count($values) === 1 && is_array($values[0])) {
            $values = $values[0];
        }
        foreach ($values as $value) {
            $actions[] = ['find', $value];
        }
        $result = $this->prepare($key)->multi($actions);
        if ($result === false) {
            return false; //查询失败
        }
        $rows = exec_function_array('array_merge', $result);
        foreach ($rows as & $row) {
            $row = array_combine($this->fields, $row);
        }
        return $rows; //向量数组
    }
}
