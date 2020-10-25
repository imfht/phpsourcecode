<?php

namespace application\modules\report\model;


use application\core\model\Model;
use application\core\utils\ArrayUtil;
use application\core\utils\Ibos;

class TemplateSort extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{template_sort}}';
    }

    /**
     * 用户uid返回排序好的模板,如果不为空返回t排序好的tid，否则返回空
     * @param integer $uid 当前用户
     * @return  array
     */
    public function getSortTemplateByUid($uid)
    {
        $template = Ibos::app()->db->createCommand()
            ->select('*')
            ->from($this->tableName())
            ->where('uid = :uid', array(
                ':uid' => $uid,
            ))
            ->order('sort ASC')
            ->queryAll();
        if (!empty($template)){
            $tids = ArrayUtil::getColumn($template, 'tid');
            return $tids;
        }else{
            return '';
        }
    }

    /**
     * 排序模板，思路是如果当前表没有该用户的模板，则是插入，如果有，则是更新
     * @param integer $uid 用户uid
     * @param array $sortTempl 模板排序
     */
    public function sortTemplate($uid, $sortTempl)
    {
        $userTemplate = $this->getSortTemplateByUid($uid);
        if (empty($userTemplate)){
            $sort = array();
            foreach ($sortTempl as $templ){
                $sort[] = array('uid' => $uid, 'tid' => $templ['tid'], 'sort' => $templ['sort']);
            }
            Ibos::app()->db->schema->commandBuilder
                ->createMultipleInsertCommand($this->tableName(), $sort)
                ->execute();
        }else{
            foreach ($sortTempl as $templ) {
                $this->updateAll(array('sort' => $templ['sort']), 'tid = :tid AND uid = :uid', array(
                    ':tid' => $templ['tid'],
                    ':uid' => $uid,
                ));
            }
        }
    }

    /**
     * 更新旧的模板id为新的模板id
     * @param integer $oldTid 旧模板id
     * @param integer $newTid 新模板id
     */
    public function updateTid($oldTid, $newTid)
    {
        $oldeTemplate = $this->fetchAll('tid = :tid', array(':tid' => $oldTid));
        if (!empty($oldeTemplate)){
            $this->updateAll(array('tid' => $newTid), 'tid = :tid', array(':tid' => $oldTid));
        }
    }
}