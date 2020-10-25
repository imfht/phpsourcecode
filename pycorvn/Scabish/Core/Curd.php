<?php
namespace Scabish\Core;

use SCS;
use Exception;
use Scabish\Core\Db;

/**
 * Scabish\Core\Curd
 * 数据库CURD操作类，操作的表必须含有单一主键字段
 * @example
 * $curd = SCS::Curd('Admin'); // 实例化表模型
 * $curd->Create(['name' => 'admin', 'addTime' => '{NOW()}']); // 创建记录
 * echo $curd->name, $curd->addTime; // 读取属性
 * $curd = SCS::Curd('Admin')->Read('name = "admin"'); // 字段name为admin的记录
 * $curd = SCS::Curd('Admin')->Read(3); // 主键值为3的记录 
 * $curd->name = 'keluo'; // 设置name字段值
 * $curd->Update(); // 更新全部字段
 * $curd->Update(['name' => 'keluo']); // 更新特定字段
 * $curd->Delete(); // 删除记录
 * $list = SCS::Curd('Admin')->ReadAll('name LIKE "%a%"'); // 多条CURD实例
 * $list = SCS::Curd('Admin')->ReadAll(null, 'ORDER BY id DESC LIMIT 10'); // 其他条件
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-02-24
 */
class Curd {
    
    private $_connect = null;
    private $_table = null;
    private $_fields = [];
    private $_pkName = null;
    private $_pkValue = null;
    private $_attributes;
    
    /**
     * 实例化单表原子操作模型
     * @param string $table 表名
     * @param string $connect 数据库连接标识
     * @throws Exception
     */
    public function __construct($table, $connect = null) {
        $this->_connect = $connect ? $connect : 'default';
        if(!preg_match('/^\{.*\}$/s', $table)) { // 表全名 ，如'{SC_Admin}'
            $this->_table = '{'.SCS::Instance()->db[$this->_connect]['prefix'].$table.'}';
        } else { // 默认自动加表前缀
            $this->_table = $table;
        }
        $db = new Db($connect);
        $this->_pkName = $db->GetTableStruct($this->_table, 'pk');
        $this->_fields = $db->GetTableStruct($this->_table);
        
        if(is_null($this->_pkName)) throw new Exception('There is no primary key in table '.$this->_table);
    }
    
    /**
     * 创建记录
     * @param array $data 数据，field=>value
     * @param boolean $refresh 是否刷新当前数据
     * @param boolean $replace 主键重复则替换
     * @return SCCurd
     */
    public function Create(array $data, $refresh = true, $replace = false) {
        $this->_pkValue = SCS::Db($this->_connect)->From($this->_table)->Insert($data, $replace);
        return $refresh ? $this->Read($this->_pkValue) : $this;
    }
    
    /**
     * 更新记录
     * @param array $data 数据，field=>value
     * @param boolean $refresh 是否刷新当前数据
     */
    public function Update(array $data, $refresh = true) {
        foreach($data as $field=>$value) {
            if(in_array($field, $this->_fields)) {
                $this->_attributes->$field = $value;
            }
        }
        
        SCS::Db($this->_connect)->From($this->_table)->Where($this->_pkName.' = "'.$this->_pkValue.'"')->Update($data);
        
        // 重新更新主键值(考虑到上一步更新操作可能会更新主键值)
        $this->_pkValue = $this->_attributes->{$this->_pkName};
        
        return $refresh ? $this->Read($this->_pkValue) : $this;
    }
    
    /**
     * 读取记录
     * @param number|string $condition 为数值时表示主键值，为表达式则表示条件
     * @param boolean $returnOnlyAttributes 是否仅返回属性合集
     * @return object
     */
    public function Read($condition, $extra = null, $returnOnlyAttributes = false) {
        if(is_numeric($condition)) {
            $record = SCS::Db($this->_connect)->From($this->_table)->Where($this->_pkName.' = "'.$condition.'"')->Fetch();
        } else {
            $table = !preg_match('/^\{.*\}$/s', $this->_table) ? : substr($this->_table, 1, -1);
            $condition = trim($condition);
            if(!strlen($condition)) throw new Exception('Reading condition is empty. [table: '.$table.']');
            $record = SCS::Db($this->_connect)->Find('SELECT * FROM '.$table.
            ($condition ? ' WHERE '.$condition : '').($extra ? (' '.$extra) : ''));
        }
        
        if($record) {
            $this->_attributes = $record;
            $this->_pkValue = $this->_attributes->{$this->_pkName};
            return $returnOnlyAttributes ? $this->Attributes() : $this;
        }
        return false;
    }
    
    /**
     * 删除记录
     */
    public function Delete() {
        return SCS::Db($this->_connect)->From($this->_table)->Where($this->_pkName.' = "'.$this->_pkValue.'"')->Delete();
        unset($this);
    }
    
    /**
     * 返回多条记录 
     * @param string $condition
     * @param string $extra
     * @param boolean $returnOnlyAttributes 是否仅返回属性合集
     * @return array
     */
    public function ReadAll($condition = null, $extra = null, $returnOnlyAttributes = false) {
        $table = !preg_match('/^\{.*\}$/s', $this->_table) ? : substr($this->_table, 1, -1);
        $records = SCS::Db($this->_connect)->FindAll('SELECT * FROM '.$table.
            ($condition ? ' WHERE '.$condition : '').($extra ? (' '.$extra) : ''));
        $data = [];
        foreach($records as $record) {
            $curd = new Curd($this->_table, $this->_connect);
            $curd->SetAttributes($record);
            array_push($data, $returnOnlyAttributes ? $curd->Attributes() : $curd);
        }
        return $data;
    }
    
    /**
     * 获取所有属性
     * @return stdClass
     */
    public function Attributes($returnArray = false) {
        return $returnArray ? (array)$this->_attributes : $this->_attributes;
    }
    
    /**
     * 获取字段值
     * @param string $field
     * @throws Exception
     */
    public function __get($field) {
        if(in_array($field, $this->_fields)) {
            return $this->_attributes->$field;
        } else {
            throw new Exception('There is no field named "'.$field.'" in table "'.$this->_table.'"');
        }
    }
    
    /**
     * 设置字段值
     * @param string $field
     * @param mixed $value
     * @throws Exception
     */
    public function __set($field, $value) {
        if(in_array($field, $this->_fields)) {
            $this->_attributes->$field= $value;
        } else {
            throw new Exception('There is no field named "'.$field.'" in table "'.$this->_table.'"');
        }
    }
    
    public function __toString() {
        return json_encode($this->_attributes);
    }
    
    private function SetAttributes(\stdClass $attributes) {
        $this->_attributes = $attributes;
        $this->_pkValue = $this->_attributes->{$this->_pkName};
    }
}