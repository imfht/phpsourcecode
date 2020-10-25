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
 * 后台主页-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-18
 */
namespace Admin\Controller;
class IndexController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 首页入口
     * 
     * @author 牧羊人
     * @date 2018-06-21
     */
    public function index() {
        $this->display();
    }
    
    
    /**
     * 后台主页入口
     * 
     * @author 牧羊人
     * @date 2018-07-09
     */
    public function main() {
        $this->display();
    }
    
    /**
     * 消息查阅
     */
    public function msg() {
        $this->display();
    }
    
}