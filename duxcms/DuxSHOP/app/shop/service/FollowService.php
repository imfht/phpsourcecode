<?php
namespace app\shop\service;
/**
 * 商品收藏
 */
class FollowService extends \app\base\service\BaseService {

    public function addShop($shopId, $userId) {
        $count = target('member/ShopFollow')->countList([
            'user_id' => $userId,
            'shop_id' => $shopId
        ]);
        if($count) {
            $this->error('该商品已收藏!');
        }
        $data = [];
        $data['shop_id'] = $shopId;
        $data['user_id'] = $userId;
        $data['time'] = time();
        if (!target('shop/ShopFollow')->add($data)) {
            return $this->error('商品收藏失败,请稍候再试!');
        }
        return $this->success();
    }
}

