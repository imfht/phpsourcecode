<?php

namespace addon\editor;

use app\common\controller\AddonBase;

use addon\AddonInterface;

/**
 * 富文本编辑器插件
 */
class Editor extends AddonBase implements AddonInterface
{
    
    /**
     * 实现钩子
     */
    public function ArticleEditor($param = [])
    {
        
        $this->assign('addons_data', $param);
        
        $addons_config = $this->getConfig();
        
        if(empty($addons_config['editor_height'])){
        	
        	$addons_config['editor_height'] = '300px';
        }
        
        $this->assign('addons_config', $addons_config);
        
        $this->addonTemplate('index/index');
    }
    
    /**
     * 插件安装
     */
    public function addonInstall()
    {
    	$arr=$this->addonInfo();
    	$this->getisHook('ArticleEditor', $arr['name'], $arr['describe']);
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
    	$this->deleteHook('ArticleEditor');
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
     		     'name' => 'Editor',
        		 'title' => '文本编辑器', 
        		 'describe' => '富文本编辑器',
        		 'author' => 'Bigotry',
        		 'version' => '1.0',
                 'has_adminlist' => '0'
     		
               ];
    }
    

}
