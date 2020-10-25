<?php
/**
 * 获得汇报后台可以用的图章
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\utils\Report as ReportUtil;

class GetStamp extends Base 
{
  
    public function run()
    {
        //取出后台的图章
        $stamp = ReportUtil::getEnableStamp();
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $stamp,
        ));
    }
}