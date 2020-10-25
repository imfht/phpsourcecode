<?php
namespace addon\superlinks;

use app\common\controller\AddonBase;

use addon\AddonInterface;


/**
 * 合作单位插件
 * @author 苏南
 */
class Superlinks extends AddonBase implements AddonInterface
{

   
    public $admin_list = array(
        'listKey' => array(
            'title' => '站点名称',
            'type' => '类型',
            'status' => '显示状态',
            'level' => '优先级',
            'create_time' => '创建时间',
        ),
        'model' => 'superlinks',
        'order' => 'level desc,id asc',
        'field' => '*'
    );
    public $custom_adminlist = 'adminlist.html';
 /**
     * 插件安装
     */
    public function addonInstall()
    {
    	$arr=$this->addonInfo();
    	$this->getisHook('friendLink', $arr['name'], $arr['describe']);
    	$this->installAddon($arr);
    	
        $this->addonCacheUpdate();
        
        return [RESULT_SUCCESS, '安装成功'];
    }
    
    /**
     * 插件卸载
     */
    public function addonUninstall()
    {
    	$arr=$this->addonInfo();
    	$this->deleteHook('friendLink');
    	$this->uninstallAddon($arr['name']);
        $this->addonCacheUpdate();
        
        return [RESULT_SUCCESS, '卸载成功'];
    }
    
    /**
     * 插件基本信息
     */
    public function addonInfo()
    {
        
     return [
     		     'name' => 'Superlinks',
        		 'title' => '友情链接', 
        		 'describe' => '友情链接',
        		 'author' => '苏南 newsn.net',
        		 'version' => '1.0',
     		     'has_adminlist' => '1'
               ];
    }


    //钩子方法
    public function friendLink($param)
    {
    	$list = Db::name('superlinks')->where('status = 1')->order('level desc,id asc')->select();
    	foreach($list as $key=>$val){
    		if($val['type'] == 1){//图片连接
    			
    			
    			$list[$key]['savepath'] = get_cover($val['cover_id'],'savepath');
    		}
    	}
    	
    	
 
        
    
        
        $this->assign('list', $list);
        $this->assign('link', $param);
       echo  $this->tplfetch('widget'); 
 
    }

  
}