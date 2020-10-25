<?php

namespace application\modules\message\model;

use application\core\model\Model;
use application\core\utils\Ibos;
use application\core\utils\StringUtil;

class NotifyMessage extends Model
{

    /**
     * @param string $className
     * @return NotifyMessage
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{notify_message}}';
    }

    /**
     * 消息提醒列表页，以模块分类，最新消息在前
     * @param integer $uid 用户ID
     * @param string $order 排序
     * @param integer $limit 每页条数
     * @param integer $offset 页数偏移量
     * @return array
     */
    public function fetchAllNotifyListByUid($uid, $order = 'ctime DESC', $limit = 10, $offset = 0)
    {
        $criteria = array(
            'condition' => 'uid = :uid',
            'params' => array(':uid' => intval($uid)),
            'group' => 'module',
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset
        );
        $return = array();
        $records = $this->findAll($criteria);
        if (!empty($records)) {
            foreach ($records as $record) {
                $msg = $record->attributes;
                $return[$msg['module']] = array();
                $criteria = array(
                    'condition' => 'isread = 0 AND module = :module AND uid = :uid',
                    'params' => array(':module' => $msg['module'], ':uid' => $uid),
                    'order' => 'ctime DESC',
                );
                $new = $this->fetchAll($criteria);
                if (!empty($new)) {
                    $return[$msg['module']]['newlist'] = $new;
                } else {
                    $return[$msg['module']]['latest'] = $this->fetch(
                        array(
                            'condition' => 'module = :module AND uid = :uid',
                            'params' => array(':uid' => $uid, ':module' => $msg['module']),
                            'order' => 'ctime DESC'
                        )
                    );
                }
            }
        }
        return $return;
    }

    /**
     * 根据uid和模块筛选
     * @param $uid
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param $module
     */
    public function fetchAllNotifyListByUidAndModule($uid, $order = 'ctime DESC', $limit = 10, $offset = 0, $module = '', $isread = '0', $search = '')
    {
        $criteria = array(
            'condition' => 'uid = :uid AND isread = :isread',
            'params' => array(':uid' => intval($uid), ':isread' => intval($isread)),
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset
        );

        if(!empty($module)){
            $criteria['condition'] .= ' AND `module` = :module';
            $criteria['params'][':module'] = $module;
        }

        // 拼接条件
        if(!empty($search)) {
            $criteria['condition'] .= ' AND `body` LIKE \'%' .$search. '%\'';
//            $params[':search'] = $search;
        }

        $res = $this->findAll($criteria);
        return $res;
    }

    /**
     * 提醒详情页面，按时间轴排序
     * @param integer $uid 用户ID
     * @param string $module 模块名称
     * @param integer $limit 每页条数
     * @param integer $offset 页数偏移量
     * @return array
     */
    public function fetchAllDetailByTimeLine($uid, $module, $limit = 10, $offset = 0)
    {
        $criteria = array(
            'condition' => 'uid = :uid AND module = :module',
            'params' => array(':uid' => intval($uid), 'module' => $module),
            'order' => 'ctime DESC',
            'limit' => $limit,
            'offset' => $offset
        );
        $return = array();
        $records = $this->findAll($criteria);
        if (!empty($records)) {
            foreach ($records as $record) {
                $msg = $record->attributes;
                $index = date('Yn', $msg['ctime']);
                $return[$index][$msg['id']] = $msg;
            }
        }
        return $return;
    }

    /**
     * 获取指定用户未读消息的总数
     * @param integer $uid 用户ID
     * @return integer 指定用户未读消息的总数
     */
    public function countUnreadByUid($uid)
    {
        return $this->count('`uid` = :uid AND `isread` = :isread', array(':uid' => $uid, ':isread' => 0));
    }

    /**
     * 获取指定用户未读流程结束消息的总数
     *
     * @param $uid
     * @return int
     */
    public function countFlowCompleteByUid($uid)
    {
        $count = $this->count('uid = :uid AND isread = :isread AND node = :node AND url LIKE :url AND `title` NOT LIKE :title1 AND `title` NOT LIKE :title2', array(
            ':uid' => $uid,
            ':isread' => 0,
            ':node' => 'workflow_turn_notice',
            ':url' => '%workflow/preview/print%',
            ':title1' => '%关注了您的工作%',
            ':title2' => '%您关注的工作%',
        ));
        return (int)$count;
    }

    /**
     * 获取指定用户未读被委托记录消息的总数
     *
     * @param $uid
     * @return int
     */
    public function countEntrustRecordByUid($uid)
    {
        $count = $this->count('uid = :uid AND isread = :isread AND node = :node AND title LIKE :title', array(
            ':uid' => $uid,
            ':isread' => 0,
            ':node' => 'workflow_entrust_notice',
            ':title' => '%工作流委托提醒%',
        ));

        return (int)$count;
    }

    /**
     * 更改指定用户的消息从未读为已读
     * @param integer $uid 用户ID
     * @return mixed 更改失败返回false，更改成功返回影响消息ID
     */
    public function setRead($uid)
    {
        return $this->updateAll(array('isread' => 1), 'uid = :uid', array(':uid' => intval($uid)));
    }

    /**
     * 更改指定用户指定模块的消息从未读为已读
     * @param integer $uid 用户ID
     * @param string $module 模块名称
     * @return mixed 更改失败返回false，更改成功返回影响消息ID
     */
    public function setReadByModule($uid, $module)
    {
        return $this->updateAll(array('isread' => 1), "uid = :uid AND FIND_IN_SET(module,:module)", array(':uid' => intval($uid), ':module' => $module));
    }

    /**
     * 根据用户访问的 url 地址修改对应的消息为已读
     * @param integer $uid 用户 ID
     * @param string $url 用户访问的 URL
     * @return mixed 更改失败返回false，更改成功返回影响消息ID
     */
    public function setReadByUrl($uid, $url)
    {
        return $this->updateAll(array('isread' => 1), "uid = :uid AND FIND_IN_SET(url, :url)", array(':uid' => intval($uid), ':url' => $url));
    }

    /**
     * 根据提交的id设置已读
     * @param $uid
     * @param $idx
     */
    public function setReadByIdx($uid, $idx)
    {
        $idx = is_array($idx) ? implode(',', $idx) : $idx;
        return $this->updateAll(array('isread' => 1), "uid = :uid AND FIND_IN_SET(id, :idx)", array(':uid' => intval($uid), ':idx' => $idx));
    }

    /**
     * 发送一条消息提醒
     * @param array $data 发送消息提醒所需数组
     * @return boolean
     */
    public function sendMessage($data)
    {
        if (empty($data['uid'])) {
            return false;
        }
        $s['uid'] = intval($data['uid']);
        $s['node'] = StringUtil::filterCleanHtml($data['node']);
        $s['module'] = StringUtil::filterCleanHtml($data['module']);
        $s['isread'] = 0;
        $s['title'] = StringUtil::filterCleanHtml($data['title']);
        $s['body'] = StringUtil::filterDangerTag($data['body']);
        $s['ctime'] = time();
        $s['url'] = $data['url'];
        if(!empty($data['isalarm'])){
            // 区分是否为主动提醒
            $s['isalarm'] = $data['isalarm'];
        }
        if(!empty($data['senduid'])){
            // 主动提醒发送人
            $s['senduid'] = $data['senduid'];
        }
        return $s;
    }

    /**
     * 根据ID或模块删除通知
     * @param mixed $id 通知ID或模块
     * @return mixed 删除失败返回false，删除成功返回删除的条数
     */
    public function deleteNotify($id, $type = 'id')
    {
        $uid = Ibos::app()->user->uid;
        if ($type == 'id') {
            return $this->deleteAll('uid = :uid AND FIND_IN_SET(id,:id)', array(':uid' => $uid, ':id' => $id));
        } else if ($type == 'module') {
            return $this->deleteAll('uid = :uid AND FIND_IN_SET(module,:module)', array(':uid' => $uid, ':module' => $id));
        }
    }

    /**
     * 根据uid查找有多少个模块有消息，用于分页
     * @param integer $uid 用户uid
     * @return integer 符合条件的条数，注：是根据模块分组
     */
    public function fetchPageCountByUid($uid)
    {
        $pageCount = $this->count(array(
            'select' => 'id',
            'condition' => 'uid=:uid',
            'params' => array(':uid' => $uid),
            'group' => 'module'
        ));
        return $pageCount;
    }

    /**
     * 根据uid查找有多少个模块有消息，用于分页
     * @param integer $uid 用户uid
     * @return integer 符合条件的条数，注：是根据模块分组
     */
    public function fetchPageCountByUidAndModuleAndIsreadAndSearch($uid, $module, $isread, $search)
    {
        $criteria = array(
            'select' => 'id',
            'condition' => 'uid = :uid AND isread = :isread',
            'params' => array(':uid' => intval($uid), ':isread' => intval($isread)),
        );

        if(!empty($module)){
            $criteria['condition'] .= ' AND `module` = :module';
            $criteria['params'][':module'] = $module;
        }

        // 拼接条件
        if(!empty($search)) {
            $criteria['condition'] .= ' AND `body` LIKE \'%' .$search. '%\'';
//            $params[':search'] = $search;
        }

        $pageCount = $this->count($criteria);
        return $pageCount;
    }

    /*
     * 获取指定用户的提醒通知统计
     * @param integer $uid 用户ID
     * @return array 指定用户的提醒通知统计
     */
    public function getNotifyCountByUid($uid)
    {
        return Ibos::app()->db->createCommand()
            ->select('module,count(`id`)')
            ->from($this->tableName())
            ->where("`uid` = :uid and `isread` = 0", array(':uid' => $uid))
            ->group('module')
            ->queryAll();
    }
}
