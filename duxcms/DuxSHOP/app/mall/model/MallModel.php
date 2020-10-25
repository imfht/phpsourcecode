<?php

/**
 * 商品管理
 */

namespace app\mall\model;

use app\system\model\SystemModel;

class MallModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'mall_id',
        'validate' => [
            'class_id' => [
                'empty' => ['', '请选择分类!', 'must', 'all'],
            ],
        ],
        'format' => [
            'content' => [
                'function' => ['html_in', 'all', 0],
            ]
        ]
    ];


    protected function base($where, $modelId = 0) {
        $base = $this->table('site_content(A)')
            ->join('mall(B)', ['B.content_id', 'A.content_id'])
            ->join('mall_class(C)', ['C.class_id', 'B.class_id'])
            ->join('site_class(D)', ['D.category_id', 'C.category_id']);
        $field = ['A.*', 'B.*', 'D.name(class_name)', 'D.model_id', 'D.filter_id'];
        if ($modelId) {
            $modelInfo = target('site/SiteModel')->getInfo($modelId);
            $base = $base->join('model_' . $modelInfo['label'] . '(E)', ['E.content_id', 'A.content_id'], '<');
            $field[] = 'E.*';
        }

        return $base
            ->field($field)
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.sort desc, A.create_time desc, B.mall_id desc', $modelId = 0) {
        $list = $this->base($where, $modelId)
            ->limit($limit)
            ->order($order)
            ->select();
        if (empty($list)) {
            return [];
        }
        foreach ($list as $key => $vo) {
            $list[$key]['url'] = $this->getUrl($vo['mall_id']);
        }
        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where, $modelId = 0) {
        $info = $this->base($where, $modelId)->find();
        if($info) {
            $info['url'] = $this->getUrl($info['mall_id']);
        }
        return $info;
    }

    public function getInfo($id, $modelId = 0) {
        $where = [];
        $where['B.mall_id'] = $id;
        return $this->getWhereInfo($where, $modelId);
    }

    public function getUrl($id) {
        return url(VIEW_LAYER_NAME . '/mall/Info/index', ['id' => $id]);
    }

    public function saveData($type = 'add', $data = []) {
        $this->beginTransaction();
        $data = empty($data) ? $_POST : $data;

        if ($data['content'] && empty($data['description'])) {
            $data['description'] = \dux\lib\Str::strMake($data['content'], 240);
        }

        if (empty($data['images'])) {
            $this->rollBack();
            $this->error = '请至少上传一张图片!';

            return false;
        }

        if (empty($data['content'])) {
            $this->rollBack();
            $this->error = '请填写商品详情!';

            return false;
        }
        $images = [];
        foreach ($data['images']['url'] as $key => $vo) {
            $images[] = [
                'url' => $vo,
                'title' => $data['images']['title'][$key]
            ];
        }
        $data['images'] = serialize($images);
        //处理缩略图
        $data['image'] = target('site/Tools', 'service')->coverImage($images[0]['url']);
        //处理规格数据
        $specData = '';
        if (isset($data['data']['spec'])) {
            $goods_spec_array = [];
            foreach ($data['data']['spec'] as $key => $val) {
                foreach ($val as $v) {
                    $tempSpec = json_decode($v, true);
                    if (!isset($goods_spec_array[$tempSpec['id']])) {
                        $goods_spec_array[$tempSpec['id']] = ['id' => $tempSpec['id'], 'name' => $tempSpec['name'], 'value' => []];
                    }
                    $goods_spec_array[$tempSpec['id']]['value'][] = $tempSpec['value'];
                }
            }
            foreach ($goods_spec_array as $key => $val) {
                $val['value'] = array_unique($val['value']);
                $goods_spec_array[$key]['value'] = join(',', $val['value']);
            }
            $specData = serialize($goods_spec_array);
        }
        $data['spec_data'] = $specData;

        $data['goods_no'] = $data['data']['goods_no'][0];
        $data['barcode'] = $data['data']['barcode'][0];
        $data['sell_price'] = $data['data']['sell_price'][0];
        $data['market_price'] = $data['data']['market_price'][0];
        $data['cost_price'] = $data['data']['cost_price'][0];
        $data['store'] = 0;
        $data['weight'] = $data['data']['weight'][0];
        $data['update_time'] = time();

        if ($data['up_time']) {
            $data['up_time'] = strtotime($data['up_time']);
        }
        if ($data['down_time']) {
            $data['down_time'] = strtotime($data['down_time']);
        }

        $specData = $data['data'];
        $proData = [];
        $store = 0;
        foreach ($specData['goods_no'] as $key => $vo) {
            $proData[$key] = [
                'products_id' => $specData['id'][$key],
                'products_no' => $vo,
                'barcode' => $specData['barcode'][$key],
                'sell_price' => $specData['sell_price'][$key],
                'market_price' => $specData['market_price'][$key],
                'cost_price' => $specData['cost_price'][$key],
                'store' => $specData['store'][$key],
                'weight' => $specData['weight'][$key],
                'spec_data' => $this->mergerSpec($specData['spec'][$key])
            ];
            if (empty($vo)) {
                $this->rollBack();
                $this->error = '商品货号未填写!';
                return false;
            }
            $store += intval($specData['store'][$key]);
        }
        $data['store'] = $store;
        $mallId = $data['mall_id'];
        if ($type == 'add') {
            $data['app'] = 'mall';
            $data['create_time'] = date('Y-m-d H:i:s');
            $id = target('site/SiteContent')->saveData('add', $data);
            if (!$id) {
                $this->rollBack();
                $this->error = target('site/SiteContent')->getError();

                return false;
            }
            $data['content_id'] = $id;
            $id = parent::saveData('add', $data);
            $mallId = $id;
            if (!$id) {
                $this->rollBack();
                $this->error = $this->getError();

                return false;
            }
        }
        if ($type == 'edit') {
            $info = $this->getInfo($mallId);
            $data['content_id'] = $info['content_id'];
            $status = target('site/SiteContent')->saveData('edit', $data);
            if (!$status) {
                $this->rollBack();
                $this->error = target('site/SiteContent')->getError();

                return false;
            }
            $status = parent::saveData('edit', $data);
            if (!$status) {
                $this->rollBack();
                $this->error = $this->getError();

                return false;
            }
        }
        //处理货品
        $proIds = [];
        foreach ($proData as $vo) {
            $vo['mall_id'] = $mallId;
            if ($vo['products_id']) {
                $status = target('mall/MallProducts')->edit($vo);
                $proIds[] = $vo['products_id'];
            } else {
                $status = target('mall/MallProducts')->add($vo);
                $proIds[] = $status;
            }
            if (!$status) {
                $this->error = target('mall/MallProducts')->getError();

                return false;
            }
        }
        $status = target('mall/MallProducts')->where([
            '_sql' => 'products_id NOT IN (' . implode(',', $proIds) . ')',
            'mall_id' => $mallId
        ])->delete();

        if (!$status) {
            $this->error = target('mall/MallProducts')->getError();

            return false;
        }

        $this->commit();
        return $mallId;
    }

    /**
     * 合并规格
     */
    protected function mergerSpec($data) {
        if ($data) {
            $data = str_replace("'", '"', $data);

            return serialize(json_decode('[' . implode(',', $data) . ']', true));
        } else {
            return '';
        }
    }

    public function delData($id) {
        $info = $this->getInfo($id);
        $this->beginTransaction();
        $where = [];
        $where['mall_id'] = $id;
        if (!$this->where($where)->delete()) {
            $this->rollBack();

            return false;
        }
        if (!target('site/SiteContent')->delData($info)) {
            $this->rollBack();

            return false;
        }
        if (!target('mall/MallProducts')->where(['mall_id' => $id])->delete()) {
            $this->rollBack();

            return false;
        }
        $this->commit();

        return true;
    }

}