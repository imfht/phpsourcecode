<?php

/**
 * 商品列表
 */

namespace app\mall\middle;

class ListMiddle extends \app\base\middle\BaseMiddle {

    protected $crumb = [];
    protected $classInfo = [];
    protected $listWhere = [];
    protected $listOrder = [];
    protected $listLimit = 20;
    protected $listModel = 0;
    private $tpl = '';

    private function getClass() {
        if ($this->classInfo) {
            return $this->classInfo;
        }
        $classId = $this->params['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->classInfo = target('mall/MallClass')->getInfo($classId);

        return $this->classInfo;
    }

    private function getCrumb() {
        if ($this->crumb) {
            return $this->crumb;
        }
        $classId = $this->params['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->crumb = target('mall/MallClass')->loadCrumbList($classId);

        return $this->crumb;
    }

    protected function classInfo() {
        $this->classInfo = $this->getClass();
        if (empty($this->classInfo)) {
            return $this->run([
                'classInfo' => $this->classInfo,
                'parentClassInfo' => [],
                'topClassInfo' => [],
            ]);
        }
        if ($this->classInfo['url']) {
            $this->data['url'] = $this->classInfo['url'];

            return $this->stop('跳转', 302, $this->classInfo['url']);
        }
        $this->crumb = $this->getCrumb();
        $parentClassInfo = array_slice($this->crumb, -2, 1);
        if (empty($parentClassInfo)) {
            $parentClassInfo = $this->crumb[0];
        } else {
            $parentClassInfo = $parentClassInfo[0];
        }
        $topClassInfo = $this->crumb[0];

        if ($this->classInfo['tpl_class']) {
            $this->tpl = $this->classInfo['tpl_class'];
        }

        return $this->run([
            'classInfo' => $this->classInfo,
            'parentClassInfo' => $parentClassInfo,
            'topClassInfo' => $topClassInfo,
            'tpl' => $this->tpl
        ]);
    }


    protected function meta($title = '全部商品', $name = '全部商品', $url = '') {
        $classId = $this->params['class_id'];
        $coupon = $this->params['coupon'];
        if ($classId) {
            $this->crumb = $this->getCrumb();
            $this->classInfo = $this->getClass();
            $this->setMeta($this->classInfo['name'], $this->classInfo['keyword'], $this->classInfo['description']);
            $this->setCrumb($this->crumb);
        }elseif($coupon) {
            $this->setName('优惠券商品');
            $this->setMeta('优惠券商品');
            $this->setCrumb([
                [
                    'name' => '优惠券商品',
                    'url' => URL
                ]
            ]);
        } else {
            $this->setName($name ? $name : '全部商品');
            $this->setMeta($title ? $title : '全部商品');
            $this->setCrumb([
                [
                    'name' => $name,
                    'url' => $url ? $url : url()
                ]
            ]);
        }

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private $filter = [];

    protected function data($filterStatus = true, $urlParams = []) {
        $classId = $this->params['class_id'];
        $pos = $this->params['pos'];

        $keyword = str_len(html_clear(urldecode($this->params['keyword'])), 10, false);
        $listLimit = $this->params['limit'] ? $this->params['limit'] : 20;
        $modelId = $this->params['model_id'] ? $this->params['model_id'] : 0;
        $coupon = $this->params['coupon'];
        $filterWhere = [];
        if ($classId) {
            $this->classInfo = $this->getClass();
            $modelId = $this->classInfo['model_id'];
            $classIds = target('mall/MallClass')->getSubClassId($classId);
            if ($classIds) {
                $filterWhere['_sql'][] = 'B.class_id in(' . $classIds . ')';
            }
        }
        if($coupon) {
            $couponInfo = target('order/OrderCoupon')->getInfo($coupon);
            if($couponInfo['has_id']) {
                $filterWhere['_sql'][] = 'B.mall_id in ('.$couponInfo['has_id'].')';
            }
        }
        $filterWhere['_sql'][] = '(B.up_time <= ' . time() . ' OR B.up_time = 0) AND (B.down_time >= ' . time() . ' OR B.down_time = 0)';
        if ($keyword) {
            $filterWhere['_sql'][] = 'A.title like "%' . $keyword . '%"';
            target('site/SiteSearch')->stats($keyword, APP_NAME);
        }
        if($pos) {
            $filterWhere['_sql'][] = 'FIND_IN_SET('.$pos.', A.pos_id)';
        }

        $data = [];
        if ($filterStatus) {
            $service = target('shop/Filter', 'service');
            $data = $service->getData('mall', $filterWhere, array_merge($urlParams, [
                'id' => $classId,
                'pos' => $pos,
                'keyword' => $keyword,
            ]));
            $this->filter = $data['filter'];
            $listWhere = $data['where'];
        } else {
            $filterWhere['A.status'] = 1;
            $listWhere = $filterWhere;
        }

        if ($this->params['where']) {
            $listWhere = array_merge($listWhere, $this->params['where']);
        }
        $listOrder = $this->params['order'] ? $this->params['order'] : $data['order'];
        if ($listWhere) {
            $model = target('mall/Mall');
            $count = $model->countList($listWhere);
            $pageData = $this->pageData($count, $listLimit);
            $list = $model->loadList($listWhere, $pageData['limit'], $listOrder, $modelId);
        } else {
            $pageData = [];
            $list = [];
            $count = 0;
        }
        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list
        ]);
    }

    protected function filter() {
        $service = target('shop/Filter', 'service');
        $priceList = $service->getPriceData($this->filter['ids']);
        $brandList = $service->getBrandData($this->filter['ids']);

        return $this->run([
            'pageParams' => $this->filter['urlParam'],
            'attrList' => $this->filter['attrList'],
            'priceList' => $priceList,
            'brandList' => $brandList,
            'orderList' => $service->getOrderData(),
        ]);
    }
}