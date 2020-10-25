<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace addon\imginput;

use app\common\controller\AddonBase;

use addon\AddonInterface;

/**
 * 图片上传插件
 */
class Imginput extends AddonBase implements AddonInterface
{
    
    /**
     * 实现钩子
     */
    public function ImgUpload($param = [])
    {
        
        $this->assign('addons_data', $param);
        
        $this->addonTemplate('index/index');
    }
    
    /**
     * 插件安装
     */
    public function addonInstall()
    {
    	$arr=$this->addonInfo();
    	$this->getisHook('ImgUpload', $arr['name'], $arr['describe']);
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
    	$this->deleteHook('ImgUpload');
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
        'name' => 'Imginput', 
        'title' => '图片上传', 
        'describe' => '图片上传插件，可支持拖动图片及批量上传', 
        'author' => 'Bigotry', 
        'version' => '1.0',
        'has_adminlist' => '0'
        ];
    }
    

}
