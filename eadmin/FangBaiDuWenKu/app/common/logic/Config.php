<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;

/**
 * 配置逻辑
 */
class Config extends LogicBase
{
    
    // 配置模型
    public static $configModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$configModel = model($this->name);
    }
    
    /**
     * 获取配置列表
     */
    public function getConfigList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return self::$configModel->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 获取配置信息
     */
    public function getConfigInfo($where = [], $field = true)
    {
        
        return self::$configModel->getInfo($where, $field);
    }
    
    /**
     * 系统设置
     */
    public function settingSave($data = [])
    {
    	$str1 = '<?php return [';
    	$str1 .="  'datetime_format'           => false,";
    	$str1 .="'app_namespace'           => 'app',";
    
        foreach ($data as $name => $value) {
            
            $where = array('name' => $name);
          
         
          
          
           if($name=='WEB_SITE_FOOTER'){
            
           
           $value=htmlspecialchars_decode($value);
          

           }
           
           	self::$configModel->updatenosaveInfo($where,array('value'=>$value));
          
          
            
            if($name=='site_tpl'){
            
            $path = 'app/extra/web.php';
            
            $file = include $path;
            $config = array(
            		'WEB_TPL' => $value,

            );
            $res = array_merge($file, $config);
            $str = '<?php return [';
            
            foreach ($res as $key => $value){
            	$str .= '\''.$key.'\''.'=>'.'\''.$value.'\''.',';
            };
            $str .= ']; ';
            file_put_contents($path, $str);
            
            }
           
           
            if($name=='OPEN_ROUTER'){
            	 
            	
                 
         
                 if($value==1){
           	         $str1 .="'url_route_on'           => true,";
                 }else{
           	         $str1 .="'url_route_on'           => false,";
                 }
             

            }
            if($name=='DEVELOP_MODE'){
            
            	 
            	 
            	 
            	if($value==1){
            		$str1 .="'app_debug'           => true,";
            	}else{
            		$str1 .="'app_debug'           => false,";
            	}
            

            }
            if($name=='SHOW_PAGE_TRACE'){
            
            	 
            	 
            	 
            	if($value==1){
            		$str1 .="'app_trace'           => true,";
            	}else{
            		$str1 .="'app_trace'           => false,";
            	}

            	

            }
           
            
           
          
        
         
           
            
            
            
            
            
            
        }
        $path = 'app/config.php';
        $str1 .= ']; ';
        file_put_contents($path, $str1);
        
       
        return [RESULT_SUCCESS, '设置保存成功','d',$data];
    }
    
    /**
     * 配置添加
     */
    public function configAdd($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('configList', array('group' => $data['group'] ? $data['group'] : 0));
        
        return self::$configModel->setInfo($data) ? [RESULT_SUCCESS, '配置添加成功', $url] : [RESULT_ERROR, self::$configModel->getError()];
    }
    
    /**
     * 配置编辑
     */
    public function configEdit($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('edit')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('configList', array('group' => $data['group'] ? $data['group'] : 0));
        
        return self::$configModel->setInfo($data) ? [RESULT_SUCCESS, '配置编辑成功', $url] : [RESULT_ERROR, self::$configModel->getError()];
    }
    
    /**
     * 配置删除
     */
    public function configDel($where = [])
    {
        
        return self::$configModel->deleteInfo($where) ? [RESULT_SUCCESS, '配置删除成功'] : [RESULT_ERROR, self::$configModel->getError()];
    }
    
    /**
     * 批量删除
     */
    public function configAlldel($ids)
    {
    	

    return self::$configModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '配置删除成功'] : [RESULT_ERROR, self::$configModel->getError()];
    }  
    
}
