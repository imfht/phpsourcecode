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
 * 测试模块-控制器
 * 
 * @author 牧羊人
 * @date 2018-11-22
 */
namespace Admin\Controller;
class TestController extends BaseController {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 获取首页
     * 
     * @author 牧羊人
     * @date 2018-11-22
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        $this->render("test.index");
    }
    
}