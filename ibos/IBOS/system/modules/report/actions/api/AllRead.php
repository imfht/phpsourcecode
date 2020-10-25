<?php
/**
 * 全部设置为已读
 */

namespace application\modules\report\actions\api;

use application\core\utils\ArrayUtil;
use application\core\utils\Ibos;
use application\modules\report\model\ModuleReader;
use application\modules\report\model\Report;
use application\modules\report\utils\Report as ReportUtil;

class AllRead extends Base
{

    public function run()
    {
        $uid = Ibos::app()->user->uid;
        $data = $this->data;
        if (!empty($data['repids']) && isset($data['repids'])){
            $repids = explode(',', $data['repids']);
        }elseif ($data['repids'] == 0){
            $condition = ReportUtil::getListCondition(self::UNREAD, $uid);
            $repids = Ibos::app()->db->createCommand()
                ->select('repid')
                ->from('{{report}}')
                ->where($condition)
                ->queryColumn();
        }
        $row = ModuleReader::model()->setAllRead($repids, $uid);
        if ($row){
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => Ibos::lang('Read all report success'),
                'data' => '',
            ));
        }
    }
}