<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 回收站逻辑
 */
class Trash extends AdminBase
{
    
    /**
     * 获取回收站列表
     */
    public function getTrashList()
    {
        
        $list = [];
        
        $trash_config = parse_config_array('trash_config');
        
        foreach ($trash_config as $k => $v) {
        	
        	$temp = [];
        	if(strpos(strtolower($k),'addon/')!==false){
        		
        		$url_array = explode(SYS_DSS, $k);
        		
        		$class = get_addon_model ( $url_array[1],  $url_array[2] );
        		
        		$model = new $class();
        		
        		$temp['name']   = 'addon@'.$url_array[1].'@'.$url_array[2];
        		
        		$temp['title']=ucfirst($url_array[2]);
        		
        	}else{
        		
        		$model = model($k);
        		
        		$temp['name']   = $k;
        		
        		$temp['title']=$k;
        	}
        	
            [$v];
            
            $temp['model_path']  = $model->class;
            
            $temp['number'] = $model->stat([DATA_COMMON_STATUS => DATA_DELETE]);
            
            $list[] = $temp;
        }
        
        return $list;
    }
    
    /**
     * 获取回收站数据列表
     */
    public function getTrashDataList($model_name = '')
    {
        
        $trash_config = parse_config_array('trash_config');
        
       
        
        
        
        if(strpos(strtolower($model_name),'addon@')!==false){
        	
        	$url_array = explode('@', $model_name);
        	
        	$n=str_replace('@', '/', $model_name);
        	
        	$class = get_addon_model ( $url_array[1],  $url_array[2] );
        	
        	$dynamic_field = $trash_config[$n];
        	
        	$model = new $class();
        	
        }else{
        	$dynamic_field = $trash_config[$model_name];
        	
        	$model =model($model_name);
        	
        }
        $field = 'id,' . TIME_CT_NAME . ','.TIME_UT_NAME.',' . $dynamic_field;
      
        $list = $model->getList([DATA_COMMON_STATUS => DATA_DELETE], $field, 'id desc');
        
        return compact('list', 'dynamic_field', 'model_name');
    }
    
    /**
     * 彻底删除数据
     */
    public function trashDataDel($model_name = '', $id = 0)
    {
    	if(strpos(strtolower($model_name),'addon@')!==false){
    		
    		$url_array = explode('@', $model_name);
    		
    		$class = get_addon_model ( $url_array[1],  $url_array[2] );
    		
    		$model = new $class();
    		
    	}else{
    		
    		$model = model($model_name);
    		
    	}
        
        
        return $model->deleteInfo(['id' => $id], true) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $model->getError()];
    }
    
    /**
     * 恢复数据
     */
    public function restoreData($model_name = '', $id = 0)
    {
        
    if(strpos(strtolower($model_name),'addon@')!==false){
    		
    		$url_array = explode('@', $model_name);
    		
    		$class = get_addon_model ( $url_array[1],  $url_array[2] );
    		
    		$model = new $class();
    		
    	}else{
    		
    		$model = model($model_name);
    		
    	}
        
        return $model->setFieldValue(['id' => $id], DATA_COMMON_STATUS, DATA_NORMAL) ? [RESULT_SUCCESS, '数据恢复成功'] : [RESULT_ERROR, $model->getError()];
    }
 
}
