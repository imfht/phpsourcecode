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
 * 通知公告-控制器
 *
 * @author lys
 * @date 2018-12-04
 */
namespace Admin\Controller;
use Admin\Model\SystemNoticeModel;
use Admin\Service\SystemNoticeService;
class SystemNoticeController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new SystemNoticeModel();
        $this->service = new SystemNoticeService();
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

}