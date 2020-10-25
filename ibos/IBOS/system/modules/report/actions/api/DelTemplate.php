<?php
/**
 * 删除模板接口
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\Template;
use application\modules\report\model\TemplateAdd;

class DelTemplate extends Base
{

    public function run()
    {
        $data = $this->data;
        $tid = $data['tid'];
        Template::model()->deleteTemplate($tid);
        TemplateAdd::model()->delTemplateUser($tid);
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => Ibos::lang('Delete template success'),
            'data' => '',
        ));
    }
}