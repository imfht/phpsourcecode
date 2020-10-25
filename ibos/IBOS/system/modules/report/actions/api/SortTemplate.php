<?php
/**
 * 排序模板接口，这里的排序是指排序自己可使用的模板，也就是说是个人本地排序
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\Template;
use application\modules\report\model\TemplateSort;

class SortTemplate extends Base
{

    public function run()
    {
        $data = $this->data;
        $uid = Ibos::app()->user->uid;
        $template = $data['template'];
        TemplateSort::model()->sortTemplate($uid, $template);
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => Ibos::lang('Update sort template'),
            'data' => '',
        ));
    }

}