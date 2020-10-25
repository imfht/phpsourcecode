<?php

/**
 * 商城列表
 */

namespace app\mall\mobile;

class IndexMobile extends \app\base\mobile\SiteMobile {


    protected $_middle = 'mall/List';

    /**
     * 首页
     */
    public function index() {

        $classId = request('get', 'id', 0, 'intval');
        $pageLimit = request('get', 'limit', 0, 'intval');
        $keyword = request('get', 'keyword', '');
        $pos = request('get', 'pos', 0, 'intval');
        $type = request('get', 'type', 0, 'intval');
        $freeExpress = request('get', 'free_express', 0, 'intval');
        $coupon = request('get', 'coupon', 0, 'intval');
        $minPrice = request('get', 'min_price',0, 'intval');
        $maxPrice = request('get', 'max_price',0, 'intval');

        $urlParams = [
            'id' => $classId,
            'limit' => $pageLimit,
            'keyword' => $keyword,
            'pos' => $pos,
            'type' => $type,
            'free_express' => $freeExpress,
            'coupon' => $coupon,
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ];

        $userId = target('member/MemberUser')->getUid();
        target($this->_middle, 'middle')->setParams([
            'class_id' => $classId,
            'limit' => $pageLimit,
            'keyword' => $keyword,
            'user_id' => $userId,
            'layer' => 'mobile',
            'pos' => $pos,
            'type' => $type,
            'free_express' => $freeExpress,
            'coupon' => $coupon,
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ])->meta()->classInfo()->data()->filter()->export(function ($data) use ($urlParams, $userId) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
	    $this->assign('userId', $userId);
            $this->mobileDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        $classId = request('get', 'id', 0, 'intval');
        $pageLimit = request('get', 'limit', 0, 'intval');
        $keyword = request('get', 'keyword', '');
        $pos = request('get', 'pos', 0, 'intval');
        $type = request('get', 'type', 0, 'intval');
        $freeExpress = request('get', 'free_express', 0, 'intval');
        $coupon = request('get', 'coupon', 0, 'intval');
        $minPrice = request('get', 'min_price',0, 'intval');
        $maxPrice = request('get', 'max_price',0, 'intval');

        target($this->_middle, 'middle')->setParams([
            'class_id' => $classId,
            'limit' => $pageLimit,
            'keyword' => $keyword,
            'pos' => $pos,
            'type' => $type,
            'free_express' => $freeExpress,
            'coupon' => $coupon,
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ])->data()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success([
                    'data' => $data['pageList'],
                    'page' => $data['pageData']['page'],
                ]);
            }else {
                $this->error('暂无数据');
            }

        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}