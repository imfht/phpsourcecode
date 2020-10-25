<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Balance;
use App\Models\BalanceDetail;
use App\Models\Member;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Validator;

/**
 * 提现
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class WithdrawController extends BaseController
{
    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        list($page, $limit, $offset) = get_page_params();
        $status = $request->input('status');
        $username = $request->input('username');

        //搜索
        $where = array();
        if (is_numeric($status)) $where[] = array('status', $status);
        if ($username) {
            $member_id = Member::where('username', $username)->value('id');
            if ($member_id) {
                $where[] = array('m_id', $member_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }
        $res_list = Withdraw::select('id', 'm_id', 'type', 'amount', 'name', 'bank_name', 'pay_number', 'note', 'status', 'created_at', 'done_at')
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
            $_item['type'] = Withdraw::TYPE_DESC[$value['type']];
            $_item['status_text'] = Withdraw::STATUS_DESC[$value['status']];
            $data_list[] = $_item;
        }
        $total = Withdraw::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 获取状态组
     */
    public function getStatus()
    {
        return $this->success(Withdraw::STATUS_DESC);
    }

    /**
     * 同意退款
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function agreed(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => 'id错误',
            'id.numeric' => 'id错误',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        $data = array(
            'status' => Withdraw::STATUS_ON,
            'done_at' => get_date()
        );
        $res = Withdraw::where([['id', $id], ['status', Withdraw::STATUS_OFF]])->update($data);
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 拒绝并退还资金
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function refusedMoney(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'note' => 'required'
        ], [
            'id.required' => 'id错误',
            'id.numeric' => 'id错误',
            'note.required' => '备注不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        $note = $request->input('note');
        $data = array(
            'status' => Withdraw::STATUS_REFUND,
            'note' => $note,
            'done_at' => get_date()
        );
        $res = Withdraw::where([['id', $id], ['status', Withdraw::STATUS_OFF]])->update($data);
        if ($res) {
            //退款资金
            $withdraw = Withdraw::find($id);
            Balance::updateAmount($withdraw['m_id'], $withdraw['amount'], BalanceDetail::EVENT_WITHDRAW_REFUND, $withdraw['id'], '提现拒绝退还');
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 拒绝不退还资金
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function refusedNoMoney(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'note' => 'required'
        ], [
            'id.required' => 'id错误',
            'id.numeric' => 'id错误',
            'note.required' => '备注不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        $note = $request->input('note');
        $data = array(
            'status' => Withdraw::STATUS_DEDUCT,
            'note' => $note,
            'done_at' => get_date()
        );
        $res = Withdraw::where([['id', $id], ['status', Withdraw::STATUS_OFF]])->update($data);
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }
}