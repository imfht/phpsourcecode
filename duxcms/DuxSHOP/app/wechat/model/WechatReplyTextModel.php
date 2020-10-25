<?php

/**
 * 微信回复
 */
namespace app\wechat\model;

use app\system\model\SystemModel;

class WechatReplyTextModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'reply_id',
        'into' => '',
        'out' => '',
    ];

    protected function base($where) {
        return $this->table('wechat_reply_text(A)')
            ->join('wechat_reply(B)', ['B.reply_id', 'A.reply_id'])
            ->field(['A.*', 'B.*'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = '') {
        return $list = $this->base($where)
            ->limit($limit)
            ->order('B.priority desc, B.reply_id desc')
            ->select();
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        return $this->base($where)->find();
    }

    public function getInfo($id) {
        $where = [];
        $where['B.reply_id'] = $id;
        return $this->getWhereInfo($where);
    }


    public function saveData($type = 'add', $data = []) {
        $this->beginTransaction();
        if ($type == 'add') {
            $id = target('wechat/WechatReply')->saveData('add');
            if (!$id) {
                $this->rollBack();
                $this->error = target('wechat/WechatReply')->getError();
                return false;
            }
            $_POST['reply_id'] = $id;
            $id = parent::saveData('add');
            if (!$id) {
                $this->rollBack();
                $this->error = $this->getError();
                return false;
            }
        }
        if ($type == 'edit') {
            $status = target('wechat/WechatReply')->saveData('edit');
            if (!$status) {
                $this->rollBack();
                $this->error = target('wechat/WechatReply')->getError();
                return false;
            }
            $status = parent::saveData('edit');
            if (!$status) {
                $this->rollBack();
                $this->error = $this->getError();
                return false;
            }
        }
        $this->commit();
        return true;
    }


    public function _saveBefore($data) {
        $xss = new \dux\vendor\HtmlCleaner(['a'], ['href'], 0, 0);
        $data['content'] = $xss->remove($data['content']);
        return $data;
    }

    public function getReply($replyId) {
        $info = $this->getInfo($replyId);
        return [
            'type' => 'text',
            'content' => html_out($info['content'])
        ];
    }

    public function delData($id) {
        $this->beginTransaction();
        $where = array();
        $where['reply_id'] = $id;
        if (!$this->where($where)->delete()) {
            $this->rollBack();
            return false;
        }
        if (!target('wechat/WechatReply')->delData($id)) {
            $this->rollBack();
            return false;
        }
        $this->commit();
        return true;
    }


}