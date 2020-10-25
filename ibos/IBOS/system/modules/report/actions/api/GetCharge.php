<?php
/**
 * 得到主管和当前用户对应的个主管的uid
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;

class GetCharge extends Base
{

    //各级主管的uid
    static $charge = array();

    public function run()
    {
        $firstCharge = Ibos::app()->db->createCommand()
            ->select('upuid')
            ->from('{{user}}')
            ->where('status = 0 AND upuid != 0')
            ->queryColumn();
        self::$charge[1] = $firstCharge;
         $allAharge = array(
            array('uptype' => 1, 'upname'=> '一级主管', 'manager' => $this->getUpuid(1)),
            array('uptype' => 2, 'upname'=> '二级主管', 'manager' => $this->getUpuid(2)),
            array('uptype' => 3, 'upname'=> '三级主管', 'manager' => $this->getUpuid(3)),
            array('uptype' => 4, 'upname'=> '四级主管', 'manager' => $this->getUpuid(4)),
            array('uptype' => 5, 'upname'=> '五级主管', 'manager' => $this->getUpuid(5)),
         );
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $allAharge,
        ));
    }

    /**
     * @param  integer $level 主管类型，一级主管则是1，以此类推
     * @return array
     */
    protected function getUpuid($level)
    {
        if (isset(self::$charge[$level])){
            return (array)self::$charge[$level];
        }else{
            $formLevel = $level - 1;
            if (isset(self::$charge[$formLevel])){
                $formUpuidStr = '"' . implode('","', self::$charge[$formLevel]) . '"';
                $upuid = Ibos::app()->db->createCommand()
                    ->select('upuid')
                    ->from('{{user}}')
                    ->where("status = 0 AND upuid != 0 AND uid IN ({$formUpuidStr})")
                    ->queryColumn();
                self::$charge[$level] = $upuid;
                return $upuid;
            }else{
                return array();
            }
        }
    }
}