<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 公共模型-基类
 * 
 * @author 牧羊人
 * @date 2018-06-21
 */
namespace Common\Model;
use Think\Model;
class BaseModel extends Model {
    protected $tableName = ''; 
    public function __construct($table='') {
        //继承父类前先赋值
        $this->tableName = $table;
        parent::__construct();
    }
    
    /**
     * 重置缓存
     *
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function _cacheReset($id, $data=array(), $isEdit=true) {
        if (!$data) {
            return $this->resetFuncCache('info', $id);
        }
        if ($isEdit) {
            $info = $this->getFuncCache("info", $id);
        }
        $info = $info ? $info : array();
        if (is_array($data)) {
            $info = array_merge($info, $data);
        } else {
            $info = $data;
        }
        $key = $this->getFuncKey("info", $id);
        return $this->setCache($key, $info);
    
    }
    
    /**
     * 删除缓存
     *
     * @author 牧羊人
     * @date 2018-07-10
     */
    public function _cacheDelete($id) {
        return $this->deleteFuncCache("info", $id);
    }


    /**
     * 获取函数缓存
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function getFuncCache($funcName){
        //拼接key
        $argList = func_get_args();
        if ($this->tableName)  array_unshift($argList, $this->tablePrefix . $this->tableName);
        $key = implode("_", $argList);
        //从缓存中获取
        $data = $this->getCache($key);
        if(!$data){
            array_shift($argList);
            if ($this->tableName) array_shift($argList);
            $act = "_cache".ucfirst($funcName);
            $data = call_user_func_array(array($this, $act), $argList);
            $this->setCache($key, $data);
        }
        return $data ;
    }
    
    /**
     * 获取数据信息
     *
     * @author 牧羊人
     * @date 2018-07-11
     */
    private function _cacheInfo($id) {
        $info = $this->find((int)$id);
        return $info;
    }
    
    /**
     * 删除函数缓存
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    private function deleteFuncCache($funcName) {
        $argList = func_get_args();
        if ($this->tableName)  array_unshift($argList, $this->tablePrefix . $this->tableName);
        $key = implode("_", $argList);
        return $this->deleteCache($key);
    }
    
    /**
     * 重置函数缓存
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function resetFuncCache($funcName) {
//         //拼接key
//         $argList = func_get_args();
//         if ($this->tableName)  array_unshift($argList, $this->tablePrefix . $this->tableName);
//         $key = implode("_", $argList);
        
        $key = $this->getFuncKey($funcName);
        
        //延迟1s缓存
        $this->deleteCache($key, 0);
    }

    /**
     * 获取缓存
     *
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function getCache($key) {
        $key = $this->getKey($key);
        $data = S($key);
        if($data) {
            $data = json_decode(gzuncompress($data),true);
        }
        return $data;
    }
    
    /**
     * 设置缓存
     *
     * @author 牧羊人
     * @date 2018-07-11
     * @param unknown $key 缓存KEY值
     * @param unknown $data 缓存数据
     * @param number $ttl 缓存时间
     */
    public function setCache($key, $data, $ttl = 0) {
        $key = $this->getKey($key);
        if(!$data) return false;
        $isGzcompress = gzcompress(json_encode($data));
        if($isGzcompress){
            $data = S($key,$isGzcompress,$ttl);
        }
        return $data;
    }
    
    /**
     * 删除缓存
     *
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function deleteCache($key, $delay=0) {
        $key = $this->getKey($key);
        if(S($key)){
            $result = S($key,NULL);
        }
        return $result;
    }
    
    /**
     * 拼接缓存前缀
     *
     * @author 牧羊人
     * @date 2018-07-11
     */
    private function getKey($key){
        //TODO...
        return $key;
    }
    
    /**
     * 获取函数缓存Key
     *
     * @author 牧羊人
     * @date 2018-07-11
     * @param unknown $funcName 函数方法名
     * @return string
     */
    private function getFuncKey($funcName) {
        //拼接key
        $argList = func_get_args();
        if ($this->tableName)  array_unshift($argList, $this->tablePrefix . $this->tableName);
        $key = implode("_", $argList);
        return $key;
    }
    
    /**
     * 删除数据前的回调方法
     *
     * @author 牧羊人
     * @date 2018-07-11
     * (non-PHPdoc)
     * @see \Think\Model::_before_delete()
     */
    protected function _before_delete($options) {
    
    }
    
    /**
     * 删除成功后的回调方法
     *
     * @author 牧羊人
     * @date 2018-07-11
     * (non-PHPdoc)
     * @see \Think\Model::_after_delete()
     */
    protected function _after_delete($data,$options) {
    
    }
    
    /**
     * 更新数据前的回调方法
     * 
     * @author 牧羊人
     * @date 2018-07-11
     * (non-PHPdoc)
     * @see \Think\Model::_before_update()
     */
    protected function _before_update(&$data,$options) {
        
    }
    
    /**
     * 更新成功后的回调方法
     * 
     * @author 牧羊人
     * @date 2018-07-11
     * (non-PHPdoc)
     * @see \Think\Model::_after_update()
     */
    protected function _after_update($data,$options) {
        
    }
    
}
