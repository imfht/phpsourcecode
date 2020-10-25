<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Seller\Goods;

use App\Http\Controllers\Seller\BaseController;
use App\Libs\Elasticsearch\EsGoods;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Goods;
use App\Models\GoodsAttribute;
use App\Models\GoodsSku;
use App\Models\Spec;
use App\Models\SpecValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class GoodsController extends BaseController
{

    public function __construct()
    {
        $this->goods_search_es = config('other.goods.goods_search_es');
        $this->es_goods = new EsGoods();
    }

    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $seller_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $id = (int)$request->input('id');
        $title = $request->input('title');
        $category_id = (int)$request->input('category_id');
        $brand_id = (int)$request->input('brand_id');
        $status = $request->input('status');
        $shelves_status = $request->input('shelves_status');
        $is_rem = $request->input('is_rem');

        //搜索
        $where = array();
        $where[] = array('seller_id', $seller_id);
        if ($id) $where[] = array('id', $id);
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        if ($category_id) $where[] = array('category_id', $category_id);
        if ($brand_id) $where[] = array('brand_id', $brand_id);
        if (is_numeric($status)) $where[] = array('status', $status);
        if (is_numeric($shelves_status)) $where[] = array('shelves_status', $shelves_status);
        if (is_numeric($is_rem)) $where[] = array('is_rem', $is_rem);

        $res_list = Goods::select('id', 'title', 'sku_code', 'image', 'market_price', 'sell_price', 'is_rem', 'category_id', 'position', 'shelves_status', 'status', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $category_ids = array();
        foreach ($res_list as $value) {
            $category_ids[] = $value['category_id'];
        }
        //获取分类名称
        if ($category_ids) {
            $category = Category::whereIn('id', array_unique($category_ids))->pluck('title', 'id');
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['category'] = isset($category[$value['category_id']]) ? $category[$value['category_id']] : '';
            $data_list[] = $_item;
        }
        $total = Goods::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 根据id获取信息
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $seller_id = $this->getUserId();
        $id = (int)$request->input('id');
        if ($id) {
            $data = Goods::where(['id' => $id, 'seller_id' => $seller_id])->first();
            $data['goods_image'] = $data->image()->pluck('image')->toArray();//查询商品图片
            $data['content'] = $data->content()->value('content');
            $data['seller_category'] = $data->sellerCategory()->pluck('category_id')->toArray();//商家分类
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        return $this->success($data);
    }

    /**
     * 添加编辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function save(Request $request)
    {
        $id = (int)$request->input('id');
        //验证规则
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required|numeric',
            'delivery_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'image' => 'required|array',
            'sku_code' => [
                'required',
                Rule::unique('goods')->ignore($id)
            ],
            'position' => 'required|numeric',
            'spec_market_price' => 'required|array',
            'spec_sell_price' => 'required|array',
            'spec_stock' => 'required|array',
            'spec_stock[]' => 'numeric',
            'spec_sku_code' => 'required|array',
            'spec_weight' => 'required|array',
            'spec_weight[]' => 'numeric',
            'spec_min_buy' => 'required|array',
            'spec_min_buy[]' => 'numeric',
        ], [
            'title.required' => '标题不能为空',
            'category_id.required' => '分类id不能为空',
            'category_id.numeric' => '分类id只能是数字',
            'delivery_id.required' => '运费模板不能为空',
            'delivery_id.numeric' => '运费模板只能是数字',
            'brand_id.required' => '排序不能为空',
            'brand_id.numeric' => '排序只能是数字',
            'image.required' => '图片不能为空',
            'image.array' => '图片不能为空',
            'sku_code.required' => '货号不能为空',
            'sku_code.unique' => '货号已经存在',
            'position.required' => '排序不能为空',
            'position.numeric' => '排序只能是数字',
            'spec_market_price.required' => '市场价不能为空',
            'spec_market_price.array' => '市场价参数错误',
            'spec_sell_price.required' => '销售价不能为空',
            'spec_sell_price.array' => '销售价参数错误',
            'spec_stock.required' => '库存不能为空',
            'spec_stock.array' => '库存参数错误',
            'spec_stock[].numeric' => '库存只能是数字',
            'spec_sku_code.required' => '货号不能为空',
            'spec_sku_code.array' => '货号参数错误',
            'spec_weight.required' => '重量不能为空',
            'spec_weight.array' => '重量参数错误',
            'spec_weight[].numeric' => '重量只能是数字',
            'spec_min_buy.required' => '起订量不能为空',
            'spec_min_buy.array' => '起订量参数错误',
            'spec_min_buy[].numeric' => '起订量只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $seller_id = $this->getUserId();
        $save_data = Goods::getGoodsData($request, true);
        $save_data['seller_id'] = $seller_id;
        $res = Goods::saveData($save_data, $id);
        if ($res) {
            if ($this->goods_search_es) $this->es_goods->addDoc($res);//更新es
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $seller_id = $this->getUserId();
        $ids = $this->checkBatchId();
        $status = (int)$request->input('status');
        if ($ids && isset($status)) {
            $res = Goods::where('seller_id', $seller_id)->whereIn('id', $ids)->update(['status' => $status, 'shelves_status' => Goods::SHELVES_STATUS_OFF]);
            if ($res) {
                if ($this->goods_search_es) $this->es_goods->batchAddDoc($ids);//更新es
                return $this->success();
            } else {
                api_error(__('admin.fail'));
            }
        } else {
            api_error(__('admin.invalid_params'));
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $seller_id = $this->getUserId();
        $ids = $this->checkBatchId();
        $res = Goods::where('seller_id', $seller_id)->whereIn('id', $ids)->delete();
        if ($res) {
            if ($this->goods_search_es) $this->es_goods->delDoc($ids);//更新es
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 获取分类下的属性并判断是否已经选择
     * @return array
     */
    public function getAttribute(Request $request)
    {
        $category_id = $request->input('category_id');
        $goods_id = $request->input('goods_id');
        if (!$category_id) return array();
        $goods_attribute = array();
        //查询商品属性
        if ($goods_id) {
            $goods_attribute_res = GoodsAttribute::select('value', 'attribute_id')->where('goods_id', $goods_id)->get();
            if (!$goods_attribute_res->isEmpty()) {
                foreach ($goods_attribute_res as $value) {
                    $goods_attribute[$value['attribute_id']][] = $value['value'];
                }
            }
        }
        //查询分类下的属性
        $attribute = array();
        $attribute_res = Attribute::where('category_id', $category_id)
            ->select('id', 'title', 'input_type')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        if (!$attribute_res->isEmpty()) {
            $attribute_ids = array();
            foreach ($attribute_res->toArray() as $value) {
                $attribute_ids[] = $value['id'];
                if ($value['input_type'] == 'text' && isset($goods_attribute[$value['id']])) {
                    $value['value'] = current($goods_attribute[$value['id']]);
                }
                $attribute[$value['id']] = $value;
            }
            //获取属性值
            if ($attribute_ids) {
                $attribute_value_res = AttributeValue::whereIn('attribute_id', array_unique($attribute_ids))
                    ->select('id', 'value', 'attribute_id')
                    ->orderBy('position', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                if (!$attribute_value_res->isEmpty()) {
                    foreach ($attribute_value_res->toArray() as $value) {
                        if (isset($attribute[$value['attribute_id']])) {
                            $value['is_checked'] = 0;
                            if (isset($goods_attribute[$value['attribute_id']])) {
                                if (in_array($value['id'], $goods_attribute[$value['attribute_id']])) {
                                    $value['is_checked'] = 1;
                                }
                            }
                            $attribute[$value['attribute_id']]['value'][] = $value;
                        }
                    }
                }
            }
        }
        return $this->success(array_values($attribute));
    }

    /**
     * 获取分类下的规格并判断是否已经选择
     * @return array
     */
    public function getSpec(Request $request)
    {
        $category_id = $request->input('category_id');
        $goods_id = $request->input('goods_id');
        if (!$category_id) return array();
        $goods_spec = array();
        //查询子商品
        $goods_sku = array();
        if ($goods_id) {
            $goods_sku_res = GoodsSku::where([['status', GoodsSku::STATUS_ON], ['goods_id', $goods_id]])->get();
            if (!$goods_sku_res->isEmpty()) {
                foreach ($goods_sku_res as $value) {
                    $_key_arr = array();
                    $spec_value = json_decode($value['spec_value'], true);
                    foreach ($spec_value as $spec) {
                        $_key_arr[] = $spec['id'];
                        $goods_spec[$spec['id']] = array(
                            'value' => $spec['value'],
                            'image' => $spec['image'],
                            'alias' => $spec['alias'],
                        );
                    }
                    $_key = join('|', $_key_arr);
                    if (!$_key) $_key = 'default';
                    $goods_sku[$_key] = array(
                        'spec_sku_id' => $value['id'],
                        'spec_market_price' => $value['market_price'],
                        'spec_sell_price' => $value['sell_price'],
                        'spec_sku_code' => $value['sku_code'],
                        'spec_stock' => $value['stock'],
                        'spec_weight' => $value['weight'],
                        'spec_min_buy' => $value['min_buy']
                    );
                }
            }
        }
        $spec = array();
        //查询分类下的属性
        $spec_res = Spec::where('category_id', $category_id)
            ->select('id', 'title', 'type')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        if (!$spec_res->isEmpty()) {
            $spec_ids = array();
            foreach ($spec_res->toArray() as $value) {
                $spec_ids[] = $value['id'];
                $spec[$value['id']] = $value;
            }
            //判断依据选择的属性值
            if ($spec_ids) {
                $spec_value_res = SpecValue::whereIn('spec_id', $spec_ids)
                    ->select('id', 'value', 'spec_id')
                    ->orderBy('position', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                if (!$spec_value_res->isEmpty()) {
                    foreach ($spec_value_res->toArray() as $value) {
                        if (isset($spec[$value['spec_id']])) {
                            $_goods_spec = isset($goods_spec[$value['id']]) ? $goods_spec[$value['id']] : array();
                            //判断已经选择的
                            $value['is_checked'] = 0;
                            if (isset($_goods_spec['value']) && $_goods_spec['value'] == $value['value']) {
                                $value['is_checked'] = 1;
                            }
                            $value['alias'] = isset($_goods_spec['alias']) ? $_goods_spec['alias'] : '';
                            $value['image'] = isset($_goods_spec['image']) ? $_goods_spec['image'] : '';
                            $spec[$value['spec_id']]['value'][] = $value;
                        }
                    }
                }
            }
        }
        $return = array(
            'spec' => array_values($spec),
            'goods_sku' => $goods_sku
        );
        return $this->success($return);
    }
}
