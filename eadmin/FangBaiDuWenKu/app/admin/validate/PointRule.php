<?php

namespace app\admin\validate;

/**
 * 验证器
 */
class PointRule extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'controller'  => 'require|checkUnique',

    ];
    
    // 验证提示
    protected $message  =   [
        
        'controller.require'    => '动作不能为空',
        'controller.checkUnique'     => '该规则已存在',
       

    ];
    // 自定义验证规则
    protected function checkUnique($value,$rule,$data)
    {
    	if(!empty($data['id'])){
    		
    		if(model('pointRule')->where(['controller'=>$data["controller"],'scoretype'=>$data["scoretype"],'id'=>array('neq',$data['id'])])->count()>0){
    			return false;
    		}else{
    			return true;
    		}
    		
    	}else{
    		if(model('pointRule')->where(['controller'=>$data["controller"],'scoretype'=>$data["scoretype"]])->count()>0){
    			return false;
    		}else{
    			return true;
    		}
    	}
    	
    	
    			
    }
    // 应用场景
    protected $scene = [
    	'edit'  =>  ['controller'],
        'add'  =>  ['controller'],
    ];
    
}
