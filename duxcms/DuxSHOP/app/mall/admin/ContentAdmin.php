<?php

/**
 * 商品管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class ContentAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'Mall';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商品管理',
                'description' => '管理站点中的商品信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'class_id' => 'B.class_id',
            'pos_id' => 'pos_id',
            'keyword' => 'A.title,B.goods_no',
			'status' => 'A.status'
        ];
    }

    protected function _indexWhere($whereMaps) {
		if(!isset($whereMaps['A.status'])){
			$whereMaps['A.status'] = 1;	
		}
        if ($whereMaps['pos_id']) {
            $whereMaps['_sql'] = 'FIND_IN_SET(' . $whereMaps['pos_id'] . ', A.pos_id)';
        }

        unset($whereMaps['pos_id']);
        return $whereMaps;
    }

    public function _indexOrder() {
        return 'B.mall_id desc';
    }


    public function _indexAssign($pageMaps) {
        $classId = $pageMaps['class_id'];
        return array(
            'posList' => target('site/SitePosition')->loadList(),
            'treeHtml' => target('mall/MallClass')->getHtmlTree(0, $classId, ['parent_id', 'class_id', 'name']),
            'classId' => $classId,
        );
    }

    public function _addAssign() {
        $classId = request('get', 'class_id', 0, 'intval');
        $classInfo = target('mall/MallClass')->getInfo($classId);

        //扩展模型
        $modelId = intval($classInfo['model_id']);
        $modelHtml = target('site/SiteModel')->getHtml($modelId);


        //运费模板
        $deliveryList = target('order/OrderConfigDelivery')->loadList([], 0, 'delivery_id asc');
        $shopConfig = target('shop/ShopConfig')->getConfig();

        return array(
            'classList' => target('mall/MallClass')->loadTreeList(['A.model_id' => $modelId]),
            'posList' => target('site/SitePosition')->loadList(),
            'brandList' => target('shop/ShopBrand')->loadList(),
            'deliveryList' => $deliveryList,
            'modelHtml' => $modelHtml,
            'classId' => $classId,
            'proDataJson' => json_encode([]),
            'proHeadJson' => json_encode([]),
            'specJson' => json_encode([]),
            'productNo' => \app\shop\unit\SequenceNumber::get(time(), 12, 'D'),
            'shopConfig' => $shopConfig,
            'urlApp' => 'mall'
        );
    }

    public function _editAssign($info) {
        $classId = intval($info['class_id']);
        $modelId = intval($info['model_id']);

        //扩展模型
        $modelInfo = target('site/SiteModel')->getContent($modelId, $info['content_id']);
        $modelHtml = target('site/SiteModel')->getHtml($modelId, $modelInfo);


        //运费模板
        $deliveryList = target('order/OrderConfigDelivery')->loadList([], 0, 'delivery_id asc');
        $shopConfig = target('shop/ShopConfig')->getConfig();

        //重组产品数据
        $proData = array();
        $proHead = array();
        $headStatus = false;
        $proList = target('mall/MallProducts')->loadList(array('A.mall_id' => $info['mall_id']), 0, 1);

        foreach ($proList as $key => $vo) {
            $specData = unserialize($vo['spec_data']);
            if (!$headStatus) {
                if (!empty($specData)) {
                    foreach ($specData as $k => $v) {
                        $proHead[$k] = array(
                            'id' => $v['id'],
                            'name' => $v['name'],
                        );
                    }
                }
                $headStatus = true;
            }
            $proData['spec_list'][$key] = $specData;
            $proData['id'][$key] = $vo['products_id'];
            $proData['goods_no'][$key] = $vo['products_no'];
            $proData['barcode'][$key] = $vo['barcode'];
            $proData['sell_price'][$key] = $vo['sell_price'];
            $proData['market_price'][$key] = $vo['market_price'];
            $proData['cost_price'][$key] = $vo['cost_price'];
            $proData['store'][$key] = $vo['store'];
            $proData['weight'][$key] = $vo['weight'];
        }

        return array(
            'classList' => target('mall/MallClass')->loadTreeList(['A.model_id' => $modelId]),
            'posList' => target('site/SitePosition')->loadList(),
            'brandList' => target('shop/ShopBrand')->loadList(),
            'deliveryList' => $deliveryList,
            'modelHtml' => $modelHtml,
            'classId' => $classId,
            'proDataJson' => json_encode($proData),
            'proHeadJson' => json_encode($proHead),
            'specJson' => json_encode(unserialize($info['spec_data'])),
            'productNo' => \app\shop\unit\SequenceNumber::get(time(), 12, 'D'),
            'shopConfig' => $shopConfig,
            'urlApp' => 'mall'
        );
    }


    protected function _indexUrl($id) {
        return url('index', array('class_id' => request('post', 'class_id')));
    }

    public function _statusData($id, $status) {
        $info = target('site/SiteContent')->getInfo($id);
        return target('site/SiteContent')->where([
            'content_id' => $info['content_id']
        ])->data([
            'status' => $status
        ])->update();
    }

    public function getSpec() {
        $classId = request('get', 'class_id', 0, 'intval');
        $app = request('get', 'app', '', 'html_clear');
        target('shop/Spec', 'middle')->setParams([
            'class_id' => $classId,
            'app' => $app,
        ])->data()->export(function ($data) {
            $this->success($data['html']);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


    public function getFilterHtml() {
        $classId = request('get', 'class_id', 0, 'intval');
        $app = request('get', 'app', '', 'html_clear');
        $type = request('get', 'type', 0, 'intval');
        $contentId = request('get', 'content_id', 0, 'intval');
        target('site/Fliter', 'middle')->setParams([
            'class_id' => $classId,
            'app' => $app,
            'type' => $type,
            'content_id' => $contentId
        ])->data()->export(function ($data) {
            $this->success($data['html']);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }




}