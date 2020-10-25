<?php
/**
 * 评论列表接口
 */

namespace application\modules\report\actions\api;

use application\core\utils\ArrayUtil;
use application\core\utils\Convert;
use application\core\utils\Ibos;
use application\core\utils\StringUtil;

class GetCommentList extends Base
{

    static $commentIds = array();

    public function run()
    {
        $data = $this->data;
        $rowid = $data['rowid'];
        $limit = empty($data['limit']) ? 10 : $data['limit'];
        $offset = empty($data['offset']) ? 0 : $data['offset'];

        $cids = Ibos::app()->db->createCommand()
            ->select('cid')
            ->from('{{comment}}')
            ->where("`module` = :module AND `table` = :table AND `rowid` = :rowid AND `isdel` = :isdel", array(
                ':module' => Ibos::getCurrentModuleName(),
                ':table' => Ibos::getCurrentModuleName(),
                ':rowid' => $rowid,
                ':isdel' => 0
            ))->queryAll();
        $cids = ArrayUtil::getColumn($cids, 'cid');
        $result = $this->getReply($cids);
        $allCids = array_merge($cids, $result);
        $strCids = "\"". implode('","', $allCids). "\"";

        $query = Ibos::app()->db->createCommand()
            ->from('{{comment}}')
            ->where("`isdel` = 0 AND`cid` IN ({$strCids})");
        $queryClone = clone $query;
        $lists = $query->select('*')
            ->limit($limit)
            ->offset($offset)
            ->order('ctime DESC')
            ->queryAll();
        $count = $queryClone->select('count(*)')->queryScalar();
        $commentOrReply = array();
        foreach ($lists as $list){
            $list['content'] = StringUtil::parseHtml($list['content']);
            $list['content'] = StringUtil::purify($list['content']);
            $list['ctime'] = Convert::formatDate($list['ctime'], 'u');
            $list['ctime'] = str_replace('&nbsp;', '', $list['ctime']);
            $commentOrReply[] = $list;
        }
        $listCount = count($lists);
        //是否还有更多数据
        if ($count == 0 || $listCount + $offset >= $count || $count <= $limit){
            $hasMore =  false;
        }else{
            $hasMore =  true;
        }

        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => array(
                'lists' => $commentOrReply,
                'count' => $count,
                'hasMore' => $hasMore,
            )
        ));
    }

    private function getReply($cids)
    {
        static $allCids = array();
        foreach ($cids as $cid){
            $replyCids = Ibos::app()->db->createCommand()
                ->select('cid')
                ->from('{{comment}}')
                ->where("`module` = :module AND `table` = :table AND `rowid` = :rowid AND `isdel` = :isdel", array(
                    ':module' => 'message',
                    ':table' => 'comment',
                    ':rowid' => $cid,
                    ':isdel' => 0,
                ))->queryAll();
            if (!empty($replyCids)) {
                $replyCids = ArrayUtil::getColumn($replyCids, 'cid');
                $allCids = array_merge($replyCids, $allCids);
                $this->getReply($replyCids);
            }
        }
        return $allCids;
    }

}