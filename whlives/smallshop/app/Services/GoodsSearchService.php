<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

use App\Libs\Elasticsearch\EsGoods;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Goods;
use App\Models\GoodsAttribute;
use App\Models\GoodsSellerCategory;
use App\Models\Seller;
use App\Models\SellerCategory;

class GoodsSearchService
{

    /**
     * @param array $where_data 搜索条件
     * @param int $limit 查询条数
     * @param int $offset 偏移量
     * @param bool $is_screening 是否返回筛选项
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function search($where_data = array(), $limit = 100, $offset = 0, $is_screening = false)
    {
        //获取缓存信息
        $cache_key = 'goods_search:' . md5(json_encode($where_data) . 'limit' . $limit . 'offset' . $offset);
        $return = cache($cache_key);
        if ($return) {
            return $return;
        } else {
            //排序默认
            $orderby_data = ['is_rem' => 'desc'];
            if (isset($where_data['orderby'])) {
                $orderby_data = array_merge($orderby_data, $where_data['orderby']);
            }
            $orderby_data['shelves_at'] = 'desc';
            $orderby_data['updated_at'] = 'desc';
            $where_data['orderby'] = $orderby_data;

            $goods_search_es = config('app.goods_search_es');
            if ($goods_search_es) {
                //使用es搜索
                $es_goods = new EsGoods();
                $search_res = $es_goods->search($where_data, $limit, $offset);
            } else {
                //数据库搜索
                $search_res = self::getSearch($where_data, $limit, $offset);
            }
            list($goods_data, $total) = $search_res;

            $data_list = $screening = array();
            if ($goods_data) {
                foreach ($goods_data as $value) {
                    $_item = array(
                        'id' => $value['id'],
                        'title' => $value['title'],
                        'subtitle' => $value['subtitle'],
                        'image' => $value['image'],
                        'sell_price' => $value['sell_price'],
                        'market_price' => $value['market_price'],
                        'seller_id' => $value['seller_id'],
                        'sale' => $value['sale']
                    );
                    if ($is_screening) {
                        $screening['goods_id'][] = $value['id'];
                        $screening['brand_id'][] = $value['brand_id'];
                        $screening['seller_id'][] = $value['seller_id'];
                        $screening['category_id'][] = $value['category_id'];
                    }
                    $data_list[] = GoodsService::getVipPrice($_item);
                }
                $return = array(
                    'lists' => $data_list,
                    'total' => $total
                );
                if ($is_screening) {
                    $return['screening'] = self::screening($screening);
                }
                cache([$cache_key => $return], config('cache.time'));
            }
        }
        return $return;
    }

    /**
     * 搜索商品
     * @param array $where_data 搜索条件
     * @param int $limit 查询条数
     * @param int $offset 偏移量
     * @return array
     */
    static function getSearch($where_data = array(), $limit = 10, $offset = 0)
    {
        $where = $where_in = $goods_ids = array();
        $where[] = ['status', Goods::STATUS_ON];//已审核
        $where[] = ['shelves_status', Goods::SHELVES_STATUS_ON];//上架
        //关键字
        if (isset($where_data['keyword']) && $where_data['keyword']) {
            $where[] = ['title', 'like', '%' . $where_data['keyword'] . '%'];
        }
        $where_data_arr = ['category_id', 'seller_id', 'brand_id', 'goods_id', 'is_rem'];
        foreach ($where_data_arr as $type_id) {
            if (isset($where_data[$type_id]) && $where_data[$type_id]) {
                if (is_array($where_data[$type_id])) {
                    $where_in[$type_id] = $where_data[$type_id];
                } else {
                    $where[] = [$type_id, $where_data[$type_id]];
                }
            }
        }
        //最小价格
        if (isset($where_data['min_price']) && $where_data['min_price']) {
            $where[] = ['sell_price', '>=', $where_data['min_price']];
        }
        //最大价格
        if (isset($where_data['max_price']) && $where_data['max_price']) {
            $where[] = ['sell_price', '<=', $where_data['max_price']];
        }

        //商家分类
        if (isset($where_data['seller_category_id']) && $where_data['seller_category_id']) {
            if (is_array($where_data['seller_category_id'])) {
                $goods_id = GoodsSellerCategory::whereIn('category_id', $where_data['seller_category_id'])->pluck('goods_id')->toArray();
            } else {
                $goods_id = GoodsSellerCategory::where('category_id', $where_data['seller_category_id'])->pluck('goods_id')->toArray();
            }
            if ($goods_id) $goods_ids = array_merge($goods_ids, $goods_id);

        }
        //属性
        if (isset($where_data['attribute']) && $where_data['attribute']) {
            $goods_attr_query = GoodsAttribute::select('goods_id');
            foreach ($where_data['attribute'] as $attr_id => $attr_value) {
                if ($attr_id && $attr_value) {
                    $goods_attr_query->orWhere(function ($query) use ($attr_id, $attr_value) {
                        $query->where('attribute_id', $attr_id)->whereIn('value', $attr_value);
                    });
                }
            }
            $goods_id = $goods_attr_query->pluck('goods_id')->toArray();
            if ($goods_id) $goods_ids = array_merge($goods_ids, $goods_id);
        }
        if ($goods_ids) {
            $where_in['id'] = $goods_ids;
        }

        //开始查询数据
        $goods_query = Goods::select('id', 'title', 'subtitle', 'image', 'sell_price', 'market_price', 'seller_id', 'sale', 'brand_id', 'seller_id', 'category_id')
            ->where($where);
        if ($where_in) {
            foreach ($where_in as $key => $value) {
                $goods_query->whereIn($key, $value);
            }
        }
        foreach ($where_data['orderby'] as $key => $value) {
            $goods_query->orderBy($key, $value);
        }

        $goods_query->join('goods_num', 'goods.id', '=', 'goods_num.goods_id');
        $total = $goods_query->count();
        $goods_data = $goods_query->offset($offset)
            ->limit($limit)
            ->get();
        if (!$goods_data->isEmpty()) {
            $goods_data = $goods_data->toArray();
        }

        return [$goods_data, $total];
    }

    /**
     * 获取筛选信息
     * @param array $screening 筛选的条件
     * @return array
     */
    static function screening($screening = array())
    {
        $return = array();
        if ($screening['brand_id']) {
            $return['brand'] = Brand::select('id', 'title')->whereIn('id', array_unique($screening['brand_id']))->get();
        }
        if ($screening['seller_id']) {
            $return['seller'] = Seller::select('id', 'title')->whereIn('id', array_unique($screening['seller_id']))->get();
        }
        if ($screening['category_id']) {
            $return['category'] = Category::select('id', 'title')->whereIn('id', array_unique($screening['category_id']))->get();
        }
        //获取筛选属性
        if ($screening['goods_id']) {
            $goods_attr_res = GoodsAttribute::select('attribute_id', 'value')->whereIn('goods_id', $screening['goods_id'])->get();
            if (!$goods_attr_res->isEmpty()) {
                $attribute_ids = array();
                foreach ($goods_attr_res as $value) {
                    $attribute_ids[] = $value['attribute_id'];
                }
                if ($attribute_ids) {
                    $attribute = Attribute::whereIn('id', array_unique($attribute_ids))->select('id', 'title', 'input_type')->get();
                    $attribute = array_column($attribute->toArray(), null, 'id');
                }
//获取属性id
                $attr_value_ids = array();
                foreach ($goods_attr_res as $value) {
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
                foreach ($goods_attr_res as $value) {
                    if (isset($attribute[$value['attribute_id']])) {
                        $_attribute = $attribute[$value['attribute_id']];
                        if ($_attribute['input_type'] != 'text') {
                            $_item = array(
                                'id' => $value['value'],
                                'value' => isset($attr_value[$value['value']]) ? $attr_value[$value['value']] : ''
                            );
                            $goods_attr[$value['attribute_id']]['id'] = $value['attribute_id'];
                            $goods_attr[$value['attribute_id']]['name'] = $_attribute['title'];
                            $goods_attr[$value['attribute_id']]['value'][] = $_item;
                        }
                    }
                }
                $return['attribute'] = array_values($goods_attr);
            }
        }
        return $return;
    }
}
