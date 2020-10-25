<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace addon;

/**
 * 插件接口
 */
interface AddonInterface
{
    
    /**
     * 插件安装
     */
    public function addonInstall();
    
    /**
     * 插件卸载
     */
    public function addonUninstall();
    
    /**
     * 插件信息
     */
    public function addonInfo();
    

}
