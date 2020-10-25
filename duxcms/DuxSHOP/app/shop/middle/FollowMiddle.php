<?php

/**
 * 评论详情
 */

namespace app\shop\middle;

class FollowMiddle extends \app\base\middle\BaseMiddle {

    protected function meta($title = '商品收藏', $name = '商品收藏') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => $name,
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $this->params['limit'] = intval($this->params['limit']);
        $where = [];
        $where['user_id'] = $userId;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target('shop/ShopFollow');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'follow_id desc');
        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }


}