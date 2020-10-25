<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 空控制器
 * 
 * @author 牧羊人
 * @date 2018-12-08
 */
namespace app\admin\controller;
class EmptyController extends AdminBaseController {
    
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 空控制器入口
     * 
     * @author 牧羊人
     * @date 2018-12-08
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::index()
     */
    function index() 
    {
        return $this->render("public/404");
    }
}