<?php
/**
 * 为汇报设置图章
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\ReportStats;

class SetStamp extends Base
{

    public function run()
    {
        $data = $this->getData();
        $repid = $data['repid'];
        $stampid = $data['stampid'];
        $uid = Ibos::app()->user->uid;
        ReportStats::model()->scoreReport($repid, $uid, $stampid);
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => Ibos::lang('Add stamp success'),
            'data' => '',
        ));
    }

}