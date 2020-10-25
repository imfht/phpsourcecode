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
 * 分类属性关系-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-24
 */
namespace Admin\Controller;
use Admin\Model\CateAttributeRelationModel;
use Admin\Service\CateAttributeRelationService;
class CateAttributeRelationController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new CateAttributeRelationModel();
        $this->service = new CateAttributeRelationService();
    }
}