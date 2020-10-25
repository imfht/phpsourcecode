<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Seller;

use App\Models\MenuSeller;
use App\Models\Order;
use App\Models\Refund;
use App\Models\SellerBalance;

class IndexController extends BaseController
{

    /**
     * 后台右侧首页
     */
    public function main()
    {
        $seller_id = $this->getUserId();
        $order_total = Order::where('seller_id', $seller_id)->count();
        $order_pay_total = Order::where(['seller_id' => $seller_id, 'status' => Order::STATUS_PAID])->count();
        $refund_total = Refund::where(['seller_id' => $seller_id, 'status' => Refund::STATUS_WAIT_APPROVE])->count();
        $balance = SellerBalance::where('m_id', $seller_id)->value('amount');
        $data = array(
            'order_total' => $order_total,
            'order_pay_total' => $order_pay_total,
            'refund_total' => $refund_total,
            'balance' => $balance
        );
        return $this->success($data);
    }

    /**
     * 获取管理菜单
     * @param Request $request
     * @return array|void
     * @throws \App\Exceptions\ApiException
     */
    public function leftMenu()
    {
        $menus = MenuSeller::getMenu();
        return $this->success($menus);
    }
}
