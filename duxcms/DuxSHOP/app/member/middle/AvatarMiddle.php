<?php

/**
 * 头像输出
 */

namespace app\member\middle;


class AvatarMiddle extends \app\base\middle\BaseMiddle {


    protected function avatar() {
        $id = intval($this->params['id']);
        $type = intval($this->params['type']);

        if (empty($id)) {
            $this->stop('头像不存在', 404);
        }

        $avatar = target('member/MemberUser')->getAvatar($id, $type);
        return $this->run([
            'file' => $avatar

        ]);
        header('content-type: image/png');
        echo file_get_contents($avatar);
    }

}