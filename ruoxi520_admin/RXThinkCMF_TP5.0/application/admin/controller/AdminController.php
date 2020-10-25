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
 * 管理人员-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-10
 */
namespace app\admin\controller;
use app\admin\model\AdminModel;
use app\admin\service\AdminService;
use think\Db;
class AdminController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    public function __construct() 
    {
        parent::__construct();
        $this->model = new AdminModel();
        $this->service = new AdminService();
    }
    
    /**
     * 设置人员角色
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function setRole()
    {
        if(IS_POST) {
            $message = $this->service->setRole();
            return $message;
        }
        $adminId = input("get.admin_id",0);
        $this->assign('admin_id',$adminId);
        
        //获取人员角色ID集合
        $roleIds = Db::name("adminRmr")->where(['admin_id'=>$adminId,'mark'=>1])->getField('role_id',true);
        
        //获取全部角色
        $list = Db::name("adminRole")->where(['status'=>1,'mark'=>1])->select();
        if($list) {
            foreach ($list as &$val) {
                if(in_array($val['id'], $roleIds)) {
                    $val['selected'] = 1;
                }
            }
        }
        $this->assign('list',$list);
        
        return $this->render();
    }
    
    /**
     * 重置人员密码
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function resetPwd()
    {
        if(IS_POST) {
            $message = $this->service->resetPwd();
            return $message;
        }
        $id = input('get.id',0);
        $this->assign('id',$id);
        return $this->render();
    }
    
}