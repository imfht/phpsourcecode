<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Model;
use think\Db;
use think\Cache;

/**
 * 模型基类
 */
class ModelBase extends Model
{
	
    // 查询对象
    private static $ob_query = null;

    /**
     * 更新缓存版本号
     */
    public function updateCacheVersion()
    {
        
        //set_cache_version($this->name);
    }
    
    /**
     * 状态获取器
     */
    public function getStatusTextAttr()
    {
       
        $status = [DATA_DELETE => '删除', DATA_DISABLE => '禁用', DATA_NORMAL => '启用',3 => '认证'];
        
        return $status[$this->data[DATA_COMMON_STATUS]];
    }
    
    /**
     * 设置数据
     */
    final protected function setInfo($data = [], $where = [], $sequence = null)
    {
        
        $pk = $this->getPk();
        
        $return_data = null;
        
        if (empty($data[$pk])) {
            
            $return_data = $this->allowField(true)->save($data, $where, $sequence);
            
            $return_data && $this->updateCacheVersion();
        } else {
        	
        	//$return_data = $this->allowField(true)->isUpdate(true,$where)->save($data);
        	//$return_data && $this->updateCacheVersion();
        	
        	if(empty($where)){
        		$where[$pk]=$data[$pk];
        	}
        	
           $return_data = $this->updateInfo($where, $data);
        }
        
        return $return_data;
    }
    
    /**
     * 新增数据
     */
    final protected function addInfo($data = [], $is_return_pk = true)
    {
     
    	$data[TIME_CT_NAME] = TIME_NOW;
        $return_data = $this->insert($data, false, $is_return_pk);
        
        $return_data && $this->updateCacheVersion();
        
        return $return_data;
    }
    
    /**
     * 更新数据
     */
    final protected function updateInfo($where = [], $data = [])
    {
    	
    	//$return_data = $this->allowField(true)->where($where)->update($data);
       
    	$return_data = $this->allowField(true)->save($data,$where);
    	
        $return_data && $this->updateCacheVersion();
        
        return $return_data;
    }
    /**
     * 更新数据1
     */
    final protected function updatenosaveInfo($where = [], $data = [])
    {
    	
    	$data[TIME_UT_NAME] = TIME_NOW;
    	$return_data = $this->allowField(true)->where($where)->update($data);
    	 
    	
    	 
    	$return_data && $this->updateCacheVersion();
    
    	return $return_data;
    }
    
    /**
     * 统计数据
     */
    public function stat($where = [], $stat_type = 'count', $field = 'id')
    {
        
        return $this->where($where)->$stat_type($field);
    }
    
    /**
     * 设置数据列表
     */
    final protected function setList($data_list = [], $replace = false)
    {
        
        $return_data = $this->saveAll($data_list, $replace);
        
        $return_data && $this->updateCacheVersion();
        
        return $return_data;
    }
    
    /**
     * 设置某个字段值
     */
    final protected function setFieldValue($where = [], $field = '', $value = '')
    {
        
        return $this->updateInfo($where, [$field => $value]);
    }
    /**
     * 批量删除数据
     */
    final protected function deleteAllInfo($where = [], $is_true = false)
    {
    
    	if ($is_true) {
    
    		$return_data = $this->where($where)->delete();
    
    		$return_data && $this->updateCacheVersion();
    	} else {
    
    		$return_data = $this->setFieldValue($where, DATA_COMMON_STATUS, DATA_DELETE);
    	}
    
    	return $return_data;
    }
    /**
     * 删除数据
     */
    final protected function deleteInfo($where = [], $is_true = false)
    {
        
        if ($is_true) {
            
            $return_data = $this->where($where)->delete();
            
            $return_data && $this->updateCacheVersion();
        } else {
            
            $return_data = $this->setFieldValue($where, DATA_COMMON_STATUS, DATA_DELETE);
        }
        
        return $return_data;
    }
    
    /**
     * 获取某个列的数组
     */
    final protected function getColumn($where = [], $field = '', $key = '')
    {
        
        return Db::name($this->name)->where($where)->column($field, $key);
    }
    
    /**
     * 获取某个字段的值
     */
    final protected function getValue($where = [], $field = '', $default = null, $force = false)
    {
        
        return Db::name($this->name)->where($where)->value($field, $default, $force);
    }
    
    /**
     * 获取单条数据
     */
    final protected function getInfo($where = [], $field = true, $join = null,$data = null,$alias='m')
    {
    	
    	
    
        self::$ob_query = $this->where($where)->field($field);
        
        if(!empty($alias)  &&!empty($join)){
        	self::$ob_query = self::$ob_query->alias($alias);
        }
        
      
        !empty($join) && self::$ob_query = self::$ob_query->join($join);
        
      
        
        
        
        return $this->getResultData(DATA_DISABLE, $data);
    }
    
    /**
     * 获取列表数据
     */
    final protected function getList($where = [], $field = true, $order = '', $paginate = 0, $join = [], $group = '', $limit = null,$data = null,$alias='m')
    {
       
    	
        empty($join) && !isset($where[DATA_COMMON_STATUS]) && $where[DATA_COMMON_STATUS] = ['neq', DATA_DELETE];
        
        self::$ob_query = $this->where($where)->order($order);

        
        self::$ob_query = self::$ob_query->field($field);
       
        
      
        if(!empty($alias)  &&!empty($join)){
        	self::$ob_query = self::$ob_query->alias($alias);
        }
       
        
        
        !empty($join)  && self::$ob_query = self::$ob_query->join($join);
        
        !empty($group) && self::$ob_query = self::$ob_query->group($group);
    
        !empty($limit) && self::$ob_query = self::$ob_query->limit($limit);
        

            if(DATA_DISABLE === $paginate){
        	
        	if(config('list_rows')>0){
        		$paginate = config('list_rows');
        		
        	}else{
        		$paginate = DB_LIST_ROWS;
        	}
        }
       

        
        return $this->getResultData($paginate, $data);
    }
    
    /**
     * 获取结果数据
     */
    final protected function getResultData($paginate = 0, $data = null)
    {
        
        $result_data = null;

            
            $backtrace = debug_backtrace(false, 2);

            array_shift($backtrace);

            $function = $backtrace[0]['function'];

            if($function == 'getList') {

            	
            	
            	
            		$result_data = false !== $paginate ? self::$ob_query->paginate($paginate) : self::$ob_query->select($data);
            	
               

            } else {

                $result_data = self::$ob_query->find($data);
            }

        
        self::$ob_query->removeOption();
       
        return $result_data;
    }
}
