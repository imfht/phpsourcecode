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
 * 积分商城品牌-控制器
 * 
 * @author 牧羊人
 * @date 2019-01-11
 */
namespace Admin\Controller;
use Admin\Model\PointsProductBrandModel;
use Admin\Service\PointsProductBrandService;
class PointsProductBrandController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new PointsProductBrandModel();
        $this->service = new PointsProductBrandService();
    }
}