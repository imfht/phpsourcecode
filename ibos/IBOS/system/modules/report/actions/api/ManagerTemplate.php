<?php
/**
 * 用户可以管理的模板列表接口
 */

namespace application\modules\report\actions\api;


use application\core\utils\Ibos;
use application\modules\report\model\Template;

class ManagerTemplate extends Base
{

    public function run()
    {
        $data = $this->getData();
        $apiType = isset($data['apiType']) ? $data['apiType'] : '';
        $template = Template::model ()->getTemplateForManager($apiType);
        Ibos::app()->controller->ajaxReturn(array(
           'isSuccess' => true,
            'msg' => '',
            'data' => $template,
        ));
    }
}