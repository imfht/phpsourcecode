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
 * CMS管理-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Controller;
use Admin\Model\ArticleModel;
use Admin\Service\ArticleService;
class ArticleController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new ArticleModel();
        $this->service = new ArticleService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-08-17
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        if(IS_POST) {
            $message = $this->service->getList();
            $this->ajaxReturn($message);
            return;
        }
        
        if($_GET['simple']) {
            $this->render("article.simple.html");
            return;
        }
        
        $this->render();
    }
    
    /**
     * 设置文章是否显示
     *
     * @author 牧羊人
     * @date 2018-09-08
     */
    function setIsShow() {
        if(IS_POST) {
            $message = $this->service->setIsShow();
            $this->ajaxReturn($message);
            return ;
        }
    }
    
}