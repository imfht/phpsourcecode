<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 短信日志-模型
 * 
 * @author 牧羊人
 * @date 2019-02-14
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class SmsLogModel extends BaseModel
{
    // 设置数据表
    protected $name = 'sms_log';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            //短信类型
            if($info['type']) {
                $info['type_name'] = Config::get('adminConfig.sms_log_type')[$info['type']];
            }
            
            //状态名称
            $info['status_name'] = Config::get('adminConfig.sms_log_status')[$info['status']];
            
        }
        return $info;
    }
    
}