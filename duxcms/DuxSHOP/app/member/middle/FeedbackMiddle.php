<?php

/**
 * 会员反馈
 */

namespace app\member\middle;

class FeedbackMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'member/MemberFeedback';

    protected function meta($title = '', $name = '', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '会员反馈',
                'url' => url('member/Feedback/index')
            ],
            [
                'name' => $name,
                'url' => $url
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function post() {
        $model = target($this->_model);
        $userId = intval($this->params['user_id']);
        $content = $this->params['content'];

        $t = time();
        $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
        $count = $model->countList([
            'A.user_id' => $userId,
            '_sql' => 'A.time <' . $end . ' AND A.time > ' . $start
        ]);

        if($count >= 10) {
            return $this->stop('当天反馈数量不能超过10条！');
        }

        $content = html_clear($content);

        if (mb_strlen($content) < 10) {
            return $this->stop('反馈内容不能小于10个字符！');
        }

        if (mb_strlen($content) > 250) {
            return $this->stop('反馈内容不能大于250个字！');
        }

        $status = $model->add([
            'time' => time(),
            'user_id' => $userId,
            'content' => $content
        ]);

        if (!$status) {
            return $this->stop('提交反馈失败');
        }
        return $this->run([], '反馈提交成功!');
    }

}