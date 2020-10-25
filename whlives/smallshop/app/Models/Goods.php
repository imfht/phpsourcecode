<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * 商品
 * Class Goods
 * @package App\Models
 */
class Goods extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '待审核',
        self::STATUS_ON => '已审核',
    ];

    //上架状态
    const SHELVES_STATUS_OFF = 0;
    const SHELVES_STATUS_ON = 1;

    const SHELVES_STATUS_DESC = [
        self::SHELVES_STATUS_OFF => '下架',
        self::SHELVES_STATUS_ON => '上架',
    ];

    //是否推荐
    const REM_OFF = 0;
    const REM_ON = 1;

    const REM_DESC = [
        self::REM_ON => '推荐',
        self::REM_OFF => '不推荐'
    ];

    //商品类型
    const TYPE_GOODS = 1;
    const TYPE_POINT = 2;

    const TYPE_DESC = [
        self::TYPE_GOODS => '普通商品',
        self::TYPE_POINT => '积分商品'
    ];

    protected $table = 'goods';
    protected $guarded = ['id'];

    /**
     * 获取详情
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function content()
    {
        return $this->hasOne('App\Models\GoodsContent');
    }

    /**
     * 获取计数信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function num()
    {
        return $this->hasOne('App\Models\GoodsNum');
    }

    /**
     * 获取商品图片
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function image()
    {
        return $this->hasMany('App\Models\GoodsImage');
    }

    /**
     * 获取商品属性
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attribute()
    {
        return $this->hasMany('App\Models\GoodsAttribute');
    }

    /**
     * 获取子商品
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsSku()
    {
        return $this->hasMany('App\Models\GoodsSku');
    }

    /**
     * 获取商家分类
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sellerCategory()
    {
        return $this->hasMany('App\Models\GoodsSellerCategory');
    }

    /**
     * 保存数据
     * @param $goods_data
     * @param string $id
     * @return bool
     */
    public static function saveData($goods_data, $id = '')
    {
        try {
            $res = DB::transaction(function () use ($goods_data, $id) {
                //修改主商品
                if ($id) {
                    self::where('id', $id)->update($goods_data['goods']);
                    GoodsContent::where('goods_id', $id)->update(['content' => $goods_data['content']]);
                    GoodsImage::where('goods_id', $id)->delete();
                    GoodsAttribute::where('goods_id', $id)->delete();
                    GoodsSku::where('goods_id', $id)->update(['status' => GoodsSku::STATUS_DEL]);
                } else {
                    $result = self::create($goods_data['goods']);
                    $id = $result->id;
                    GoodsContent::create(['goods_id' => $id, 'content' => $goods_data['content']]);
                    //商品数量相关
                    GoodsNum::create(['goods_id' => $id]);
                }

                //商品图片
                if (isset($goods_data['goods_image']) && $goods_data['goods_image']) {
                    $goods_image = array();
                    foreach ($goods_data['goods_image'] as $value) {
                        $_item = array(
                            'goods_id' => $id,
                            'image' => $value
                        );
                        $goods_image[] = $_item;
                    }
                    GoodsImage::insert($goods_image);
                }
                //sku商品
                if (isset($goods_data['goods_sku']) && $goods_data['goods_sku']) {
                    foreach ($goods_data['goods_sku'] as $value) {
                        $sku_id = $value['sku_id'];
                        unset($value['sku_id']);
                        $value['goods_id'] = $id;
                        $value['status'] = GoodsSku::STATUS_ON;
                        if ($sku_id) {
                            GoodsSku::where('id', $sku_id)->update($value);
                        } else {
                            GoodsSku::create($value);
                        }
                    }
                }
                //商品属性
                if (isset($goods_data['attribute']) && $goods_data['attribute']) {
                    $goods_attr = $goods_data['attribute'];
                    foreach ($goods_attr as $key => $value) {
                        $value['goods_id'] = $id;
                        $goods_attr[$key] = $value;
                    }
                    GoodsAttribute::insert($goods_attr);
                }
                //商家分类
                if (isset($goods_data['seller_category'])) {
                    GoodsSellerCategory::where('goods_id', $id)->delete();
                    if ($goods_data['seller_category']) {
                        $seller_category = array();
                        foreach ($goods_data['seller_category'] as $value) {
                            $seller_category[] = array(
                                'goods_id' => $id,
                                'category_id' => $value
                            );
                        }
                        GoodsSellerCategory::insert($seller_category);
                    }
                }
                return $id;
            });
            return $res;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 组装商品数据
     */
    public static function getGoodsData($request, $is_seller = false)
    {
        $save_data = array();
        $image = $request->input('image');
        $spec_sku_id = $request->input('spec_sku_id');
        $spec_market_price = $request->input('spec_market_price');
        $spec_sell_price = $request->input('spec_sell_price');
        $spec_stock = $request->input('spec_stock');
        $spec_sku_code = $request->input('spec_sku_code');
        $spec_weight = $request->input('spec_weight');
        $spec_min_buy = $request->input('spec_min_buy');

        //规格信息
        $spec_id = $request->input('spec_id');
        $spec_name = $request->input('spec_name');
        $spec_value = $request->input('spec_value');
        $spec_image = $request->input('spec_image');
        $spec_alias = $request->input('spec_alias');

        //主商品信息
        $goods = array(
            'image' => current($image),
            'market_price' => min($spec_market_price),
            'sell_price' => min($spec_sell_price),
        );
        foreach ($request->only(['title', 'subtitle', 'sku_code', 'category_id', 'delivery_id', 'seller_id', 'brand_id', 'position']) as $key => $value) {
            $goods[$key] = ($value || $value == 0) ? $value : null;
        }

        //组装子商品信息
        $goods_sku = array();
        foreach ($spec_market_price as $key => $value) {
            $sku_spec_value = array();
            $sku_spec_image = current($image);
            if (isset($spec_id[$key])) {
                foreach ($spec_id[$key] as $k => $v) {
                    $_sku_value = array(
                        'id' => $spec_id[$key][$k],
                        'name' => $spec_name[$key][$k],
                        'value' => $spec_value[$key][$k],
                        'image' => $spec_image[$key][$k],
                        'alias' => $spec_alias[$key][$k],
                    );
                    $sku_spec_value[] = $_sku_value;
                    if (isset($spec_image[$key][$k]) && $spec_image[$key][$k]) {
                        $sku_spec_image = $spec_image[$key][$k];
                    }
                }
            }
            $_sku_item = array(
                'sku_id' => isset($spec_sku_id[$key]) ? $spec_sku_id[$key] : '',
                'market_price' => $spec_market_price[$key],
                'sell_price' => $spec_sell_price[$key],
                'sku_code' => $spec_sku_code[$key],
                'stock' => $spec_stock[$key],
                'weight' => $spec_weight[$key],
                'min_buy' => $spec_min_buy[$key],
                'spec_value' => json_encode($sku_spec_value, JSON_UNESCAPED_UNICODE),
                'image' => $sku_spec_image
            );
            $goods_sku[] = $_sku_item;
        }

        //属性信息
        $goods_attribute = array();
        $attribute = $request->input('attribute');
        if ($attribute) {
            foreach ($attribute as $attribute_id => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $_item = array(
                            'attribute_id' => $attribute_id,
                            'value' => $v
                        );
                        $goods_attribute[] = $_item;
                    }
                } else {
                    $_item = array(
                        'attribute_id' => $attribute_id,
                        'value' => $value
                    );
                    $goods_attribute[] = $_item;
                }
            }
        }
        //店铺分类
        $seller_category = $request->input('seller_category');
        if ($is_seller) {
            $save_data['seller_category'] = array();
            if ($seller_category) {
                $save_data['seller_category'] = format_number($seller_category, true);
            }
        }

        $save_data['goods'] = $goods;
        $save_data['goods_sku'] = $goods_sku;
        $save_data['goods_image'] = $image;//图片信息
        $save_data['content'] = $request->input('content');
        $save_data['attribute'] = $goods_attribute;
        return $save_data;
    }

}
