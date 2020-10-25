<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;

/**
 * 云存储服务
 */
class Storage extends ServiceBase implements BaseInterface
{
    
    
    /**
     * 服务基本信息
     */
    public function serviceInfo()
    {
        
        return ['service_name' => '云存储服务', 'service_class' => 'Storage', 'service_describe' => '系统云存储服务，用于整合多个云储存平台', 'author' => 'Bigotry', 'version' => '1.0'];
    }
    
    /**
     * 上传文件
     */
    public function upload($file_id = 0)
    {
        
        return $this->driver->upload($file_id);
    }
}
