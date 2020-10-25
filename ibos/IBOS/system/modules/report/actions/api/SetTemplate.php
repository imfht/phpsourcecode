<?php
/**
 * 得到模板的设置值，保存模板设置值
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\model\Template;

class SetTemplate extends Base
{

    public function run()
    {
        $data = $this->data;
        $tid = $data['tid'];
        if (isset($data['uid']) || isset($data['uptype']) || isset($data['upuid'])) {
            $uid = $data['uid'];
            $deptid = empty($uid) ? 'alldept' : '';
            $uptype = $data['uptype'];
            $upuid = $data['upuid'];
            Template::model()->setTemplate($tid, $uid, $uptype, $upuid, $deptid);
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => Ibos::lang('Set template success'),
                'data' => '',
            ));
        }else{
            $charge = array(
                1 => '一级主管',
                2 => '二级主管',
                3 => '三级主管',
                4 => '四级主管',
                5 => '五级主管'
            );
            $set = Template::model()->getTemplateSet($tid);
            $upTypeArr = array();
            if (!empty($set['uptype'])){
                $uptypes = explode(',', $set['uptype']);
                foreach ($uptypes as $uptype ){
                    $upTypeArr[] = array('uptype' => $uptype, 'upname' => $charge[$uptype]);
                }
            }
            $set['uptype'] = $upTypeArr;
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => '',
                'data' => $set,
            ));
        }
    }

}