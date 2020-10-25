<?php
/**
 * 删除汇报，repid为0表示全部删除
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\Report;

class DelReport extends Base
{

    public function run()
    {
        $data = $this->data;
        $repids = $data['repids'];
        Report::model()->DelReport($repids);
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => Ibos::lang('Delete report success'),
            'data' => '',
        ));
    }

}