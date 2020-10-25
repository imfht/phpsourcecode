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
 * 文章-控制器
 * 
 * @author 牧羊人
 * @date 2019-02-14
 */
namespace app\admin\controller;
use app\admin\model\ArticleModel;
use app\admin\service\ArticleService;
class ArticleController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2019-02-14
     */
    function __construct() 
    {
        parent::__construct();
        $this->model = new ArticleModel();
        $this->service = new ArticleService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::index()
     */
    function index()
    {
        if(IS_POST) {
            $message = $this->service->getList();
            return $message;
        }
        if($_GET['simple']) {
            return $this->render("article/simple_select");
        }
        return $this->render();
    }
    
    /**
     * 设置文章是否显示
     * 
     * @author 牧羊人
     * @date 2019-02-14
     */
    function setIsShow()
    {
        if(IS_POST) {
            $message = $this->service->setIsShow();
            return $message;
        }
    }
    
}