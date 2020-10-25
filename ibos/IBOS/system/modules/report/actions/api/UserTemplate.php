<?php
/**
 * 用户可用模板
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\Template;
use application\modules\report\utils\Template as TemplateUtil;
use application\modules\role\utils\Role;
use application\modules\user\model\User;

class UserTemplate extends Base
{
    public function run()
    {
        $data = $this->getData();
        $uid = Ibos::app()->user->uid;
        $condition = TemplateUtil::getTemplateCondition($uid);
        $apiType = isset($data['apiType']) ? $data['apiType'] : '';
        $template = Template::model()->getTemplateForUser($condition, $apiType);
        $isSet = Role::checkRouteAccess('report/api/settemplate');
        $isUse = Role::checkRouteAccess('report/default/index');
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $template,
            'isSet' => $isSet,
            'isUse' => $isUse
        ));
    }
}