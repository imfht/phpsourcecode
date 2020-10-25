<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\Balance;
use App\Models\BalanceDetail;
use App\Models\BalanceRecharge;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class BalanceController extends BaseController
{

    /**
     * 余额明细列表
     * @param Request $request
     */
    public function detailList(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $event = (int)$request->post('event');
        $where = [
            'm_id' => $m_id
        ];
        if ($event) {
            $where['event'] = $event;
        }
        $res_list = BalanceDetail::select('id', 'type', 'event', 'detail_no', 'amount', 'created_at as create_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['event'] = BalanceDetail::EVENT_DESC[$value['event']];
            $_item['amount'] = ($value['type'] == BalanceDetail::TYPE_INCR ? '+' : '-') . $value['amount'];
            $_item['create_at'] = substr($value['create_at'], 0, 10);
            unset($_item['type']);
            $data_list[] = $_item;
        }
        $total = BalanceDetail::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 余额明细
     * @param Request $request
     */
    public function detail(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->post('id');
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        $where = [
            'm_id' => $m_id,
            'id' => $id
        ];
        $res_list = BalanceDetail::select('event', 'detail_no', 'amount', 'balance', 'note', 'created_at as create_at')->where($where)->first();
        if (!$res_list) {
            api_error(__('api.content_is_empty'));
        }

        $res_list['event'] = BalanceDetail::EVENT_DESC[$res_list['event']];
        return $this->success($res_list);
    }

    /**
     * 余额充值
     * param $amount 金额
     */
    public function recharge(Request $request)
    {
        $m_id = $this->getUserId();
        $amount = $request->post('amount');
        if (!$amount || !check_price($amount)) {
            api_error(__('api.missing_params'));
        }
        $recharge_no = BalanceRecharge::getRechargeNo();
        $create_data = [
            'm_id' => $m_id,
            'recharge_no' => $recharge_no,
            'amount' => $amount,
        ];
        $res = BalanceRecharge::create($create_data);
        if ($res) {
            $return = ['recharge_no' => $recharge_no];
            return $this->success($return);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 余额提现
     * param $amount 提现金额
     * param $pay_name 支付宝姓名
     * param $pau_number 支付宝账号
     */
    public function withdraw(Request $request)
    {
        $m_id = $this->getUserId();
        $type = (int)$request->post('type');
        $amount = $request->post('amount');
        $name = $request->post('name');
        $bank_name = $request->post('bank_name');
        $pay_number = $request->post('pay_number');
        if (!$amount || !check_price($amount) || !$name || !$pay_number) {
            api_error(__('api.missing_params'));
        }
        if (!isset(Withdraw::TYPE_DESC[$type])) {
            api_error(__('api.invalid_params'));
        }
        if ($type == Withdraw::TYPE_BANK && !$bank_name) {
            api_error(__('api.missing_params'));
        }

        //获取姓名如果需要开启实名认证这里就需要加上姓名
        $note = '用户提现【' . $amount . '】';
        $res = Balance::updateAmount($m_id, '-' . $amount, BalanceDetail::EVENT_WITHDRAW, '', $note);
        if ($res['status']) {
            $create_data = [
                'm_id' => $m_id,
                'type' => $type,
                'amount' => $amount,
                'name' => $name,
                'bank_name' => $bank_name,
                'pay_number' => $pay_number,
            ];
            $add = Withdraw::create($create_data);
            if ($add) {
                return $this->success(true);
            } else {
                api_error(__('api.fail'));
            }
        } else {
            api_error($res['message']);
        }
    }

    /**
     * 提现明细
     */
    public function withdrawList(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'm_id' => $m_id
        ];
        $res_list = Withdraw::select('id', 'type', 'amount', 'status', 'created_at as create_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['type'] = Withdraw::TYPE_DESC[$value['type']];
            $_item['status'] = Withdraw::STATUS_DESC[$value['status']];
            $_item['create_at'] = substr($value['create_at'], 0, 10);
            $data_list[] = $_item;
        }
        $total = Withdraw::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }
}
