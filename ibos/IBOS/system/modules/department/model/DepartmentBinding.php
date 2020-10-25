<?php

namespace application\modules\department\model;

use application\core\model\Model;
use application\core\utils\Ibos;

/**
 * department_binding模型类
 *
 * @namespace application\modules\department\model
 * @filename DepartmentBinding.php
 * @encoding UTF-8
 * @author forsona <2317216477@qq.com>
 * @link https://github.com/forsona
 * @copyright Copyright &copy; 2012-2015 IBOS Inc
 * @datetime 2016-6-16 15:40:17
 * @version $Id$
 */
class DepartmentBinding extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{department_binding}}';
    }

    /**
     * 获取所有的uid
     * @param str $type 绑定的第三方应用名称
     * @return array
     */
    public function fetchAllBindvalue($type)
    {
        $return = Ibos::app()->db->createCommand()
            ->select('deptid,bindvalue')
            ->from($this->tableName())
            ->where("app = '{$type}'")
            ->queryAll();
        return $return;
    }

    public function getBindingValueForDeptidAndApp($deptid, $app='wxqy')
    {
        $bindvalue = Ibos::app()->db->createCommand()
            ->select('bindvalue')
            ->from($this->tableName())
            ->where('deptid = :deptid AND app = :app', array(':deptid' => $deptid, ':app' => $app))
            ->queryScalar();
        return $bindvalue;
    }

    /**
     * @param $bindvalue
     * @param string $app
     * @return bool|\CDbDataReader|mixed|string
     */
    public function getDeptidByBindingValueAndApp($bindvalue, $app = 'wxqy')
    {
        $bindvalue = Ibos::app()->db->createCommand()
            ->select('deptid')
            ->from($this->tableName())
            ->where('bindvalue = :bindvalue AND `app` = :app', array(':bindvalue' => $bindvalue, ':app' => $app))
            ->queryScalar();
        return $bindvalue;
    }
}
