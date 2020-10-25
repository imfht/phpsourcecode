<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Evaluation;
use App\Models\EvaluationImage;
use App\Models\Favorite;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\MarketSeckill;
use App\Models\Member;
use App\Models\Seller;
use App\Services\GoodsSearchService;
use App\Services\GoodsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class GoodsController extends BaseController
{
    /**
     * 商品列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function search(Request $request)
    {
        list($page, $limit, $offset) = get_page_params();
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $seller_category_id = $request->input('seller_category_id');
        $seller_id = $request->input('seller_id');
        $brand_id = $request->input('brand_id');
        $min_price = (int)$request->input('min_price');
        $max_price = (int)$request->input('max_price');
        $orderby = $request->input('orderby');
        $attribute = $request->input('attribute');

        if ($page > 100) {
            api_error(__('api.goods_search_max_page'));
        }

        //关键字、分类、必须有一个
        if (!$keyword && !$category_id) {
            api_error(__('api.search_key_and_category_error'));
        }

        //属性组装
        $where_attr = array();
        if ($attribute) {
            $_attribute = explode(';', $attribute);
            foreach ($_attribute as $value) {
                if ($value) {
                    $_value = explode(':', $value);
                    if ($_value[0] && $_value[1]) {
                        $where_attr[$_value[0]] = explode(',', $_value[1]);
                    }
                }
            }
        }

        //排序组装
        $orderby_data = array();
        if ($orderby) {
            switch ($orderby) {
                case 'sale':
                    $orderby_data['sale'] = 'desc';
                    break;
                case 'price_desc':
                    $orderby_data['sell_price'] = 'desc';
                    break;
                case 'price_asc':
                    $orderby_data['sell_price'] = 'asc';
                    break;
            }
        }

        $search_where = array(
            'keyword' => $keyword,
            'category_id' => format_number($category_id),
            'seller_category_id' => format_number($seller_category_id),
            'seller_id' => format_number($seller_id),
            'brand_id' => format_number($brand_id),
            'min_price' => $min_price,
            'max_price' => $max_price,
            'attribute' => $where_attr,
            'orderby' => $orderby_data
        );
        $is_screening = $page == 1 ? true : false;//只有第一页才出现筛选项
        $return = GoodsSearchService::search($search_where, $limit, $offset, $is_screening);
        if (!$return['total']) {
            api_error(__('api.content_is_empty'));
        }
        return $this->success($return);
    }

    /**
     * 商品详情
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->id;
        $type = (int)$request->type;
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        $cache_key = 'goods:' . $id . '_' . $type;
        $goods = cache($cache_key);
        if (!$goods) {
            $goods = Goods::select('id', 'title', 'subtitle', 'image', 'shelves_status', 'seller_id', 'market_type', 'market_id')->find($id);
            if (!$goods) {
                api_error(__('api.content_is_empty'));
            }
            //是否收藏
            $goods['favorite'] = 0;
            if ($m_id) {
                if (Favorite::where(['m_id' => $m_id, 'object_id' => $goods['id'], 'type' => Favorite::TYPE_GOODS])->exists()) {
                    $goods['favorite'] = 1;
                }
            }
            $goods['num'] = $goods->num()->select('favorite', 'sale')->first();
            $goods['content'] = $goods->content()->value('content');
            $goods['image_list'] = $goods->image()->select('image as url')->get();
            $goods['sku'] = array();
            $goods['spec'] = array();
            $goods['attribute'] = array();
            $sku = $goods->goodsSku()->select('id', 'image', 'spec_value', 'stock', 'sell_price', 'market_price', 'min_buy', 'activity_stock', 'activity_price')->where('status', GoodsSku::STATUS_ON)->get();
            //获取子商品并组装规格
            $goods_sku = $spec = array();
            $goods['stock'] = 0;
            foreach ($sku as $value) {
                $spec_value = json_decode($value['spec_value'], true);
                $sku_spec_id = array();
                if ($spec_value) {
                    foreach ($spec_value as $val) {
                        $_spec = array(
                            'id' => $val['id'],
                            'alias' => $val['alias'],
                            'image' => $val['image']
                        );
                        $spec[$val['name']][$val['id']] = $_spec;
                        $sku_spec_id[] = $val['id'];
                    }
                }
                $value = GoodsService::getVipPrice($value->toArray(), $type);//获取折扣价格
                $_item = $value;
                $_item['sku_spec_id'] = join('_', $sku_spec_id);
                unset($_item['spec_value']);
                $goods['stock'] += $value['stock'];
                $goods_sku[] = $_item;
            }
            //组装规格
            foreach ($spec as $key => $value) {
                $_item = array(
                    'name' => $key,
                    'value' => array_values($value)
                );
                $spec[$key] = $_item;
            }
            $goods['sku'] = $goods_sku;
            $goods['spec'] = array_values($spec);

            //获取商品属性
            $goods_attribute = $goods->attribute()->select('attribute_id', 'value')->get();
            $attribute_ids = array();
            foreach ($goods_attribute as $value) {
                $attribute_ids[] = $value['attribute_id'];
            }
            //获取属性信息
            if ($attribute_ids) {
                $attribute = Attribute::whereIn('id', array_unique($attribute_ids))->select('id', 'title', 'input_type')->get();
                $attribute = array_column($attribute->toArray(), null, 'id');
            }
            //获取属性id
            $attr_value_ids = array();
            foreach ($goods_attribute as $value) {
                if (isset($attribute[$value['attribute_id']]) && $attribute[$value['attribute_id']]['input_type'] != 'text') {
                    $attr_value_ids[] = $value['value'];
                }
            }
            //获取属性值
            if ($attr_value_ids) {
                $attr_value = AttributeValue::whereIn('id', array_unique($attr_value_ids))->pluck('value', 'id')->toArray();
            }
            //组装属性
            $goods_attr = array();
            foreach ($goods_attribute as $value) {
                if (isset($attribute[$value['attribute_id']])) {
                    $_attribute = $attribute[$value['attribute_id']];
                    if ($_attribute['input_type'] == 'text') {
                        $_value = $value['value'];
                    } else {
                        $_value = isset($attr_value[$value['value']]) ? $attr_value[$value['value']] : '';
                    }
                    $goods_attr[$value['attribute_id']]['name'] = $_attribute['title'];
                    $goods_attr[$value['attribute_id']]['value'][] = $_value;
                }
            }
            $goods['attribute'] = array_values($goods_attr);
            //商家信息
            $goods['seller'] = Seller::select('id', 'title', 'image')->find($goods['seller_id']);
            //获取商品价格
            $goods['show_price'] = min(array_column($goods['sku'], 'show_price'));
            $goods['line_price'] = min(array_column($goods['sku'], 'line_price'));
            cache([$cache_key, $goods], 100);
        }
        $error = '';
        //获取秒杀信息
        if ($type == Goods::MARKET_TYPE_SECKILL) {
            if ($goods['market_type'] != Goods::MARKET_TYPE_SECKILL) {
                $error = __('api.goods_not_seckill');
            } else {
                $seckill = MarketSeckill::where('id', $goods['market_id'])->first();
                if (!$seckill) {
                    $error = __('api.goods_not_seckill');
                } elseif ($seckill['end_at'] < get_date() || $seckill['status'] != MarketSeckill::STATUS_ON) {
                    $error = __('api.seckill_is_end');
                } else {
                    list($pct, $stock, $remaining_stock, $sale) = $this->getPct($id);
                    $goods['pct'] = $pct;
                    $goods['start_at'] = $seckill['start_at'];
                    $goods['end_at'] = $seckill['end_at'];
                    //秒杀库存读取redis
                    $goods['stock'] = $stock['all'];
                    $goods_sku = $goods['sku'];
                    foreach ($goods_sku as $key => $val) {
                        $goods_sku[$key]['stock'] = $stock[$val['id']];
                    }
                    $goods['sku'] = $goods_sku;
                    $goods['num']['sale'] = $sale;
                }
            }
        }
        $goods['error'] = substr($error, 6);
        unset($goods['market_type'], $goods['market_id']);
        return $this->success($goods);
    }

    /**
     * 商品评价列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function evaluation(Request $request)
    {
        $id = (int)$request->id;
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'goods_id' => $id,
            'status' => Evaluation::STATUS_ON
        ];
        $res_list = Evaluation::select('id', 'm_id', 'spec_value', 'content', 'is_image', 'created_at as create_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $m_ids = $e_ids = array();
        foreach ($res_list as $value) {
            $m_ids[] = $value['m_id'];
            if ($value['is_image'] == Evaluation::IS_IMAGE_TRUE) {
                $e_ids[] = $value['id'];
            }
        }
        if ($m_ids) {
            $res_member = Member::whereIn('id', $m_ids)->select('id', 'nickname', 'headimg')->get();
            $member = array_column($res_member->toArray(), null, 'id');
        }
        $image_list = array();
        if ($e_ids) {
            $res_image = EvaluationImage::whereIn('e_id', $e_ids)->select('e_id', 'image')->get();
            if (!$res_image->isEmpty()) {
                foreach ($res_image as $value) {
                    $image_list[$value['e_id']][]['url'] = $value['image'];
                }
            }
        }
        $lists = array();
        foreach ($res_list as $value) {
            $_member = isset($member[$value['m_id']]) ? $member[$value['m_id']] : array();
            $_item = $value;
            $_item['nickname'] = $_member['nickname'];
            $_item['headimg'] = $_member['headimg'];
            $_item['image_list'] = isset($image_list[$value['id']]) ? $image_list[$value['id']] : array();
            $lists[] = $_item;
        }
        $total = Evaluation::where($where)->count();
        $return = [
            'lists' => $lists,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 查询redis库存比例
     * @param $goods_id
     * @return float|int
     */
    public function getPct($goods_id)
    {
        if (!$goods_id) return 0;
        //查询库存
        $_redis_key = MarketSeckill::GOODS_REDIS_KEY . $goods_id;
        $stock = Redis::hgetall($_redis_key);
        if (!$stock) return 0;
        $_sum_num = array_sum($stock) - $stock['all'];
        $_all_num = $stock['all'];
        $pct = format_price(1 - ($_sum_num / $_all_num), 2, false) * 100;
        $remaining_stock = $_sum_num;//剩余库存
        $sale = $stock['all'] - $_sum_num;//已经销售的存库
        return [$pct, $stock, $remaining_stock, $sale];//已经销售比例，库存信息，剩余库存，已经销售数量
    }
}
