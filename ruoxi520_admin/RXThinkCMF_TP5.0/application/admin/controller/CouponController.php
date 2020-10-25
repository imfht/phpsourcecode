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
 * 优惠券-控制器
 * 
 * @author 牧羊人
 * @date 2019-01-27
 */
namespace app\admin\controller;
use app\admin\model\CouponModel;
use app\admin\service\CouponService;
class CouponController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2019-01-27
     */
    function __construct() 
    {
        parent::__construct();
        $this->model = new CouponModel();
        $this->service = new CouponService();
    }
    
}