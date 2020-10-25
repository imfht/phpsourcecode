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
 * 用户分组-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-14
 */
namespace app\admin\controller;
use app\admin\model\UserGroupModel;
use app\admin\service\UserGroupService;
class UserGroupController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-14
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new UserGroupModel();
        $this->service = new UserGroupService();
    }
    
}