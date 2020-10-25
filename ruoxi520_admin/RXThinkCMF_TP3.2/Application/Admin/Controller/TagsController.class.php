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
 * 标签-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-19
 */
namespace Admin\Controller;
use Admin\Model\TagsModel;
use Admin\Service\TagsService;
class TagsController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new TagsModel();
        $this->service = new TagsService();
    }
}