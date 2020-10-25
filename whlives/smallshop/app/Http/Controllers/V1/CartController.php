<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Cart;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    /**
     * 购物车商品列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $m_id = $this->getUserId();
        $cart = Cart::where('m_id', $m_id)->orderBy('updated_at', 'desc')->pluck('buy_qty', 'sku_id');
        if ($cart->isEmpty()) {
            api_error(__('api.cart_goods_not_exists'));
        }
        $seller_goods = GoodsService::formatCart($cart->toArray());
        return $this->success($seller_goods);
    }

    /**
     * 添加商品
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function add(Request $request)
    {
        $m_id = $this->getUserId();
        $sku_id = (int)$request->post('sku_id');
        $buy_qty = (int)$request->post('buy_qty', 1);
        if (!$sku_id || !$buy_qty) {
            api_error(__('api.missing_params'));
        }

        //查询商品是否正常
        $goods_sku = GoodsSku::find($sku_id);
        if (!$goods_sku) {
            api_error(__('api.goods_sku_error'));
        } elseif ($goods_sku['status'] != GoodsSku::STATUS_ON) {
            api_error(__('api.goods_sku_status_error'));
        } elseif ($goods_sku['min_buy'] > $buy_qty) {
            api_error(__('api.goods_min_buy_qty_error') . $goods_sku['min_buy'] . '件');
        } elseif ($goods_sku['stock'] < $buy_qty) {
            api_error(__('api.goods_stock_no_enough') . '最多能订购' . $goods_sku['stock'] . '件');
        }
        $goods = Goods::find($goods_sku['goods_id']);
        if (!$goods) {
            api_error(__('api.goods_error'));
        } elseif ($goods['shelves_status'] != Goods::SHELVES_STATUS_ON) {
            api_error(__('api.goods_shelves_status_error'));
        }

        //查询是否已经加入
        $cart = Cart::where(['m_id' => $m_id, 'sku_id' => $sku_id])->first();
        if ($cart) {
            //已经存在直接修改数量
            $buy_qty = $cart['buy_qty'] + $buy_qty;
            if ($goods_sku['min_buy'] > $buy_qty) {
                api_error(__('api.goods_min_buy_qty_error') . $goods_sku['min_buy'] . '件');
            }
            $res = Cart::where('id', $cart['id'])->update(['buy_qty' => $buy_qty]);
        } else {
            $cart_data = array(
                'm_id' => $m_id,
                'seller_id' => $goods['seller_id'],
                'goods_id' => $goods_sku['goods_id'],
                'sku_id' => $sku_id,
                'buy_qty' => $buy_qty,
            );
            $res = Cart::create($cart_data);
        }
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 修改商品
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function edit(Request $request)
    {
        $m_id = $this->getUserId();
        $sku_id = (int)$request->post('sku_id');
        $buy_qty = (int)$request->post('buy_qty', 1);
        if (!$sku_id || !$buy_qty) {
            api_error(__('api.missing_params'));
        }

        //查询是否已经加入
        $cart = Cart::where(['m_id' => $m_id, 'sku_id' => $sku_id])->first();
        if (!$cart) {
            api_error(__('api.cart_goods_error'));
        }

        //查询商品是否正常
        $goods_sku = GoodsSku::find($sku_id);
        if (!$goods_sku) {
            api_error(__('api.goods_sku_error'));
        } elseif ($goods_sku['status'] != GoodsSku::STATUS_ON) {
            api_error(__('api.goods_sku_status_error'));
        } elseif ($goods_sku['min_buy'] > $buy_qty) {
            api_error(__('api.goods_min_buy_qty_error') . $goods_sku['min_buy'] . '件');
        } elseif ($goods_sku['stock'] < $buy_qty) {
            api_error(__('api.goods_stock_no_enough') . '最多能订购' . $goods_sku['stock'] . '件');
        }
        $goods = Goods::find($goods_sku['goods_id']);
        if (!$goods) {
            api_error(__('api.goods_error'));
        } elseif ($goods['shelves_status'] != Goods::SHELVES_STATUS_ON) {
            api_error(__('api.goods_shelves_status_error'));
        }

        $res = Cart::where('id', $cart['id'])->update(['buy_qty' => $buy_qty]);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 删除商品
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function delete(Request $request)
    {
        $m_id = $this->getUserId();
        $sku_id = $request->post('sku_id');
        if (!$sku_id) {
            api_error(__('api.missing_params'));
        }
        $sku_id = format_number($sku_id);
        if (!$sku_id) {
            api_error(__('api.missing_params'));
        }
        if (is_array($sku_id)) {
            $res = Cart::where('m_id', $m_id)->whereIn('sku_id', $sku_id)->delete();
        } else {
            $res = Cart::where(['m_id' => $m_id, 'sku_id' => $sku_id])->delete();
        }

        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 清空
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function clear(Request $request)
    {
        $m_id = $this->getUserId();
        $res = Cart::where('m_id', $m_id)->delete();
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }
}
