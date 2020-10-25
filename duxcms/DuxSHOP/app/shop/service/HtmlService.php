<?php
namespace app\shop\service;
/**
 * Html接口
 */
class HtmlService extends \app\base\service\BaseService {

    private $userInfo = [];


    public function getMemberIndexBodyHtml($userInfo) {
        $this->userInfo = $userInfo;

        $followList = target('shop/ShopFollow')->loadList([
            'user_id' => $userInfo['user_id']
        ], 8);

        $html = \dux\Dux::view()->fetch('app/shop/view/service/member/indexbody', [
            'followList' => $followList
        ]);

        return [
            [
                'name' => '商城模块',
                'order' => 99,
                'html' => $html
            ]
        ];
    }

    public function getMemberIndexSideHtml($userInfo) {
        $shopFootprint = target('shop/ShopFootprint')->loadFootprint($userInfo['user_id'], 5);
        $html = \dux\Dux::view()->fetch('app/shop/view/service/member/indexside', [
            'shopFootprint' => $shopFootprint
        ]);
        return [
            [
                'name' => '商城边栏模块',
                'order' => 99,
                'html' => $html
            ]
        ];
    }



}

