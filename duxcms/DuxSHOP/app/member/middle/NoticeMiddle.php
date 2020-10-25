<?php

/**
 * 消息列表
 */

namespace app\member\middle;


class NoticeMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'member/MemberNotice';

    protected function meta() {
        $this->setMeta('消息提醒');
        $this->setName('消息提醒');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '消息提醒',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $where['A.user_id'] = $userId;
        $pageLimit = 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'notice_id desc');

        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
        ]);
    }

}