<?php

/*
 *  @author myf
 *  @date 2014-11-13 20:01:08
 *  @Description myfmvc 数据Model基础类
 *  @web http://www.minyifei.cn
 */
namespace Myf\Mvc;

class Model {
    
    private $_db;
    private $_className;
    
    public function __construct() {
        $className = get_class($this);
        //默认连接的数据库配置项
        $dbName = "DEFAULT_DB";
        //判断子类是否有getSource方法，如果有，代表要设置新的数据库配置项
        $hasSourceMethod = method_exists($this, "getSource");
        if($hasSourceMethod){
            $dbName = $this->getSource();
        }
        $tableName = toUnderLineName(getClassFileName($className));
        $this->_className = $tableName;
        $db = M($tableName,$dbName);
        $this->_db = $db;
    }
    
    /**
     * 查询记录
     * @param Array,String $args 查询条件
     * @return Array 记录集
     */
    public function find($args=null){
        $this->setOptions($args);
        return $this->_db->find();
    }
    
    /**
     * 查找一条记录
     * @param Array,String $args 检索条件
     * @return Object 记录对象
     */
    public function findFirst($args=null){
        $this->setOptions($args);
        return $this->_db->findFirst();
    }
    
    /**
     * 设置查询条件
     * @param type $conditions
     * @param type $bind
     * @return \Myf\Mvc\Model
     */
    public function where($conditions,$bind=array()){
        $this->_db->where($conditions);
        $this->_db->bind($bind);
        return $this;
    }
    
    /**
     * 设置排序
     * @param type $order
     * @return \Myf\Mvc\Model
     */
    public function order($order){
         $this->_db->order($order);
         return $this;
    }
    
    /**
     * 设置查询字段
     * @param Array,String $columns
     * @return \Myf\Mvc\Model
     */
    public function columns($columns){
        if(is_array($columns)){
            $columns = join(",", $columns);
        }
        $this->_db->field($columns);
        return $this;
    }
    
    /**
     * 设置排序
     * @param string $limit
     *  @return \Myf\Mvc\Model
     */
    public function limit($limit){
        $this->_db->limit($limit);
        return $this;
    }
    
    /**
     * 设置查询条件
     * @param Array,String $args 检索条件
     */
    private function setOptions($args){
        $this->_db->table($this->_className);
        if(is_array($args)){
            //查询条件
            $conditions = isset($args["conditions"])?$args["conditions"]:$args[0];
            $this->_db->where($conditions);
            //查询列
            if(isset($args["columns"])){
                $this->_db->field($args["columns"]);
            }
            //绑定数据
            if(isset($args["bind"]) && is_array($args["bind"])){
                $this->_db->bind($args["bind"]);
            }
            //排序
            if(isset($args["order"])){
                $this->_db->order($args["order"]);
            }
            //截取数据
            if(isset($args["limit"])){
                $this->_db->limit($args["limit"]);
            }
            //分组
            if(isset($args["group"])){
                $this->_db->group($args["group"]);
            }
        }else if(is_string($args)){
             $this->_db->where($args);
        }
    }
    
    /**
     * 保存数据，如果有主键已经赋值则更新
     * @param Array $data 数据对象
     * @return int 最后插入的主键，如果是更新返回影响行数
     */
    public function save($data=null){
        //执行前置执行函数
        if(method_exists($this, "beforeSave")){
            $this->beforeSave();
        }
        //判断数据对象是否为空，如果为空，序列号self
        if(!is_array($data)){
            $data = objectToArray($this);
        }
        $isInsert = true;
        //判断如果有主键，代表更新记录
        $key = $this->_db->findPrimaryKey();
        if(array_key_exists($key,$data)){
            $keyValue = $data[$key];
            unset($data[$key]);
            if(isset($keyValue)){
                $isInsert = false;
                //主键有值代表更新
                $rowId = $this->update($data, "id=:id", array(":id"=>$keyValue));
            }
        }
        if($isInsert){
            $rowId = $this->insert($data);
            $this->$key = $rowId;
        }
        //执行后置函数
        if(method_exists($this, "afterSave")){
            $this->afterSave();
        }
        return $rowId;
    }
    
    /**
     * 更新记录
     * @param Array $data 数据数组
     * @param String $where 条件
     * @param Array $bindArray 条件对应数组
     * @return int 影响行数
     */
    public function update($data=null,$where=null,$bindArray=array()){
         //执行前置执行函数
        if(method_exists($this, "beforeUpdate")){
            $this->beforeUpdate();
        }
        $this->_db->table($this->_className);
        if(!is_array($data)){
            $data = objectToArray($this);
        }
        $rows = $this->_db->update($data,$where,$bindArray);
       //执行后置函数
        if(method_exists($this, "afterUpdate")){
            $this->afterUpdate();
        }
        return $rows;
    }
    
    /**
     * 添加数据
     * @param Array $data 添加数据
     * @return int 插入的主键
     */
    public function insert($data=null){
        //执行前置执行函数
        if(method_exists($this, "beforeInsert")){
            $this->beforeInsert();
        }
        $this->_db->table($this->_className);
        if(!is_array($data)){
            $data = objectToArray($this);
        }
        $key = $this->_db->findPrimaryKey();
        $rowId = $this->_db->add($data);
        if(isset($key)){
            $this->$key = $rowId;
        }
         //执行后置函数
        if(method_exists($this, "afterInsert")){
            $this->afterInsert();
        }
        return $rowId;
    }
    
    public function add($data=null){
        $this->insert($data);
    }
    
    /**
     * 删除数据
     * @param Array,String $args
     */
    public function delete($args=null){
        //执行前置执行函数
        if(method_exists($this, "beforeDelete")){
            $this->beforeDelete();
        }
        if(is_null($args)){
            $data = objectToArray($this);
            $key = $this->_db->findPrimaryKey();
            if(array_key_exists($key,$data)){
                $keyValue = $data[$key];
                $args = array(
                    "{$key}=:key",
                    "bind"=>array(":key"=>$keyValue),
                );
            }else{
                return 0;
            }
        }
        $this->setOptions($args);
        $rows = $this->_db->delete();
         //执行后置函数
        if(method_exists($this, "afterDelete")){
            $this->afterDelete();
        }
        return $rows;
    }
    
    /**
     * 查询数量
     * @param Array,String $args
     * @return int
     */
    public function count($args=null){
        $this->setOptions($args);
        return $this->_db->count();
    }
    
}
