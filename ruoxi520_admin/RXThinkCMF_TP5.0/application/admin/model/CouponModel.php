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
 * 优惠券-模型
 * 
 * @author 牧羊人
 * @date 2019-01-27
 */
namespace app\admin\model;
use app\common\model\BaseModel;
class CouponModel extends BaseModel
{
    // 设置数据表
    protected $name = 'coupon';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-01-27
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info)
        {
            
            //TODO...
            
        }
        return $info;
    }
    
}