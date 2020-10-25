<?php
/**
 * 获得已读用户接口
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\ModuleReader;
use application\modules\department\utils\Department as DepartmentUtil;
use application\modules\user\model\User;

class GetReader extends Base
{

    public function run()
    {
        $data = $this->data;
        $repid = $data['repid'];
        $readerData = ModuleReader::model()->getReader($repid);
        $departments = DepartmentUtil::loadDepartment();
        $res = $tempDeptids = $users = array();
        foreach ($readerData as $reader) {
            $user = User::model()->fetchByUid($reader);
            $users[] = $user;
            $deptid = $user['deptid'];
            $tempDeptids[] = $user['deptid'];
        }
        $deptids = array_unique($tempDeptids);
        foreach ($deptids as $deptid) {
            $deptName = isset($departments[$deptid]['deptname']) ? $departments[$deptid]['deptname'] : '--';
            foreach ($users as $k => $user) {
                if ($user['deptid'] == $deptid) {
                    $res[$deptName][] = $user;
                    unset($users[$k]);
                }
            }
        }
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $res,
        ));
    }
}