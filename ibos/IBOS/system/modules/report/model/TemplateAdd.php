<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/30
 * Time: 16:26
 */

namespace application\modules\report\model;


use application\core\model\Model;
use application\core\utils\Ibos;

class TemplateAdd extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{template_add}}';
    }

    /**
     * 判断用户是否已经添加过系统模板
     * @param integer $uid 用户id
     * @param integer $tid 模板id
     * @return boolean
     */
    public function isAddTemplate($tid)
    {
        $isAddTemplate  = $this->find(' shoptid = :shoptid',array(
            ':shoptid' => $tid
        ));
        if (!empty($isAddTemplate)) {
            return true;
        }
        return false;
    }

    /**
     * 用户添加汇报模板
     * @param integer $tid 添加模板ID
     * @param integer $shopTid 商城模板id
     * @param integer $uid 用户ID
     * @return boolean
     */
    public function addTemplateUser($tid, $shopTid, $uid)
    {
        $insertRow = Ibos::app()->db->createCommand()->insert($this->tableName(), array(
            'shoptid' => $shopTid,
            'tid' => $tid,
            'uid' => $uid,
            'addtime' => TIMESTAMP,
        ));
        if ($insertRow > 0) {
            return true;
        }
        return false;
    }

    /**
     * 用户删除汇报模板
     * @param integer $tid 模板ID
     * @param integer $uid 用户ID
     * @return boolean
     */
    public function delTemplateUser($tid)
    {
        $row = $this->deleteAll("tid = :tid", array(
            ':tid' => $tid,
        ));
        if ($row > 0){
            return true;
        }
        return false;
    }
}