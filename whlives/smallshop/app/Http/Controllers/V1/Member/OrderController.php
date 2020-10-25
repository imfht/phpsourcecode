<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\DeliveryTraces;
use App\Models\Evaluation;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderGoods;
use App\Models\Seller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseController
{

    /**
     * 订单列表
     * @param Request $request
     */
    public function index(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $type = $request->post('type');
        $where = [
            'm_id' => $m_id
        ];
        if (isset(Order::STATUS_DESC[$type])) {
            if ($type == Order::STATUS_CANNEL) {
                $where_in = array(Order::STATUS_CANNEL, Order::STATUS_SYSTEM_CANNEL);
            } else {
                $where['status'] = $type;
            }
        }
        $query = Order::select('id', 'order_no', 'seller_id', 'product_num', 'subtotal', 'status', 'delivery_price_real')
            ->where($where);
        if (isset($where_in)) {
            $query->whereIn('status', $where_in);
        }
        $res_list = $query->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $order_ids = $seller_ids = array();
        foreach ($res_list as $value) {
            $order_ids[] = $value['id'];
            $seller_ids[] = $value['seller_id'];
        }
        $order_goods_res = OrderGoods::select('order_id', 'goods_id', 'goods_title', 'image', 'sell_price', 'buy_qty', 'spec_value', 'refund')->whereIn('order_id', $order_ids)->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.order_goods_error'));
        }
        $order_goods = array();
        foreach ($order_goods_res as $value) {
            $_order_id = $value['order_id'];
            unset($value['order_id']);
            $value['refund_text'] = OrderGoods::REFUND_DESC[$value['refund']];
            $order_goods[$_order_id][] = $value;
        }
        $seller_res = Seller::select('id', 'title', 'image')->whereIn('id', $seller_ids)->get();
        if ($seller_res->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $seller_res = array_column($seller_res->toArray(), null, 'id');

        $data_list = array();
        foreach ($res_list as $value) {
            $_item = array(
                'order_no' => $value['order_no'],
                'seller' => isset($seller_res[$value['seller_id']]) ? $seller_res[$value['seller_id']] : [],
                'goods' => isset($order_goods[$value['id']]) ? $order_goods[$value['id']] : [],
                'product_num' => $value['product_num'],
                'subtotal' => $value['subtotal'],
                'status' => $value['status'],
                'status_text' => Order::STATUS_MEMBER_DESC[$value['status']],
                'button' => OrderService::orderButton($value['status'])
            );
            $data_list[] = $_item;
        }
        $total = Order::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 订单详情
     * @param Request $request
     */
    public function detail(Request $request)
    {
        $m_id = $this->getUserId();
        $order_no = $request->post('order_no');
        if (!$order_no) {
            api_error(__('api.missing_params'));
        }
        $where = [
            'm_id' => $m_id,
            'order_no' => $order_no
        ];
        $order_res = Order::select('id', 'full_name', 'prov', 'city', 'area', 'tel', 'address', 'status', 'order_no', 'sell_price_total', 'delivery_price_real', 'discount_price', 'promotion_price', 'subtotal', 'note', 'created_at as create_at', 'pay_at', 'send_at', 'done_at', 'seller_id')->where($where)->first();
        if (!$order_res) {
            api_error(__('api.order_error'));
        }

        //查询订单商品
        $order_goods_res = OrderGoods::select('goods_id', 'goods_title', 'image', 'sell_price', 'buy_qty', 'spec_value', 'refund')->where('order_id', $order_res['id'])->get();
        if ($order_goods_res->isEmpty()) {
            api_error(__('api.order_goods_error'));
        }

        $order_goods = array();
        foreach ($order_goods_res->toArray() as $value) {
            $_item = $value;
            $_item['refund_text'] = OrderGoods::REFUND_DESC[$value['refund']];
            $order_goods[] = $_item;
        }
        $seller = Seller::select('id', 'title', 'image')->where('id', $order_res['seller_id'])->first();

        $order = array(
            'full_name' => $order_res['full_name'],
            'tel' => $order_res['tel'],
            'address' => $order_res['prov'] . $order_res['city'] . $order_res['area'] . $order_res['address'],
            'status' => $order_res['status'],
            'status_text' => Order::STATUS_MEMBER_DESC[$order_res['status']],
            'order_no' => $order_res['order_no'],
            'sell_price_total' => $order_res['sell_price_total'],
            'delivery_price_real' => $order_res['delivery_price_real'],
            'discount_price' => $order_res['discount_price'],
            'promotion_price' => $order_res['promotion_price'],
            'subtotal' => $order_res['subtotal'],
            'note' => $order_res['note'],
            'create_at' => $order_res['create_at'],
            'pay_at' => $order_res['pay_at'],
            'send_at' => $order_res['send_at'],
            'done_at' => $order_res['done_at'],
        );

        $return = array(
            'order' => $order,
            'goods' => $order_goods,
            'seller' => $seller,
            'button' => OrderService::orderButton($order_res['status'], true)
        );
        return $this->success($return);
    }

    /**
     * 订单取消
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function cancel(Request $request)
    {
        $m_id = $this->getUserId();
        $order_no = $request->post('order_no');
        if (!$order_no) {
            api_error(__('api.missing_params'));
        }
        $order = Order::where(['m_id' => $m_id, 'order_no' => $order_no])->first();
        if (!$order) {
            api_error(__('api.order_error'));
        }

        //开始取消
        $userinfo = $this->getUserInfo();
        $res = OrderService::cancel($order, $userinfo);
        if ($res === true) {
            return $this->success(true);
        } elseif ($res === false) {
            api_error(__('api.fail'));
        } else {
            api_error($res);
        }
    }

    /**
     * 订单确认
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function confirm(Request $request)
    {
        $m_id = $this->getUserId();
        $order_no = $request->post('order_no');
        if (!$order_no) {
            api_error(__('api.missing_params'));
        }
        $order = Order::where(['m_id' => $m_id, 'order_no' => $order_no])->first();
        if (!$order) {
            api_error(__('api.order_error'));
        }

        //开始确认
        $userinfo = $this->getUserInfo();
        $res = OrderService::confirm($order, $userinfo);
        if ($res === true) {
            return $this->success(true);
        } elseif ($res === false) {
            api_error(__('api.fail'));
        } else {
            api_error($res);
        }
    }

    /**
     * 物流信息
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function delivery(Request $request)
    {
        $m_id = $this->getUserId();
        $order_no = $request->post('order_no');
        if (!$order_no) {
            api_error(__('api.missing_params'));
        }
        $order = Order::where(['m_id' => $m_id, 'order_no' => $order_no])->first();
        if (!$order) {
            api_error(__('api.order_error'));
        }
        $delivery_traces = array();
        $delivery_res = OrderDelivery::select('company_code', 'company_name', 'code')->where('order_id', $order['id'])->get();
        if (!$delivery_res->isEmpty()) {
            $query = DeliveryTraces::select('company_code', 'code', 'accept_time', 'info');
            foreach ($delivery_res as $value) {
                $delivery_traces[$value['company_code'] . $value['code']] = array(
                    'company_name' => $value['company_name'],
                    'code' => $value['code'],
                    'traces' => array()
                );
                $query->orWhere(function ($query) use ($value) {
                    $query->where(['company_code' => $value['company_code'], 'code' => $value['code']]);
                });
            }
            $traces = $query->orderBy('id', 'asc')->get();

            if (!$traces->isEmpty()) {
                foreach ($traces as $value) {
                    $_item = array(
                        'accept_time' => $value['accept_time'],
                        'info' => $value['info'],
                    );
                    $delivery_traces[$value['company_code'] . $value['code']]['traces'][] = $_item;
                }
            }
        }

        return $this->success(array_values($delivery_traces));
    }

    /**
     * 评价
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function evaluation(Request $request)
    {
        $m_id = $this->getUserId();
        $order_no = $request->post('order_no');
        if (!$order_no) {
            api_error(__('api.missing_params'));
        }
        $order = Order::where(['m_id' => $m_id, 'order_no' => $order_no])->first();
        if (!$order) {
            api_error(__('api.order_error'));
        }
        if (OrderService::isEvaluation($order)) {
            //查询子商品
            $order_goods = OrderGoods::select('id', 'goods_title', 'image')->where('order_id', $order['id'])->get();
            if ($order_goods->isEmpty()) {
                api_error(__('api.content_is_empty'));
            } else {
                return $this->success($order_goods);
            }
        } else {
            api_error(__('api.order_status_error'));
        }
    }

    /**
     * 评价
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function evaluationPut(Request $request)
    {
        $m_id = $this->getUserId();
        $order_no = $request->post('order_no');
        $content = $request->post('content');
        if (!$order_no || !$content) {
            api_error(__('api.missing_params'));
        }
        $content = json_decode($content, true);
        $order = Order::where(['m_id' => $m_id, 'order_no' => $order_no])->first();
        if (!$order) {
            api_error(__('api.order_error'));
        }
        //查询子商品
        $order_goods = OrderGoods::select('id', 'goods_id', 'sku_id', 'spec_value')->where('order_id', $order['id'])->get();
        if ($order_goods->isEmpty()) {
            api_error(__('api.invalid_params'));
        }
        $order_goods = array_column($order_goods->toArray(), null, 'id');
        $order_goods_id = array_keys($order_goods);
        if (count($content) != count($order_goods)) {
            api_error(__('api.missing_params'));
        }
        $evaluation = array();
        foreach ($content as $value) {
            if (in_array($value['id'], $order_goods_id)) {
                $level = isset($value['level']) ? (int)$value['level'] : 5;
                if ($level < 1 || $level > 5) {
                    api_error(__('api.evaluation_level_error'));
                }
                $_item = array(
                    'id' => $value['id'],
                    'm_id' => $this->getUserId(),
                    'goods_id' => $order_goods[$value['id']]['goods_id'],
                    'sku_id' => $order_goods[$value['id']]['sku_id'],
                    'spec_value' => $order_goods[$value['id']]['spec_value'],
                    'level' => $level,
                    'content' => isset($value['content']) ? $value['content'] : '好评',
                    'image' => isset($value['image']) ? explode(',', $value['image']) : [],
                    'is_image' => Evaluation::IS_IMAGE_FALSE
                );
                if ($_item['image']) $_item['is_image'] = Evaluation::IS_IMAGE_TRUE;
                $evaluation[] = $_item;
            }
        }
        if ($evaluation) {
            //修改订单状态
            $order_res = Order::where(['id' => $order['id'], 'status' => Order::STATUS_DONE])->update(['status' => Order::STATUS_COMMENT, 'comment_at' => get_date()]);
            if ($order_res) {
                //保存评论信息
                $res = Evaluation::saveData($evaluation);
                if ($res) {
                    return $this->success(true);
                } elseif ($res === false) {
                    api_error(__('api.fail'));
                }
            } else {
                api_error(__('api.fail'));
            }
        } else {
            api_error(__('api.missing_params'));
        }
    }
}
