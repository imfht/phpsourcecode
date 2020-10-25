<?php

/**
 * 评论详情
 */

namespace app\order\middle;

class CommentMiddle extends \app\base\middle\BaseMiddle {



    protected function meta() {
        $id = intval($this->params['id']);

        $this->setMeta('商品评价');
        $this->setName('商品评价');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '商品评价',
                'url' => url('', ['id' => $id])
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function info() {
        $userId = intval($this->params['user_id']);
        $id = intval($this->params['id']);
        $info = target('order/OrderGoods')->getWhereInfo([
            'id' => $id
        ]);
        if (empty($info)) {
            return $this->stop('订单商品不存在！', 404);
        }
        $target = target('order/Order');
        $orderInfo = $target->getInfo($info['order_id']);
        if ($orderInfo['order_user_id'] <> $userId) {
            return $this->stop('该订单无法操作!');
        }
        if ($orderInfo['status_data']['action'] <> 'comment') {
            return $this->stop('暂时无法评价该订单!');
        }
        return $this->run([
            'id' => $id,
            'info' => $info,
            'orderInfo' => $orderInfo
        ]);
    }

    protected function post() {
        $userId = intval($this->params['user_id']);
        $info = $this->data['info'];
        if (empty($info)) {
            return $this->stop('订单商品不存在！', 404);
        }
        $this->params['level'] = intval($this->params['level']);
        $this->params['store'] = intval($this->params['store']);
        $level = $this->params['level'] ? $this->params['level'] : 5;
        $store = $this->params['store'] ? $this->params['store'] : 5;
        $content = str_len(html_clear($this->params['content']), 300);
        if (empty($content)) {
            return $this->stop('请填写评价内容!');
        }
        $images = $this->params['images'];
        if (!empty($images) && is_array($images)) {
            $httpHost = DOMAIN_HTTP;
            foreach ($images as $image) {
                if (strpos($image, $httpHost, 0) === false) {
                    return $this->stop('您上传的图片有误,请重新上传!');
                }
            }
        } else {
            $images = [];
        }
        $images = $images ? $images : [];
        $spec = [];
        if (!empty($info['options'])) {
            foreach ($info['options'] as $vo) {
                $spec[] = $vo['value'];
            }
        }
        $orderInfo = $this->data['orderInfo'];
        $id = $this->data['id'];
        target('order/OrderComment')->beginTransaction();
        $status = target('order/OrderComment')->add([
            'order_goods_id' => $info['id'],
            'user_id' => $userId,
            'app' => $orderInfo['order_app'],
            'has_id' => $info['has_id'],
            'spec' => implode(' ', $spec),
            'time' => time(),
            'content' => $content,
            'level' => $level,
            'images' => serialize($images)
        ]);
        if (!$status) {
            target('order/OrderComment')->rollBack();

            return $this->stop('商品评价失败,请稍后再试!');
        }
        $status = target('order/OrderGoods')->edit([
            'id' => $id,
            'comment_status' => 1
        ]);
        if (!$status) {
            target('order/OrderComment')->rollBack();

            return $this->stop('商品评价失败,请稍后再试!');
        }
        $count = target('order/OrderGoods')->countList([
            'order_id' => $orderInfo['order_id'],
            '_sql' => 'id <> ' . $id,
            'comment_status' => 0
        ]);
        if (!$count) {
            $status = target('order/Order')->edit([
                'order_id' => $orderInfo['order_id'],
                'comment_status' => 1
            ]);
            if (!$status) {
                target('order/OrderComment')->rollBack();

                return $this->stop('商品评价失败,请稍后再试!');
            }
        }
        $model = $orderInfo['order_app'] . '/' . $orderInfo['order_app'];
        $status = target($model)->where([target($model)->getPrimary() => $info['has_id']])->setInc('comments', 1);
        if (!$status) {
            target('order/OrderComment')->rollBack();

            return $this->stop('商品评价失败,请稍后再试!');
        }
        $status = target($model)->where([target($model)->getPrimary() => $info['has_id']])->setInc('score', $level);
        if (!$status) {
            target('order/OrderComment')->rollBack();

            return $this->stop('商品评价失败,请稍后再试!');
        }

        //评价接口
        $hookList = run('service', 'Order', 'hookCommentOrder', [$orderInfo, $this->params]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                target('order/OrderComment')->rollBack();
                return $this->stop(target($app . '/Order', 'service')->getError());
            }
        }

        target('order/OrderComment')->commit();

        return $this->run([], '商品评价成功!', url('order/order/index'));
    }


}