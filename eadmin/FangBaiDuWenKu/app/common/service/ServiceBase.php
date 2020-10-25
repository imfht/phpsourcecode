<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;

use app\common\model\ModelBase;

/**
 * 基础服务
 */
class ServiceBase extends ModelBase
{
    
    // 驱动
    protected $driver = null;
    
    /**
     * 驱动参数
     */
    public function driverParam($driver_class = '')
    {
        
        $this->setDriver($driver_class);
        
        return $this->driver->getDriverParam();
    }
    
    /**
     * 驱动配置信息
     */
    public function driverConfig($driver_name = '')
    {
        
        $driver_info = model('Driver')->getInfo(['driver_name' => $driver_name]);
        
        empty($driver_info) && die('未安装此驱动，请先安装');
        
        $driver_info_arr = $driver_info->toArray();
        
        return unserialize($driver_info_arr['config']);
    }
    
    /**
     * 设置驱动
     */
    public function setDriver($driver_class = '')
    {
        
        $this->driver = model(ucfirst($driver_class), LAYER_SERVICE_NAME . SYS_DS_CONS . strtolower($this->name) . SYS_DS_CONS . SYS_DRIVER_DIR_NAME);
    }
}
