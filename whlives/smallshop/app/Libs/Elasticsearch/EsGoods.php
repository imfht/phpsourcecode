<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/9
 * Time: 上午11:14
 */

namespace App\Libs\Elasticsearch;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Goods;
use App\Models\GoodsAttribute;
use App\Models\GoodsNum;
use App\Models\GoodsSellerCategory;
use App\Models\GoodsSku;
use App\Models\Seller;
use Elasticsearch\ClientBuilder;

/**
 * 商品es
 * Class Sms
 * @package App\Libs
 */
class EsGoods extends EsBase
{
    const ES_INDEX = 'goods';
    const ES_TYPE = 'goods';

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    /**
     * 创建索引
     */
    public function createIndex()
    {
        $params = [
            'index' => self::ES_INDEX,
        ];
        $response = $this->client->indices()->create($params);
    }

    /**
     * 删除索引
     */
    public function delIndex()
    {
        $deleteParams = [
            'index' => self::ES_INDEX
        ];
        $response = $this->client->indices()->delete($deleteParams);
    }

    /**
     * 创建文档
     * @param $goods_id
     */
    public function addDoc($goods_id)
    {
        $goods = Goods::select('id', 'title', 'subtitle', 'image', 'sell_price', 'market_price', 'is_rem', 'category_id', 'brand_id', 'seller_id', 'status', 'shelves_status', 'position', 'shelves_at', 'created_at as create_at', 'updated_at as update_at')->find($goods_id);
        if (!$goods) {
            return false;
        }
        $goods_num = $goods->num()->select('favorite', 'sale')->first();
        $seller_category = $goods->sellerCategory()->pluck('category_id')->toArray();
        $brand = Brand::select('id', 'title')->find($goods['brand_id']);
        $seller = Seller::select('id', 'title', 'image')->find($goods['seller_id']);
        $category = Category::select('id', 'title')->find($goods['category_id']);
        //子商品信息
        $sku = $goods->goodsSku()->select('spec_value', 'stock')->where('status', GoodsSku::STATUS_ON)->get();
        $stock = 0;
        $spec_alias = array();
        foreach ($sku as $value) {
            $stock += $value['stock'];
            $spec_value = json_decode($value['spec_value'], true);
            if ($spec_value) {
                foreach ($spec_value as $val) {
                    $spec_alias[] = $val['alias'];
                }
            }
        }

        //获取属性
        $goods_attr = array();
        $goods_attribute = $goods->attribute()->select('attribute_id', 'value')->get();
        foreach ($goods_attribute as $value) {
            $_value = json_decode($value['value'], true);
            $goods_attr[$value['attribute_id']][] = $_value;
        }

        $add_data = array(
            'id' => $goods['id'],
            'title' => $goods['title'],
            'subtitle' => $goods['subtitle'],
            'image' => $goods['image'],
            'sell_price' => $goods['sell_price'],
            'market_price' => $goods['market_price'],
            'is_rem' => $goods['is_rem'],
            'category_id' => $goods['category_id'],
            'category_title' => $category['title'],
            'brand_id' => $goods['brand_id'],
            'brand_title' => $brand['title'],
            'seller_id' => $goods['seller_id'],
            'seller_title' => $seller['title'],
            'status' => $goods['status'],
            'shelves_status' => $goods['shelves_status'],
            'position' => $goods['position'],
            'shelves_at' => get_date_time($goods['shelves_at'], true),
            'created_at' => get_date_time($goods['create_at'], true),
            'updated_at' => get_date_time($goods['update_at'], true),
            'favorite' => $goods_num['favorite'],
            'sale' => $goods_num['sale'],
            'seller_category_id' => $seller_category ? $seller_category : [],
            'stock' => $stock,
            'spec_alias' => array_values(array_unique($spec_alias)),
            'attribute' => $goods_attr
        );

        $params = [
            'index' => self::ES_INDEX,
            'type' => self::ES_TYPE,
            'id' => $goods['id'],
            'body' => $add_data
        ];
        $response = $this->client->index($params);
    }

    /**
     * 批量添加文档
     * @param array $goods_ids 商品id
     */
    public function batchAddDoc($goods_ids)
    {
        $goods_ids = format_number($goods_ids);
        if (count($goods_ids) == 1) {
            //单个id的时候不走批量
            $this->addDoc($goods_ids[0]);
        } else {
            $es_body = array();
            $goods_res = Goods::select('id', 'title', 'subtitle', 'image', 'sell_price', 'market_price', 'is_rem', 'category_id', 'brand_id', 'seller_id', 'status', 'shelves_status', 'position', 'shelves_at', 'created_at', 'updated_at')->whereIn('id', $goods_ids)->get();
            if (!$goods_res->isEmpty()) {
                $ids = $brand_id = $seller_id = $category_id = array();
                foreach ($goods_res as $value) {
                    $ids[] = $value['id'];
                    $brand_id[] = $value['brand_id'];
                    $seller_id[] = $value['seller_id'];
                    $category_id[] = $value['category_id'];
                }
                $ids = array_unique($ids);
                $brand = Brand::whereIn('id', array_unique($brand_id))->pluck('title', 'id');
                $seller = Seller::whereIn('id', array_unique($seller_id))->pluck('title', 'id');
                $category = Category::whereIn('id', array_unique($category_id))->pluck('title', 'id');
                $goods_num = GoodsNum::select('goods_id', 'favorite', 'sale')->whereIn('goods_id', $ids)->get();
                if (!$goods_num->isEmpty()) {
                    $goods_num = array_column($goods_num->toArray(), null, 'goods_id');
                }
                $seller_category = GoodsSellerCategory::select('goods_id', 'category_id')->whereIn('goods_id', $ids)->get();
                if (!$seller_category->isEmpty()) {
                    $seller_category = array_column($seller_category->toArray(), null, 'goods_id');
                }
                //子商品
                $sku = GoodsSku::select('goods_id', 'spec_value', 'stock')->where('status', GoodsSku::STATUS_ON)->whereIn('goods_id', $ids)->get();
                if (!$sku->isEmpty()) {
                    $stock = array();
                    $spec_alias = array();
                    foreach ($sku as $value) {

                        $_stock = (isset($stock[$value['goods_id']]) ? $stock[$value['goods_id']] : 0) + $value['stock'];
                        $stock[$value['goods_id']] = $_stock;
                        $spec_value = json_decode($value['spec_value'], true);
                        if ($spec_value) {
                            foreach ($spec_value as $val) {
                                $spec_alias[$value['goods_id']][] = $val['alias'];
                            }
                        }
                    }
                }

                //属性
                $goods_attribute = GoodsAttribute::select('goods_id', 'attribute_id', 'value')->whereIn('goods_id', $ids)->get();
                if (!$goods_attribute->isEmpty()) {
                    $goods_attr = array();
                    foreach ($goods_attribute as $value) {
                        $goods_attr[$value['goods_id']][$value['attribute_id']][] = $value['value'];
                    }
                }
                $params = ['body' => []];
                foreach ($goods_res as $goods) {
                    $_spec_alias = isset($spec_alias[$goods['id']]) ? $spec_alias[$goods['id']] : [];
                    $_item = array(
                        'id' => $goods['id'],
                        'title' => $goods['title'],
                        'subtitle' => $goods['subtitle'],
                        'image' => $goods['image'],
                        'sell_price' => $goods['sell_price'],
                        'market_price' => $goods['market_price'],
                        'is_rem' => $goods['is_rem'],
                        'category_id' => $goods['category_id'],
                        'category_title' => isset($category[$goods['category_id']]['title']) ? $category[$goods['category_id']]['title'] : '',
                        'brand_id' => $goods['brand_id'],
                        'brand_title' => isset($brand[$goods['brand_id']]['title']) ? $brand[$goods['brand_id']]['title'] : '',
                        'seller_id' => $goods['seller_id'],
                        'seller_title' => isset($seller[$goods['seller_id']]['title']) ? $seller[$goods['seller_id']]['title'] : '',
                        'status' => $goods['status'],
                        'shelves_status' => $goods['shelves_status'],
                        'position' => $goods['position'],
                        'shelves_at' => get_date_time($goods['shelves_at'], true),
                        'created_at' => get_date_time($goods['created_at'], true),
                        'updated_at' => get_date_time($goods['updated_at'], true),
                        'favorite' => isset($goods_num[$goods['id']]['favorite']) ? $goods_num[$goods['id']]['favorite'] : 0,
                        'sale' => isset($goods_num[$goods['id']]['sale']) ? $goods_num[$goods['id']]['sale'] : 0,
                        'seller_category_id' => isset($seller_category[$goods['id']]) ? $seller_category[$goods['id']] : [],
                        'stock' => isset($stock[$goods['id']]) ? $stock[$goods['id']] : 0,
                        'spec_alias' => array_values(array_unique($_spec_alias)),
                        'attribute' => isset($goods_attr[$goods['id']]) ? $goods_attr[$goods['id']] : []
                    );
                    $params['body'][] = [
                        'index' => [
                            '_index' => self::ES_INDEX,
                            '_type' => self::ES_TYPE,
                            '_id' => $goods['id']
                        ]
                    ];
                    $params['body'][] = $_item;
                }
                $response = $this->client->bulk($params);
            }
        }
    }

    /**
     * 删除文档
     * @param $goods_ids
     */
    public function delDoc($goods_ids)
    {
        $goods_ids = format_number($goods_ids, true);
        foreach ($goods_ids as $id) {
            $params = [
                'index' => self::ES_INDEX,
                'type' => self::ES_TYPE,
                'id' => $id
            ];
            $response = $this->client->delete($params);
        }

    }

    /**
     * 搜索文档
     * @param array $where_data 搜索条件
     * @param int $limit 查询条数
     * @param int $offset 偏移量
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function search($where_data = array(), $limit = 20, $offset = 0)
    {
        $search_query = array();
        $search_query['query']['bool']['filter']['bool']['must'][]['term']['status'] = Goods::STATUS_ON;
        $search_query['query']['bool']['filter']['bool']['must'][]['term']['shelves_status'] = Goods::SHELVES_STATUS_ON;
        //关键字
        if (isset($where_data['keyword']) && $where_data['keyword']) {
            $search_query['query']['bool']['must']['multi_match'] = [
                'query' => $where_data['keyword'],
                'type' => 'best_fields',
                'fields' => ['title', 'seller_title']
            ];
        }
        $where_data_arr = ['category_id', 'seller_id', 'brand_id'];
        foreach ($where_data_arr as $type_id) {
            if ($where_data[$type_id]) {
                if (is_array($where_data[$type_id])) {
                    $search_query['query']['bool']['filter']['bool']['must'][]['terms'][$type_id] = $where_data[$type_id];
                } else {
                    $search_query['query']['bool']['filter']['bool']['must'][]['term'][$type_id] = $where_data[$type_id];
                }
            }
        }
        //最小价格
        if ($where_data['min_price']) {
            $search_query['query']['bool']['filter']['bool']['must'][]['range']['sell_price']['gte'] = $where_data['min_price'];
        }
        //最大价格
        if ($where_data['max_price']) {
            $search_query['query']['bool']['filter']['bool']['must'][]['range']['sell_price']['lte'] = $where_data['max_price'];
        }
        //商家分类
        if ($where_data['seller_category_id']) {
            if (is_array($where_data['brand_id'])) {
                $search_query['query']['bool']['filter']['bool']['must'][]['terms']['seller_category_id'] = $where_data['seller_category_id'];
            } else {
                $search_query['query']['bool']['filter']['bool']['must'][]['term']['seller_category_id'] = $where_data['seller_category_id'];
            }
        }

        //属性搜索
        if ($where_data['attribute']) {
            foreach ($where_data['attribute'] as $attr_id => $attr_value) {
                $search_query['query']['bool']['filter']['bool']['must'][]['terms']['attribute.' . $attr_id] = $attr_value;
            }
        }

        //排序
        if ($where_data['orderby']) {
            foreach ($where_data['orderby'] as $key => $value) {
                $search_query['sort'][][$key]['order'] = $value;
            }
        }
        $search_query['sort'][]['_score']['order'] = 'desc';

        //分页
        $search_query['from'] = $offset;
        $search_query['size'] = $limit;
        $params = [
            'index' => self::ES_INDEX,
            'type' => self::ES_TYPE,
            'body' => $search_query
        ];
        $results = $this->client->search($params);

        $goods_data = $this->getResult($results);

        return [$goods_data, $this->getTotal($results)];
    }
}