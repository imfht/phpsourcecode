<?php

/**
 * 账户信息
 */

namespace app\sale\middle;

class UserMiddle extends \app\base\middle\BaseMiddle {


    private $_model = 'sale/SaleUser';

    protected function meta() {
        $this->setMeta('用户信息');
        $this->setName('用户信息');
        $this->setCrumb([
            [
                'name' => '用户信息',
                'url' => url()
            ],
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }
    protected function data() {

        $userId = intval($this->params['id']);

        $info = target($this->_model)->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if(empty($info)) {
            return $this->stop('用户不存在!', 404);
        }

        return $this->run([
            'info' => $info,
        ]);
    }

}