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
 * @date 2018-12-12
 */
namespace app\admin\controller;
use app\admin\model\AdminDepModel;
use app\admin\service\AdminDepService;
class AdminDepController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new AdminDepModel();
        $this->service = new AdminDepService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-12-12
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::edit()
     */
    function edit()
    {
        $pid = input("get.pid",0);
        if($pid) {
            $info = $this->mod->getInfo($pid);
        }
        return parent::edit([
            'parent_id'=>$pid,
            'parent_name'=>$info['name'],
        ]);
    }
    
}