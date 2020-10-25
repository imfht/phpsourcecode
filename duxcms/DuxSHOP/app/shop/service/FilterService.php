<?php 

namespace app\shop\service;
/**
 * 筛选服务
 */
class FilterService {

    private $urlParam = [];
    private $order = '';
    private $brand = 0;
    private $price = [];
    private $model = '';
    private $table = '';

    public function setUrlParam($key, $val) {
        $this->urlParam[$key] = $val;
    }

    public function getData($app, $filterWhere = [], $urlParams = []) {
        $this->model = $app . '/' . $app;
        $where = [];
        $contentData = target($this->model)->table('site_content(A)')->join($app . '(B)', ['B.content_id', 'A.content_id'])->field(['A.content_id'])->where($filterWhere)->limit(0)->select();
        if (!isset($_GET['price'])) {
            $minPrice = request('get', 'min_price');
            $maxPrice = request('get', 'max_price');
            if (isset($_GET['min_price']) && isset($_GET['max_price'])){
                $_GET['price'] = $minPrice . '_' . $maxPrice;
            }
        }
        $price = request('get', 'price');

        $priceAttr = $this->getUrlPrice();
        $minPrice = $priceAttr[0];
        $maxPrice = $priceAttr[1];
        $this->urlParam['min_price'] = $minPrice;
        $this->urlParam['max_price'] = $maxPrice;

        $filter = target('site/SiteFilter')->getFilter($contentData, array_merge((array)$urlParams, ['brand' => request('get', 'brand'), 'price' => $price, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'order' => request('get', 'order')]));

        $this->urlParam = $filter['urlParam'];

        if ($filter['ids']) {
            $where['A.status'] = 1;
            $contentSql = 'A.content_id in (' . $filter['ids'] . ')';
            $where['_sql'][] = $contentSql;
            $brandId = $this->getUrlBrand();
            if ($brandId) {
                $where['B.brand_id'] = $brandId;
            }
            if ($minPrice) {
                $where['_sql'][] = 'B.sell_price >=' . $minPrice;
            }
            if ($maxPrice) {
                $where['_sql'][] = 'B.sell_price <=' . $maxPrice;
            }
        }

        //排序条件
        switch ($this->getUrlOrder()) {
            case 'sale' :
                $order = 'B.sale desc';
                break;
            case 'sale_toggle':
                $order = 'B.sale asc';
                break;
            case 'price' :
                $order = 'B.sell_price desc';
                break;
            case 'price_toggle':
                $order = 'B.sell_price asc';
                break;
            case 'new' :
                $order = 'A.create_time desc';
                break;
			case 'rebate' :
				$order = 'B.rebate_money_rate desc';
				break;
			case 'rebate_toggle' :
				$order = 'B.rebate_money_rate asc';
				break;
            default :
            case 'new_toggle':
                $order = 'A.sort desc, A.create_time desc';
                break;
        };
        //筛选状态
        $filterStatus = ($filter['attrList'] || $minPrice || $maxPrice || $brandId) ? true : false;

        return [
            'filter' => $filter,
            'where' => $where,
            'filterStatus' => $filterStatus,
            'order' => $order
        ];
    }

    private function getUrlOrder() {
        if (!empty($this->order)) {
            return $this->order;
        }
        $order = request('get', 'order');
        $orderAttr = ['sale', 'sale_toggle', 'price', 'price_toggle', 'new', 'new_toggle', 'rebate', 'rebate_toggle'];

        if (!in_array($order, $orderAttr)) {
            $order = 'default';
        }
        $this->order = $order;
        return $order;
    }

    public function getOrderData() {
        $order = $this->getUrlOrder();

        return [
            'default' => [
                'name' => '默认',
                'url' => $this->getUrl([
                    'order' => ''
                ]),
                'single_url' => $this->getUrl([
                    'order' => ''
                ]),
                'up' => false,
                'down' => false,
                'cur' => $order == 'default' ? true : false,
            ],
            'new' => [
                'name' => '新品',
                'url' => $this->getUrl([
                    'order' => ($order == 'new') ? 'new_toggle' : 'new'
                ]),
                'single_url' => $this->getUrl([
                    'order' => ($order == 'new') ? '' : 'new'
                ]),
                'up' => ($order == 'new') ? true : false,
                'down' => ($order == 'new_toggle') ? true : false,
                'cur' => ($order == 'new' || $order == 'new_toggle') ? true : false,
            ],
            'sale' => [
                'name' => '销量',
                'url' => $this->getUrl([
                    'order' => ($order == 'sale') ? 'sale_toggle' : 'sale'
                ]),
                'single_url' => $this->getUrl([
                    'order' => ($order == 'sale') ? '' : 'sale'
                ]),
                'up' => ($order == 'sale') ? true : false,
                'down' => ($order == 'sale_toggle') ? true : false,
                'cur' => ($order == 'sale' || $order == 'new_toggle') ? true : false,
            ],
            'price' => [
                'name' => '价格',
                'url' => $this->getUrl([
                    'order' => ($order == 'price') ? 'price_toggle' : 'price'
                ]),
                'single_url' => $this->getUrl([
                    'order' => ($order == 'price') ? '' : 'price'
                ]),
                'up' => ($order == 'price') ? true : false,
                'down' => ($order == 'price_toggle') ? true : false,
                'cur' => ($order == 'price' || $order == 'price_toggle') ? true : false,
            ],
        ];
    }

    private function getUrlBrand() {
        if (!empty($this->brand)) {
            return $this->brand;
        }
        $brand = intval(request('get', 'brand'));
        $this->brand = $brand;
        return $this->brand;
    }

    public function getBrandData($ids = '') {
        if (empty($ids)) {
            return [];
        }
        $where = [];
        $where = ['_sql' => 'content_id IN(' . $ids . ')'];
        $brandList = target($this->model)->field(['brand_id'])->where($where)->limit(0)->select();

        if (empty($brandList)) {
            return [];
        }
        $brandIds = [];
        foreach ($brandList as $brand) {
            if ($brand['brand_id']) {
                $brandIds[] = $brand['brand_id'];
            }
        }
        $brandIds = array_unique($brandIds);
        if (empty($brandIds)) {
            return [];
        }
        $list = target('shop/ShopBrand')->where([
            '_sql' => 'brand_id IN(' . implode(',', $brandIds) . ')'
        ])->select();
        if (empty($list)) {
            return [];
        }
        $brandId = $this->getUrlBrand();

        foreach ($list as $key => $vo) {
            $list[$key]['url'] = $this->getUrl(['brand' => $vo['brand_id']]);
            if ($brandId == $vo['brand_id']) {
                $list[$key]['cur'] = true;
            } else {
                $list[$key]['cur'] = false;
            }
        }
        array_unshift($list, [
            'brand_id' => '0',
            'name' => '不限',
            'cur' => $brandId ? false : true,
            'url' => $this->getUrl(['brand' => ''])
        ]);

        return $list;
    }

    private function getUrlPrice() {
        if (!empty($this->price)) {
            return $this->price;
        }
        $priceAttrData = request('get', 'price');
        $priceAttr = explode('_', $priceAttrData, 2);
        $minPrice = intval($priceAttr[0]);
        $maxPrice = intval($priceAttr[1]);
        $this->price = [$minPrice, $maxPrice];
        return $this->price;
    }

    public function getPriceData($ids = '') {
        if (empty($ids)) {
            return [];
        }
        $showPriceNum = 5;
        $where = [];
        $where['_sql'] = 'content_id in (' . $ids . ')';
        $goodsPrice = target($this->model)->field(['MIN(sell_price)(min)', 'MAX(sell_price)(max)'])->where($where)->find();

        if ($goodsPrice['min'] < 0) {
            return [];
        }
        $minPrice = ceil($goodsPrice['min']);

        //商品价格计算
        $result = ['0~' . $minPrice];
        $perPrice = floor(($goodsPrice['max'] - $minPrice) / ($showPriceNum - 1));
        if ($perPrice > 0) {
            for ($addPrice = $minPrice + 1; $addPrice < $goodsPrice['max'];) {
                $stepPrice = $addPrice + $perPrice;
                $stepPrice = substr(intval($stepPrice), 0, 1) . str_repeat('9', (strlen(intval($stepPrice)) - 1));
                $result[] = $addPrice . '~' . $stepPrice;
                $addPrice = $stepPrice + 1;
            }
        }

        $priceAttr = $this->getUrlPrice();
        $minPrice = $priceAttr[0];
        $maxPrice = $priceAttr[1];
        if ($priceAttr[0] == 0 && $priceAttr[1] == 0) {
            $cur = true;
        } else {
            $cur = false;
        }
        $priceList = [];
        $priceList[] = [
            'name' => '不限',
            'url' => $this->getUrl(['price' => '']),
            'value' => '0_0',
            'cur' => $cur,
        ];
        foreach ($result as $key => $vo) {
            $arr = explode('~', $vo);
            if (($minPrice || $maxPrice) && ($minPrice >= $arr[0] && $maxPrice <= $arr[1])) {
                $cur = true;
            } else {
                $cur = false;
            }
            $priceList[] = [
                'name' => $vo,
                'value' => $arr[0] . '_' . $arr[1],
                'url' => $this->getUrl(['price' => $arr[0] . '_' . $arr[1]]),
                'cur' => $cur,
            ];
        }
        return $priceList;
    }

    private function getUrl($urlParam = []) {
        return url('index', array_filter(array_merge($this->urlParam, $urlParam)));
    }
}

