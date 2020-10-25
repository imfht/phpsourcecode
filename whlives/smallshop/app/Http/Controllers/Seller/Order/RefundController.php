<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\Order;

use App\Http\Controllers\Seller\BaseController;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Refund;
use App\Models\RefundImage;
use App\Models\RefundLog;
use App\Models\SellerAddress;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Validator;

/**
 * 售后
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class RefundController extends BaseController
{
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
        //搜索
        $where = array();
        $where[] = array('seller_id', $seller_id);
        $refund_no = $request->input('refund_no');
        $username = $request->input('username');
        if ($refund_no) $where[] = array('refund_no', $refund_no);
        if ($username) {
            $member_id = Member::where('username', $username)->value('id');
            if ($member_id) {
                $where[] = array('m_id', $member_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }

        $res_list = Refund::select('id', 'm_id', 'refund_no', 'amount', 'refund_type', 'reason', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        //查询用户
        $m_ids = array();
        foreach ($res_list as $value) {
            $m_ids[] = $value['m_id'];
        }
        if ($m_ids) {
            $member_data = Member::whereIn('id', array_unique($m_ids))->pluck('username', 'id');
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['username'] = isset($member_data[$value['m_id']]) ? $member_data[$value['m_id']] : '';
            $_item['refund_type_text'] = Refund::REFUND_TYPE_DESC[$_item['refund_type']];
            $_item['status_text'] = Refund::STATUS_DESC[$_item['status']];
            $data_list[] = $_item;
        }
        $total = Refund::where($where)->count();
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
        $data = array();
        if ($id) {
            $refund = Refund::where(['id' => $id, 'seller_id' => $seller_id])->first();
            if (!$refund) {
                api_error(__('admin.content_is_empty'));
            }
            $refund['status_text'] = Refund::STATUS_DESC[$refund['status']];
            $refund['refund_type_text'] = Refund::REFUND_TYPE_DESC[$refund['refund_type']];
            $log = array();
            $log_res = RefundLog::select('id', 'username', 'user_type', 'action', 'note', 'created_at')
                ->where('refund_id', $id)
                ->orderBy('id', 'desc')
                ->get();
            if (!$log_res->isEmpty()) {
                $log_ids = $log_image = array();
                foreach ($log_res as $val) {
                    $log_ids[] = $val['id'];
                }
                if ($log_ids) {
                    $image_res = RefundImage::whereIn('log_id', array_unique($log_ids))->orderBy('id', 'desc')->get();
                    if (!$image_res->isEmpty()) {
                        foreach ($image_res as $val) {
                            $log_image[$val['log_id']][] = $val['image'];
                        }
                    }
                }
                foreach ($log_res as $value) {
                    $_item = $value;
                    $_item['image'] = isset($log_image[$value['id']]) ? $log_image[$value['id']] : [];
                    $_item['user_type'] = RefundLog::USER_TYPE_DESC[$value['user_type']];
                    if ($value['note']) {
                        $_item['note'] = json_decode($value['note'], true);
                    }
                    $log[] = $_item;
                }
            }
            $order = Order::select('order_no')->find($refund['order_id']);
            $order_goods = OrderGoods::select('goods_title')->find($refund['order_goods_id']);
            $address = array();
            $address = SellerAddress::select('id', 'full_name', 'tel', 'prov_name', 'city_name', 'area_name', 'address')->where('seller_id', $refund['seller_id'])->orderBy('default', 'desc')->get();
            $data = array(
                'refund' => $refund,
                'log' => $log,
                'order' => $order,
                'order_goods' => $order_goods,
                'address' => $address
            );
        }

        return $this->success($data);
    }

    /**
     * 操作售后
     * @param Request $request
     * @return array
     */
    public function actionSave(Request $request)
    {
        $seller_id = $this->getUserId();
        $id = (int)$request->input('id');
        $type = $request->input('type');
        $note = $request->input('note');
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'type' => 'required',
        ], [
            'id.required' => 'id错误',
            'id.unique' => 'id错误',
            'type.required' => '类型不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $refund = Refund::where(['id' => $id, 'seller_id' => $seller_id])->first();
        if (!$refund) {
            api_error(__('admin.invalid_params'));
        }

        if ($type == 'approve') {
            //同意操作
            $res_data = $this->approve($refund);
        } else if ($type == 'refused') {
            //拒绝操作
            $res_data = $this->refused($refund);
        }
        if (isset($res_data['refund']['status']) && $res_data['refund']['status']) {
            $log_note = array();
            //同意退货的时候需要收货地址
            if (isset($res_data['refund']['status']) && $res_data['refund']['status'] == Refund::STATUS_WAIT_DELIVERY) {
                $address_id = (int)$request->input('address_id');
                if (!$address_id) {
                    api_error(__('admin.refund_address_error'));
                }
                $address = SellerAddress::where(['id' => $address_id, 'seller_id' => $refund['seller_id']])->first();
                if (!$address) {
                    api_error(__('admin.refund_address_error'));
                }
                $log_note[] = ['title' => '收货人', 'info' => $address['full_name']];
                $log_note[] = ['title' => '电话', 'info' => $address['tel']];
                $log_note[] = ['title' => '地址', 'info' => $address['prov_name'] . $address['city_name'] . $address['area_name'] . $address['address']];
            }
            if ($note) {
                $log_note[] = ['title' => '备注', 'info' => $note];
            }

            //添加日志
            $admin_user = $this->getUserInfo();
            $log = array(
                'refund_id' => $id,
                'user_type' => RefundLog::USER_TYPE_ADMIN,
                'user_id' => $admin_user['id'],
                'username' => $admin_user['username'],
                'action' => $res_data['action'],
                'note' => json_encode($log_note, JSON_UNESCAPED_UNICODE)
            );
            RefundLog::create($log);
            //开始修改订单商品状态
            if ($refund['status'] == Refund::STATUS_WAIT_PAY && $res_data['refund']['status'] == Refund::STATUS_DONE) {
                //开始退款原路退回操作
                $refund_amount_res = OrderService::refund($refund['id'], $refund['amount']);
                if ($refund_amount_res === true) {
                    $res = true;
                } else {
                    api_error($refund_amount_res);
                }
            } elseif ($res_data['goods_status']) {
                $res = Refund::where('id', $id)->update($res_data['refund']);
                OrderGoods::where('id', $refund['order_goods_id'])->update(['refund' => $res_data['goods_status']]);
            }
            if ($res) {
                return $this->success();
            } else {
                api_error(__('admin.fail'));
            }
        } else {
            api_error(__('admin.refund_status_error'));
        }
    }


    /**
     * 同意申请
     * @param $refund 售后单信息
     */
    private function approve($refund)
    {
        $return = array(
            'refund' => [
                'status' => '',
            ],
            'goods_status' => OrderGoods::REFUND_NO,//订单商品状态
            'action' => ''
        );
        switch ($refund['refund_type']) {
            case Refund::REFUND_TYPE_MONEY://仅退款
                switch ($refund['status']) {
                    case Refund::STATUS_WAIT_APPROVE:
                        $return['action'] = '卖家同意退款';
                        $return['goods_status'] = OrderGoods::REFUND_ONGOING;
                        $return['refund']['status'] = Refund::STATUS_WAIT_PAY;
                        $return['refund']['approve_at'] = get_date();
                        break;
                    case Refund::STATUS_WAIT_PAY:
                        $return['action'] = '退款完成';
                        $return['goods_status'] = OrderGoods::REFUND_DONE;
                        $return['refund']['status'] = Refund::STATUS_DONE;
                        $return['refund']['done_at'] = get_date();
                        break;
                }
                break;
            case Refund::REFUND_TYPE_GOODS://退货退款
                switch ($refund['status']) {
                    case Refund::STATUS_WAIT_APPROVE:
                        $return['action'] = '卖家同意退货';
                        $return['goods_status'] = OrderGoods::REFUND_ONGOING;
                        $return['refund']['status'] = Refund::STATUS_WAIT_DELIVERY;
                        $return['refund']['approve_at'] = get_date();
                        break;
                    case Refund::STATUS_RECEIVED:
                        $return['action'] = '卖家已经确认收货';
                        $return['goods_status'] = OrderGoods::REFUND_ONGOING;
                        $return['refund']['status'] = Refund::STATUS_WAIT_PAY;
                        $return['refund']['received_at'] = get_date();
                        break;
                    case Refund::STATUS_WAIT_PAY:
                        $return['action'] = '退款完成';
                        $return['goods_status'] = OrderGoods::REFUND_DONE;
                        $return['refund']['status'] = Refund::STATUS_DONE;
                        $return['refund']['done_at'] = get_date();
                        break;
                }
                break;
            case Refund::REFUND_TYPE_REPLACE://换货
                switch ($refund['status']) {
                    case Refund::STATUS_WAIT_APPROVE:
                        $return['action'] = '卖家同意换货';
                        $return['goods_status'] = OrderGoods::REFUND_ONGOING;
                        $return['refund']['status'] = Refund::STATUS_WAIT_DELIVERY;
                        $return['refund']['approve_at'] = get_date();
                        break;
                    case Refund::REFUND_TYPE_REPLACE:
                        $return['action'] = '卖家已经确认收货';
                        $return['goods_status'] = OrderGoods::REFUND_ONGOING;
                        $return['refund']['status'] = Refund::STATUS_WAIT_SELLER_DELIVERY;
                        $return['refund']['received_at'] = get_date();
                        break;
                    case Refund::STATUS_WAIT_SELLER_DELIVERY:
                        $return['action'] = '卖家已经从新发货';
                        $return['goods_status'] = OrderGoods::REFUND_REPLACE_DONE;
                        $return['refund']['status'] = Refund::STATUS_DONE;
                        $return['refund']['done_at'] = get_date();
                        break;
                }
                break;
        }
        return $return;
    }

    /**
     * 拒绝申请
     * @param $refund 售后单信息
     */
    private function refused($refund)
    {
        $return = array(
            'refund' => [
                'status' => '',
                'refused_at' => get_date()
            ],
            'goods_status' => OrderGoods::REFUND_ONGOING,
            'action' => ''
        );
        switch ($refund['refund_type']) {
            case Refund::REFUND_TYPE_MONEY://仅退款
                if ($refund['status'] == Refund::STATUS_WAIT_APPROVE) {
                    $return['action'] = '卖家拒绝退款';
                    $return['refund']['status'] = Refund::STATUS_REFUSED_APPROVE;
                }
                break;
            case Refund::REFUND_TYPE_GOODS://退货退款
                switch ($refund['status']) {
                    case Refund::STATUS_WAIT_APPROVE:
                        $return['action'] = '卖家拒绝退货';
                        $return['refund']['status'] = Refund::STATUS_REFUSED_APPROVE;
                        break;
                    case Refund::STATUS_RECEIVED:
                        $return['action'] = '卖家拒绝收货';
                        $return['refund']['status'] = Refund::STATUS_REFUSED_RECEIVED;
                        break;
                }
                break;
            case Refund::REFUND_TYPE_REPLACE://换货
                switch ($refund['status']) {
                    case Refund::STATUS_WAIT_APPROVE:
                        $return['action'] = '卖家拒绝换货';
                        $return['refund']['status'] = Refund::STATUS_REFUSED_APPROVE;
                        break;
                    case Refund::STATUS_RECEIVED:
                        $return['action'] = '卖家拒绝收货';
                        $return['refund']['status'] = Refund::STATUS_REFUSED_RECEIVED;
                        break;
                }
                break;
        }
        return $return;
    }
}