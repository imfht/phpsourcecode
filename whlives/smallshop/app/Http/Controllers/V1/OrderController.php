<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Member;
use App\Models\Order;
use App\Models\Payment;
use App\Services\GoodsService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    /**
     * 订单金额计算
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getPrice(Request $request)
    {
        $m_id = $this->getUserId();
        $type = (int)$request->post('type');
        $sku_id = $request->post('sku_id');
        $buy_qty = (int)$request->post('buy_qty');
        $address_id = (int)$request->post('address_id');
        $goods_id = (int)$request->post('goods_id');
        if (!$type || !$sku_id || !$address_id) {
            api_error(__('api.missing_params'));
        }
        //格式化优惠券配送备注发票等信息
        list($coupons, $delivery, $note, $invoice) = GoodsService::formatParams();
        $cart = self::getCart($type, $sku_id, $buy_qty);
        if (!$cart) {
            api_error(__('api.goods_error'));
        }
        //验证商品是否满足秒杀
        if ($type == Cart::TYPE_SECKILL) {
            GoodsService::checkMarketSeckill($goods_id, $sku_id, $buy_qty);
        }
        //验证地址是否存在
        $prov_id = 0;
        $address = Address::where(['m_id' => $m_id, 'id' => $address_id])->first();
        if ($address) {
            $prov_id = $address['prov_id'];
        } else {
            api_error(__('api.address_not_exists'));
        }
        $seller_goods = GoodsService::formatSellerGoods($cart, $type);
        $seller_goods = GoodsService::getOrderPrice($seller_goods, $prov_id, $coupons);//获取商品信息
        $price = GoodsService::sumOrderPrice($seller_goods);//组装价格信息
        return $this->success($price);
    }

    /**
     * 确认订单
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function confirm(Request $request)
    {
        $m_id = $this->getUserId();
        $type = (int)$request->post('type');
        $sku_id = $request->post('sku_id');
        $buy_qty = (int)$request->post('buy_qty');
        $goods_id = (int)$request->post('goods_id');
        $address_id = (int)$request->post('address_id');
        if (!$type || !$sku_id) {
            api_error(__('api.missing_params'));
        }
        $cart = self::getCart($type, $sku_id, $buy_qty);
        if (!$cart) {
            api_error(__('api.goods_error'));
        }
        //验证商品是否满足秒杀
        if ($type == Cart::TYPE_SECKILL) {
            GoodsService::checkMarketSeckill($goods_id, $sku_id, $buy_qty);
        }
        //验证地址是否存在
        $prov_id = 0;
        $address = array();
        if (!$address_id) {
            //没有收货地址查询默认地址
            $res_address = Address::where(['m_id' => $m_id])->orderBy('default', 'desc')->orderBy('id', 'desc')->first();
        } else {
            $res_address = Address::where(['m_id' => $m_id, 'id' => $address_id])->first();
        }
        if ($res_address) {
            $prov_id = $res_address['prov_id'];
            $address = array(
                'id' => $res_address['id'],
                'full_name' => $res_address['full_name'],
                'tel' => $res_address['tel'],
                'address' => $res_address['prov_name'] . $res_address['city_name'] . $res_address['area_name'] . $res_address['address']
            );
        }
        $seller_goods = GoodsService::formatSellerGoods($cart, $type);
        $seller_goods = GoodsService::getConfirm($seller_goods, $prov_id);
        $order_price = GoodsService::sumOrderPrice($seller_goods);//组装价格信息
        $return = array(
            'address' => $address,
            'seller_goods' => $seller_goods,
            'order_price' => $order_price,
            'payment' => Payment::getPayment()
        );
        return $this->success($return);
    }

    /**
     * 提交订单
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function submit(Request $request)
    {
        $m_id = $this->getUserId();
        $type = (int)$request->post('type');
        $sku_id = $request->post('sku_id');
        $buy_qty = (int)$request->post('buy_qty');
        $goods_id = (int)$request->post('goods_id');
        $address_id = (int)$request->post('address_id');
        if (!$type || !$sku_id || !$address_id) {
            api_error(__('api.missing_params'));
        }
        //格式化优惠券配送备注发票等信息
        list($coupons, $delivery, $note, $invoice) = GoodsService::formatParams();

        $cart = self::getCart($type, $sku_id, $buy_qty);
        if (!$cart) {
            api_error(__('api.goods_error'));
        }
        //验证地址是否存在
        $prov_id = 0;
        $address = Address::where(['m_id' => $m_id, 'id' => $address_id])->first();
        if (!$address) {
            api_error(__('api.address_not_exists'));
        }
        //验证商品是否满足秒杀
        if ($type == Cart::TYPE_SECKILL) {
            GoodsService::checkMarketSeckill($goods_id, $sku_id, $buy_qty, true);
        }
        $seller_goods = GoodsService::formatSellerGoods($cart, $type);
        $seller_goods = GoodsService::getOrderPrice($seller_goods, $address['prov_id'], $coupons);//获取商品信息
        //判断是否存在不能送达的商品
        foreach ($seller_goods as $value) {
            if ($value['delivery']['sku_ids']) {
                //秒杀时如果存在不能送达的商品，还原库存
                OrderService::seckillStockIncr($goods_id, $sku_id, $buy_qty);
                api_error(__('api.goods_can_not_delivery'));
            }
        }
        //检测发票信息
        $invoice = GoodsService::checkInvoice($seller_goods, $invoice);
        //三级分销推荐人
        $level_one_m_id = 0;
        $level_two_m_id = Member::where('id', $m_id)->value('parent_id');
        if ($level_two_m_id) {
            $level_one_m_id = Member::where('id', $level_two_m_id)->value('parent_id');
        }
        //开始组装订单信息
        $order_info = array();
        $order_no_arr = array();
        foreach ($seller_goods as $value) {
            $seller_id = $value['seller']['id'];
            $order_no = OrderService::getOrderNo();
            $order_no_arr[] = $order_no;
            $_order = array(
                'm_id' => $m_id,
                'seller_id' => $seller_id,
                'order_no' => $order_no,
                'status' => Order::STATUS_WAIT_PAY,
                'market_type' => ($type == Cart::TYPE_SECKILL) ? Order::MARKET_TYPE_SECKILL : Order::MARKET_TYPE_DEFAULT,
                'product_num' => $value['all_buy_qty'],
                'sell_price_total' => $value['price']['sell_price'],
                'market_price_total' => $value['price']['market_price'],
                'delivery_type' => isset($delivery[$seller_id]['delivery_type']) ? $delivery[$seller_id]['delivery_type'] : 1,
                'delivery_time' => isset($delivery[$seller_id]['delivery_time']) ? $delivery[$seller_id]['delivery_time'] : '',
                'delivery_price' => $value['delivery']['delivery_price'],
                'delivery_price_real' => $value['delivery']['delivery_price_real'],
                'promotion_price' => $value['price']['promotion_price'],
                'promotion_text' => isset($value['promotion']) ? json_encode($value['promotion'], JSON_UNESCAPED_UNICODE) : '',
                'subtotal' => $value['price']['subtotal'],
                'coupons_id' => isset($value['coupons_id']) ? $value['coupons_id'] : 0,
                'platform' => get_platform(),
                'full_name' => $address['full_name'],
                'tel' => $address['tel'],
                'prov' => $address['prov_name'],
                'city' => $address['city_name'],
                'area' => $address['area_name'],
                'address' => $address['address'],
                'note' => isset($note[$seller_id]) ? $note[$seller_id] : '',
                'level_one_m_id' => $level_one_m_id,
                'level_two_m_id' => $level_two_m_id
            );
            if ($level_one_m_id || $level_two_m_id) {
                $_order['is_rem'] = Order::REM_ON;
            }

            //发票信息
            if ($invoice) {
                $_order['invoice'] = array(
                    'type' => $invoice[$seller_id]['type'],
                    'title' => $invoice[$seller_id]['title'],
                    'tax_no' => isset($invoice[$seller_id]['tax_no']) ? $invoice[$seller_id]['tax_no'] : ''
                );
            }

            $order_goods = array();
            foreach ($value['goods'] as $goods) {
                $_order_goods = array(
                    'm_id' => $m_id,
                    'goods_id' => $goods['goods_id'],
                    'goods_title' => $goods['title'],
                    'sku_id' => $goods['sku_id'],
                    'sku_code' => $goods['sku_code'],
                    'image' => $goods['image'],
                    'sell_price' => $goods['show_price'],
                    'market_price' => $goods['line_price'],
                    'promotion_price' => $goods['promotion_price'],
                    'buy_qty' => $goods['buy_qty'],
                    'weight' => $goods['weight'] * $goods['buy_qty'],
                    'spec_value' => $goods['spec_value'],
                    'seller_id' => $seller_id,
                    'level_one_pct' => $goods['level_one_pct'],
                    'level_two_pct' => $goods['level_two_pct']
                );
                $order_goods[] = $_order_goods;
            }
            $_order['goods'] = $order_goods;
            $order_info[] = $_order;
        }
        $res = Order::submitOrder($order_info);
        if ($res) {
            //减少商品库存
            $is_special = ($type == Cart::TYPE_SECKILL) ? true : false;
            OrderService::stockDecr($cart, $is_special);
            //删除购物车商品
            self::delCart($type, $sku_id);

            $order_no_arr = join(',', $order_no_arr);
            $return = array(
                'order_no' => trim($order_no_arr, ',')
            );
            return $this->success($return);
        } else {
            api_error(__('api.order_submit_fail'));
        }
    }

    /**
     * 组装商品购买信息
     * @param $type
     * @param $sku_id
     * @param $buy_qty
     * @throws \App\Exceptions\ApiException
     */
    private function getCart($type, $sku_id, $buy_qty)
    {
        $m_id = $this->getUserId();
        //购物车提交
        $cart = array();
        if ($type == Cart::TYPE_CART) {
            $sku_id = format_number($sku_id, true);
            if (!$sku_id) {
                api_error(__('api.missing_params'));
            }
            $cart = Cart::where('m_id', $m_id)->whereIn('sku_id', $sku_id)->orderBy('updated_at', 'desc')->pluck('buy_qty', 'sku_id');
            $cart = $cart->toArray();
            if (!$cart) {
                api_error(__('api.cart_goods_error'));
            }
        } elseif ($type == Cart::TYPE_NOW || $type == Cart::TYPE_SECKILL) {
            //直接购买
            $sku_id = (int)$sku_id;
            if (!$sku_id) {
                api_error(__('api.goods_error'));
            }
            if ($buy_qty < 1) {
                api_error(__('api.buy_qty_error'));
            }
            $cart = array($sku_id => $buy_qty);
        }
        return $cart;
    }

    /**
     * 删除购物车商品
     * @param $type
     * @param $sku_id
     * @param $buy_qty
     * @throws \App\Exceptions\ApiException
     */
    private function delCart($type, $sku_id)
    {
        $m_id = $this->getUserId();
        //购物车提交
        if ($type == Cart::TYPE_CART) {
            $sku_id = format_number($sku_id, true);
            if (is_array($sku_id)) {
                Cart::where('m_id', $m_id)->whereIn('sku_id', $sku_id)->delete();
            } else {
                Cart::where(['m_id' => $m_id, 'sku_id' => $sku_id])->delete();
            }
        }
    }
}
