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
 * 系统日志-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Controller;
use Admin\Model\SystemLogModel;
use Admin\Service\SystemLogService;
class SystemLogController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new SystemLogModel();
        $this->service = new SystemLogService();
        
    }
    
    /**
     * 查看系统日志详情
     * 
     * @author 牧羊人
     * @date 2018-07-26
     */
    function detail() {
        $id = I("get.id",0);
        if($id) {
            $info = $this->mod->getInfo($id);
            $this->assign('info',$info);
        }
        $this->render();
    }
    
}