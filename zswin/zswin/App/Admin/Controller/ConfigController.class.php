<?php

namespace Admin\Controller;

class ConfigController extends CommonController {

       function _filter(&$map) {
       	
    	$map['status']  = 1;
	   }
    
    public function _before_index(){

       
		$this->assign('group',C('CONFIG_GROUP_LIST'));
        
    }
    
   public function after_insert(){
	
	 S('DB_CONFIG_DATA',null);
   }
   public function after_update(){
	
	 S('DB_CONFIG_DATA',null);
   }
	function after_foreverdelete($ids){
		
	   S('DB_CONFIG_DATA',null);
		
	}
    
	function after_selectedDelete($ids){
		
	   S('DB_CONFIG_DATA',null);
		
	}
    /**
     * 批量保存配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function save($config){
    	
    	$old['artid']=M('Config')->where(array('name'=>'WXARTID'))->getField('value');
    	
    
    	
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                
                if($name=='WXARTID'){
                	if($old['artid']!=$value){
                		
                		asyn_sendwx();
                		//$WX->sendwx();
                	}
                	
                }
                if($name=='WXPASS'){
                	$value=think_encrypt($value,UC_AUTH_KEY);
                	
                }
                $Config->where($map)->setField('value', $value);
            }
        }
        
       
        
        S('DB_CONFIG_DATA',null);
        $config_file ='./App/Home/Conf/theme.php';
        $themename = include $config_file;
        
        if($config['WEB_THEME']!=$themename['DEFAULT_THEME']){
        
       
            //写入配置文件
            $theme['DEFAULT_THEME'] = $config['WEB_THEME'];
            file_put_contents($config_file, "<?php \nreturn " . var_export($theme, true) . ";", LOCK_EX);
            dir_delete(RUNTIME_PATH);
       
        	
        }
        
        
        
        $this->mtReturn(200,'网站配置保存成功！','','forward',U('group'));
    }


    // 获取某个标签的配置参数
    public function group() {
        
        $type   =   C('CONFIG_GROUP_LIST');
        
        foreach($type as $key=> $vo){
        	
        	$list[$key]   =   M("Config")->where(array('status'=>1,'groupid'=>$key))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        }
        
       //dump();
            $this->assign('list',$list);
       
        
        
        $this->display();
    }

    /**
     * 配置排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            $ids = I('ids');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(I('group')){
                $map['groupid']	=	I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            
            $this->display();
        }elseif (IS_POST){
            $ids = I('ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->mtReturn(200,'排序成功！');
            }else{
                $this->mtReturn(300,'排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
