<?php
/**
 * author: phplwd
 * createTime: 2017/1/5
 * description:汇报权限接口
 */

namespace application\modules\report\actions\api;


use application\core\utils\Ibos;
use application\modules\role\utils\Role;

class GetAuthority extends Base
{

    public function run()
    {
        $manager = Role::checkRouteAccess('report/api/savetemplate');
        $set = Role::checkRouteAccess('report/api/settemplate');
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => array(
                'manager' => $manager,
                'set' => $set,
            ),
        ));
    }
}