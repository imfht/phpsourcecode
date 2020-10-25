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
 * 部门-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Controller;
use Admin\Model\AdminDepModel;
use Admin\Service\AdminDepService;
class AdminDepController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new AdminDepModel();
        $this->service = new AdminDepService();

    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::edit()
     */
    function edit() {
        $pid = I("get.pid",0);
        if($pid) {
            $pInfo = $this->mod->getInfo($pid);
        }
        parent::edit([
            'parent_id'=>$pid,
            'parent_name'=>$pInfo['name'],
        ]);
    }
    
}