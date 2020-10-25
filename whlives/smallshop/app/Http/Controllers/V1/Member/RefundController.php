<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Libs\Kdniao;
use App\Models\ExpressCompany;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Refund;
use App\Models\RefundDelivery;
use App\Models\RefundImage;
use App\Models\RefundLog;
use App\Models\Seller;
use Illuminate\Http\Request;

class RefundController extends BaseController
{

    /**
     * 售后单列表
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

        $res_list = Refund::select('id', 'order_goods_id', 'seller_id', 'refund_type', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $order_goods_ids = $seller_ids = array();
        foreach ($res_list as $value) {
            $order_goods_ids[] = $value['order_goods_id'];
            $seller_ids[] = $value['seller_id'];
        }
        //获取商品信息
        $order_goods_res = OrderGoods::select('id', 'goods_title', 'image', 'buy_qty', 'spec_value')->whereIn('id', $order_goods_ids)->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.order_goods_error'));
        }
        $order_goods_res = array_column($order_goods_res->toArray(), null, 'id');
        //获取商家
        $seller_res = Seller::select('id', 'title', 'image')->whereIn('id', $seller_ids)->get();
        if ($seller_res->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $seller_res = array_column($seller_res->toArray(), null, 'id');

        $data_list = array();
        foreach ($res_list as $value) {
            $_item = array(
                'id' => $value['id'],
                'refund_type_text' => Refund::REFUND_TYPE_DESC[$value['refund_type']],
                'refund_type' => $value['refund_type'],
                'status' => $value['status'],
                'status_text' => Refund::STATUS_MEMBER_DESC[$value['status']],
                'goods' => isset($order_goods_res[$value['order_goods_id']]) ? $order_goods_res[$value['order_goods_id']] : [],
                'seller' => isset($seller_res[$value['seller_id']]) ? $seller_res[$value['seller_id']] : [],
            );
            $data_list[] = $_item;
        }
        $total = Refund::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 退款详情
     * @param Request $request
     */
    public function detail(Request $request)
    {
        $m_id = $this->getUserId();
        $refund_no = (int)$request->post('refund_no');
        if (!$refund_no) {
            api_error(__('api.missing_params'));
        }
        $refund = Refund::select('id', 'order_goods_id', 'status', 'refund_type', 'reason', 'amount', 'delivery_price', 'refund_no', 'created_at as create_at')->where(['refund_no' => $refund_no, 'm_id' => $m_id])->first();
        if (!$refund) {
            api_error(__('api.refund_error'));
        }
        $goods = OrderGoods::select('goods_title', 'image', 'spec_value')->where('id', $refund['order_goods_id'])->first();
        $return = array(
            'status' => $refund['status'],
            'status_text' => Refund::STATUS_DESC[$refund['status']],
            'refund_type' => Refund::REFUND_TYPE_DESC[$refund['refund_type']],
            'reason' => Refund::REASON_DESC[$refund['refund_type']][$refund['reason']],
            'amount' => $refund['amount'],
            'delivery_price' => $refund['delivery_price'],
            'refund_no' => $refund['refund_no'],
            'create_at' => $refund['create_at'],
            'goods' => $goods
        );
        return $this->success($return);
    }

    /**
     * 退款日志
     * @param Request $request
     */
    public function log(Request $request)
    {
        $m_id = $this->getUserId();
        $refund_no = (int)$request->post('refund_no');
        if (!$refund_no) {
            api_error(__('api.missing_params'));
        }
        $refund = Refund::where(['refund_no' => $refund_no, 'm_id' => $m_id])->first();
        if (!$refund) {
            api_error(__('api.refund_error'));
        }
        $log_res = RefundLog::select('id', 'user_type', 'username', 'action', 'note', 'created_at as create_at')->where('refund_id', $refund['id'])->orderBy('id', 'desc')->get();
        if ($log_res->isEmpty()) {
            api_error(__('api.admin.content_is_empty'));
        }
        $log_ids = array();
        foreach ($log_res as $value) {
            $log_ids[] = $value['id'];
        }
        if ($log_ids) {
            $refund_image = array();
            $refund_image_res = RefundImage::whereIn('log_id', $log_ids)->get();
            if (!$refund_image_res->isEmpty()) {
                foreach ($refund_image_res as $value) {
                    $refund_image[$value['log_id']][] = $value['image'];
                }
            }
        }
        $log = array();
        foreach ($log_res as $value) {
            $_item = $value;
            $_item['user_type'] = RefundLog::USER_TYPE_DESC[$value['user_type']];
            if ($value['note']) {
                $_item['note'] = json_decode($value['note'], true);
            }
            $_item['image'] = isset($refund_image[$value['id']]) ? $refund_image[$value['id']] : [];
            $log[] = $_item;
        }
        return $this->success($log);
    }

    /**
     * 退款申请
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function apply(Request $request)
    {
        $m_id = $this->getUserId();
        $is_update = (int)$request->post('is_update');
        $order_goods_id = (int)$request->post('order_goods_id');
        if (!$order_goods_id) {
            api_error(__('api.missing_params'));
        }

        list($order_goods, $order, $refund, $max_amount, $delivery_price) = $this->checkRefund($order_goods_id, $is_update);

        $return = array(
            'goods' => array(
                'goods_title' => $order_goods['goods_title'],
                'image' => $order_goods['image'],
                'buy_qty' => $order_goods['buy_qty'],
                'spec_value' => $order_goods['spec_value'],
            ),
            'amount' => 0,
            'max_amount' => $max_amount + $delivery_price,
            'delivery_price' => $delivery_price,
            'refund_type' => 0,
            'reason' => 0,
            'note' => '',
            'image' => [],
            'reason_data' => Refund::REASON_DESC
        );

        //修改时回填已经申请的信息
        if ($refund) {
            $return['amount'] = $refund['amount'];
            $return['refund_type'] = $refund['refund_type'];
            $return['reason'] = $refund['reason'];
            //查询最后一次的日志
            $last_refund_log = RefundLog::where(['refund_id' => $refund['id'], 'user_type' => RefundLog::USER_TYPE_MEMBER])->orderBy('id', 'desc')->first();
            if ($last_refund_log) {
                $return['note'] = $last_refund_log['note'];
                $image = RefundImage::where('log_id', $last_refund_log['id'])->pluck('image');
                if ($image) {
                    $return['image'] = $image;
                }
            }
        }
        return $this->success($return);
    }

    /**
     * 退款申请提交或修改
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function applyPut(Request $request)
    {
        $m_id = $this->getUserId();
        $userinfo = $this->getUserInfo();
        $is_update = (int)$request->post('is_update');
        $order_goods_id = (int)$request->post('order_goods_id');
        $refund_type = (int)$request->post('refund_type');
        $reason = (int)$request->post('reason');
        $note = $request->post('note');
        $image = $request->post('image');
        $amount = $request->post('amount');
        if (!$order_goods_id || !$refund_type || !$reason || !check_price($amount) || !isset(Refund::REASON_DESC[$refund_type][$reason])) {
            api_error(__('api.missing_params'));
        }

        list($order_goods, $order, $refund, $max_amount, $delivery_price) = $this->checkRefund($order_goods_id, $is_update);

        if ($amount > $max_amount) {
            api_error(__('api.refund_amount_error'));
        }

        $refund_data = array(
            'order_id' => $order['id'],
            'order_goods_id' => $order_goods_id,
            'payment_id' => $order['payment_id'],
            'seller_id' => $order['seller_id'],
            'm_id' => $m_id,
            'amount' => $amount,
            'max_amount' => $max_amount,
            'delivery_price' => $delivery_price,
            'refund_type' => $refund_type,
            'reason' => $reason,
            'status' => Refund::STATUS_WAIT_APPROVE,
        );

        $log_note = array(
            [
                'title' => '退款类型',
                'info' => Refund::REFUND_TYPE_DESC[$refund_type]
            ],
            [
                'title' => '退款金额',
                'info' => '￥' . $amount
            ],
            [
                'title' => '退款原因',
                'info' => Refund::REASON_DESC[$refund_type][$reason]
            ]
        );
        if ($note) {
            $log_note[] = ['title' => '备注', 'info' => $note];
        }

        //退款日志信息
        $refund_data['log'] = array(
            'user_type' => RefundLog::USER_TYPE_MEMBER,
            'user_id' => $m_id,
            'username' => $userinfo['username'],
            'action' => '修改售后申请',
            'note' => json_encode($log_note, JSON_UNESCAPED_UNICODE),
            'image' => $image ? explode(',', $image) : []
        );

        //新增的时候
        if ($is_update != 1) {
            $log['action'] = '提交售后申请';
            $refund_data['refund_no'] = date('YmdHis', time()) . rand(100000, 999999);
        }
        $res = Refund::saveData($refund['id'], $refund_data);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 验证退款信息
     * @param $order_goods_id 订单商品id
     * @param int $is_update 是否修改1是
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    private function checkRefund($order_goods_id, $is_update = 0)
    {
        $m_id = $this->getUserId();
        //获取订单商品信息
        $order_goods = OrderGoods::where(['id' => $order_goods_id, 'm_id' => $m_id])->first();
        if (!$order_goods) {
            api_error(__('api.order_goods_error'));
        }
        //获取订单信息
        $order = Order::where('id', $order_goods['order_id'])->first();

        //只有已支付、待收货、待评价、已评价的可以申请
        if (!in_array($order['status'], [Order::STATUS_PAID, Order::STATUS_SHIPMENT, Order::STATUS_DONE, Order::STATUS_COMMENT])) {
            api_error(__('api.refund_time_out'));
        }

        //查询是否已经申请
        $refund = Refund::where('order_goods_id', $order_goods_id)->first();
        //已经申请不能再次申请
        if ($refund && $is_update != 1) {
            api_error(__('api.refund_wait_audit'));
        }
        if (!$refund && $is_update == 1) {
            api_error(__('api.invalid_params'));
        }
        //已经售后完成
        if ($refund['status'] == Refund::STATUS_DONE) {
            api_error(__('api.refund_complete'));
        }
        //在等待寄回商品、寄回商品、待退款的时候不允许修改
        if (in_array($refund['status'], [Refund::STATUS_WAIT_DELIVERY, Refund::STATUS_RECEIVED, Refund::STATUS_WAIT_PAY])) {
            api_error(__('api.refund_wait_audit'));
        }

        $all_refund = 0;//是否全部退款，如果是最后一个需要加上运费
        //查询订单下没有售后的商品
        $wiat_refund_id = OrderGoods::where('order_id', $order['id'])->whereIn('refund', [OrderGoods::REFUND_NO, OrderGoods::REFUND_CLOSE])->pluck('id')->toArray();
        if (count($wiat_refund_id) == 1 && $wiat_refund_id[0] == $order_goods_id) {
            $all_refund = 1;
        }

        //获取最大退款价格
        $delivery_price = 0;
        $max_amount = $order_goods['sell_price'] * $order_goods['buy_qty'] - $order_goods['promotion_price'];
        if ($all_refund == 1) {
            $delivery_price = $order_goods['delivery_price_real'];
        }
        return [$order_goods, $order, $refund, $max_amount, $delivery_price];
    }

    /**
     * 退货物流
     * @param Request $request
     */
    public function delivery(Request $request)
    {
        $m_id = $this->getUserId();
        $userinfo = $this->getUserInfo();
        $refund_no = (int)$request->post('refund_no');
        $company_id = (int)$request->post('company_id');
        $code = $request->post('code');
        $note = $request->post('note');
        if (!$refund_no || !$company_id || !$code) {
            api_error(__('api.missing_params'));
        }

        $refund = Refund::where(['refund_no' => $refund_no, 'm_id' => $m_id])->first();
        if (!$refund) {
            api_error(__('api.refund_error'));
        } elseif ($refund['status'] != Refund::STATUS_WAIT_DELIVERY) {
            api_error(__('api.refund_status_error'));
        }
        $express_company = ExpressCompany::select('title', 'code')->where('id', $company_id)->first();
        if (!$express_company) {
            return __('api.express_company_error');
        }
        $log_note = array(
            [
                'title' => '物流公司',
                'info' => $express_company['title']
            ],
            [
                'title' => '物流单号',
                'info' => $code
            ]
        );
        if ($note) {
            $log_note[] = ['title' => '备注', 'info' => $note];
        }

        //退款日志信息
        $log = array(
            'refund_id' => $refund['id'],
            'user_type' => RefundLog::USER_TYPE_MEMBER,
            'user_id' => $m_id,
            'username' => $userinfo['username'],
            'action' => '退回商品',
            'note' => json_encode($log_note, JSON_UNESCAPED_UNICODE)
        );
        $delivery_data = array(
            'refund_id' => $refund['id'],
            'company_code' => $express_company['code'],
            'company_name' => $express_company['title'],
            'code' => $code,
            'log' => $log
        );
        $res = RefundDelivery::saveData($delivery_data);
        if ($res) {
            //订阅物流消息
            $kdniao = New Kdniao();
            $kdniao->subscribe($express_company['code'], $code);
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 售后取消
     * @param Request $request
     */
    public function cancel(Request $request)
    {
        $m_id = $this->getUserId();
        $userinfo = $this->getUserInfo();
        $refund_no = (int)$request->post('refund_no');
        $note = $request->post('note');
        if (!$refund_no) {
            api_error(__('api.missing_params'));
        }

        $refund = Refund::where(['refund_no' => $refund_no, 'm_id' => $m_id])->first();
        if (!$refund) {
            api_error(__('api.refund_error'));
        }

        if (!in_array($refund['status'], [Refund::STATUS_WAIT_APPROVE, Refund::STATUS_WAIT_DELIVERY, Refund::STATUS_REFUSED_RECEIVED])) {
            api_error(__('api.refund_status_error'));
        }
        $log_note = array();
        if ($note) {
            $log_note[] = ['title' => '备注', 'info' => $note];
        }

        //退款日志信息
        $log = array(
            'refund_id' => $refund['id'],
            'user_type' => RefundLog::USER_TYPE_MEMBER,
            'user_id' => $m_id,
            'username' => $userinfo['username'],
            'action' => '取消售后',
            'note' => json_encode($log_note, JSON_UNESCAPED_UNICODE)
        );
        $res = Refund::cancel($refund['id'], $refund['order_goods_id'], $log);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }
}
